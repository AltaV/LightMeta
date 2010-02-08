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
    * Lime types
    * Define types by (id, type-name, label)
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
    * Lime Type container formats
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
    * Lime Type query
    * select all types from lime_types
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
    * Lime Entity
    * extends Nuclear Entity
    * contains type|id|name
    *
    */

    class LimeEntity extends Entity
    {
        protected static $_instance;
        protected $type_id;

        function __construct( $type, $type_id, $id, $name )
        {
            parent::__construct( $type, $id, $name );
            $this->type_id = $type_id;
            self::$_instance = $this;
        }

        function __get( $f )
        {
            if( $r = parent::__get($f) )
                return $r;

            if( $f == "type_id" )
                return $this->type_id;
            
            return null;
        }

        public static function getInstance()
        {
            return self::$_instance;
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
        protected static $_instance;

        function __construct( $object )
        {
            parent::__construct(
                $object->type, 
                $object->type_id, 
                $object->id, 
                $object->name );

            self::$_instance = $this;
        }

        public static function getInstance()
        {
            return self::$_instance;
        }
    }

?>
