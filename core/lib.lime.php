<?php
    
    /*

      LightMeta Library
      =================================
      altmanryan@gmail.com - Winter 2010
      
      The Lime library contains basic containers 
      and queries which fetch and hold type/id
      info.

    */


    /*
     * 
     * Lime types
     * Define types by (id, type-name, label)
     * 
    */

    class LimeTypes implements iSingleton
    {
        protected static $_instance;
        protected $types;

        function __construct()
        {
            $this->types = array();
            self::$_instance = $this;
        }

        public static function getInstance()
        {
            if( is_null(self::$_instance) )
                self::$_instance = new LimeTypes();
            return self::$_instance;
        }

        public function append( $type )
        {
            $this->types[] = $type;
        }

        public function id( $name )
        {
            foreach( $this->types as $type )
            {
                if( $type['type'] == $name )
                    return $type['id'];
            }

            return null;
        }

        public function type( $id )
        {
            foreach( $this->types as $type )
            {
                if( $type['id'] == $id )
                    return $type['type'];
            }

            return null;
        }
    }

    /*
     * 
     * Lime Type container formats
     * 
    */

    class LimeTypesJSON extends LimeTypes
    {
        function __toString()
        {
            return json_encode( $this->types );
        }
    }

    class LimeTypesXML extends XMLResponse
    {
        function __construct( $query )
        {
            parent::__construct();
            
            while( $type = $query->hash() )
            {
                $type_node = $this->attach("type");
                $type_node->appendChild( $this->attach("id",$type['id']) );
                $type_node->appendChild( $this->attach("name",$type['type']) );
                $type_node->appendChild( $this->attach("label",$type['label']) );
                $this->append( $type_node );
            }
        }
    }


    /*
     * 
     * Lime Type query
     * select all types from lime_types
     * 
    */

    class LimeTypesQuery extends NuSelect
    {
        function __construct()
        {
            parent::__construct("lime_types T");
            $this->order("T.id");
        }
    }
    
    
    /*
     * 
     * Lime Resolution select
     * select entity information
     * 
    */

    class LimeResolveSelect extends NuSelect
    {
        function __construct( $type, $name, $parent_id=0 )
        {
            parent::__construct( "lime_resolution R" );
            
            // join labels
            $this->join( "lime_label L", "L.id=R.label", "right" );
            
            // condition on Label matching name
            $this->where( "L.type={$type}" );
            $this->where( "L.label='". safe_slash($name) ."'" );
            $this->where( "R.parent={$parent_id}" );
                        
            // perhaps join extra information
            NuEvent::action( 'lime_resolve_select', $this );

            // primary identification of entity
            $this->field( "R.entity as id" );
        }
    }
    
    
    
    /*
     * 
     * Lime Label
     * greedy container // auto-creates
     * 
    */
    
    class LimeLabel
    {
        protected $id;
        protected $type;
        protected $label;
        
        function __construct( $type, $label )
        {
            $this->type  = $type;
            $this->label = $label;
        }
        
        function id( $create = true )
        {
            if( is_null($this->id) )
            {
                $select = new NuSelect("lime_label L");
                $select->field( "L.id" );
                $select->where( "L.type={$this->type}" );
                $select->where( "L.label='". safe_slash( $this->label ) ."'" );
                
                if( $data = $select->single() )
                {
                    $this->id = $data[0];
                }
                else if( $create )
                {
                    require_once('class.nuinsert.php');
                    $insert = new NuInsert("lime_label");
                    $insert->field( "type,label" );
                    $insert->value( array($this->type, "'". safe_slash( $this->label ) ."'") );
                    $id = $insert->id();
                    
                    $this->id = $id;
                }
            }
            
            return $this->id;
        }
    }



    /*
     * 
     * Lime Type
     * greedy container // auto-creates
     * 
    */
    
    class LimeType
    {
        protected $id;
        protected $type;
        
        function __construct( $type )
        {
            $this->type  = $type;
        }
        
        function id( $create = true )
        {
            if( is_null($this->id) )
            {
                $select = new NuSelect("lime_dynamic_types T");
                $select->field( "T.id" );
                $select->where( "T.type='{$this->type}'" );
                
                if( $data = $select->single() )
                {
                    $this->id = $data[0];
                }
                else if( $create )
                {
                    require_once('class.nuinsert.php');
                    $insert = new NuInsert("lime_dynamic_types T");
                    $insert->field( "type" );
                    $insert->value( array("'". safe_slash( $this->type ) ."'") );
                    $id = $insert->id();
                    
                    $this->id = $id;
                }
            }
            
            return $this->id;
        }
    }
    
    

    /*
     *
     * Lime Entity
     * extends Nuclear Entity
     *
    */

    class LimeEntity extends Entity
    {
        protected   $parent_entity; // (LimeEntity)
        protected   $type_id;       // (int)
        protected   $image_id;      // (int)

        function __construct( $type, $type_id, $id, $name, $parent_entity=null )
        {
            parent::__construct( $type, $id, $name );
            
            $this->type_id          = $type_id;
            $this->parent_entity    = $parent_entity;
        }

        function __get( $f )
        {
            if( $r = parent::__get($f) )
                return $r;

            switch( $f )
            {
                case 'parent':
                    return $this->parent_entity;
                    
                case 'type_id':
                    return $this->type_id;

                case 'image_id':
                    return $this->image_id;
            }
            
            return null;
        }
                
        
        //
        // Resolve
        // expects unresolved Object
        //
        public static function resolve( $entity, $create=false )
        {
            // entity(type, type_id, name, parent=>entity)
            $type       = $entity->type;
            $name       = $entity->name;            
            $type_id    = $entity->type_id;
            
            if( is_null($entity->type_id) )
            {
                $lime_type  = new LimeType( $type );
                $type_id    = $lime_type->id( $create );
            }
            
            if( !$type_id )
                throw new Exception("LimeEntity type_id could not be resolved.");
            
            // test for parent
            if( !is_null($entity->parent) )
            {
                // rescurse for parent
                $parent     = LimeEntity::resolve( $entity->parent, $create );
                
                if( is_null($parent) )
                {
                    if( $create === true )
                    {
                        $parent = LimeEntity::create( $entity );
                    }
                    else
                    {
                        throw new Exception("LimeEntity parent could not be resolved.");
                    }
                }
                
                $parent_id  = $parent->id;
            }
            else
            {
                $parent     = null;
                $parent_id  = 0;
            }
            
            // create selection object
            $select     = new LimeResolveSelect( $type_id, $name, $parent_id );
            
            if( $select->select() && ($data = $select->object()) )
            {
                // create lime object
                $lime   = new LimeEntity( $type, $type_id, -1, $name, $parent );
                
                // acquired entity, assign data
                foreach( $data as $k=>$v )
                {
                    if( !is_numeric($k) && is_null($lime->$k) )
                        $lime->$k = $v;
                }
                
                // return entity object
                return $lime;
            }
            
            // does not exist
            if( $create === true )
            {
                LimeEntity::create( $entity );
            }
        }
        
        
        //
        // Create
        //
        public static function create( $entity )
        {
            $type       = $entity->type;
            $name       = $entity->name;
            $type_id    = $entity->type_id;

            if( is_null($entity->type_id) )
            {
                $lime_type  = new LimeType( $type );
                $type_id    = $lime_type->id( true );
            }

            if( !is_null($entity->parent) )
            {
                // rescurse for parent
                $parent     = LimeEntity::resolve( $entity->parent, true );
                
                if( is_null($parent) )
                    throw new Exception("LimeEntity parent could not be created.");
                
                $parent_id  = $parent->id;
            }
            else
            {
                $parent     = null;
                $parent_id  = 0;
            }
            
            //
            // create Entity
            require_once('class.nuinsert.php');
            
            $insert = new NuInsert("lime_entity");
            $insert->field("type,label,guid");
            $insert->values( array($type_id, "'". safe_slash($name) ."'", 'UUID()') );
            
            // entity id
            $entity_id     = $insert->id();
            
            // label id
            $label          = new LimeLabel( $type_id, $name );
            $label_id       = $label->id();
            
            // insert
            $insert = new NuInsert("lime_resolution");
            $insert->field("label,parent,entity");
            $insert->values( array( $label_id, $parent_id, $entity_id ) );
            
            // check affected, return resolve
            if( $insert->affected()>0 )
                return LimeEntity::resolve( $entity );
            
            return null;
        }

    }


    
    /*
     *
     * Lime Focus
     * the active Lime entity of any request
     *
    */

    class LimeFocus extends LimeEntity implements iSingleton
    {
        private static $_instance;  // LimeEntity

        function __construct( $object )
        {
            parent::__construct(
                $object->type, 
                $object->type_id, 
                $object->id, 
                $object->name );

            self::$_instance = $this;
            
            $this->image_id  = $object->image;
        }

        public static function getInstance()
        {
            return self::$_instance;
        }
    }

?>
