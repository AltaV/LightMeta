<?php

    /*

      LightMeta Version
      ================================
      altman,ryan - 2010 Winter

      Page version maintenance

    */

    $defines = array(
        'NEW', 
        'APPROVED', 
        'ACTIVE', 
        'PENDING', 
        'DENIED', 
        'DELETED'
    );

    foreach( $defines as $v=>$n )
    {
        define("LIME_STATUS_{$n}",$v+1);
    }


    class LimeVersion
    {
        protected $lime_object;
        protected $owner_object;
        protected $page_id;
        protected $page_object;

        //
        // constructor
        //
        function __construct( $lime, $owner )
        {
            $this->lime_object  = $lime;
            $this->owner_object = $owner;

            // get page
            //
            $page_id            = false;
            $this->page_object  = self::loadPage( $lime, $owner, $page_id );
            $this->page_id      = $page_id;
        }

        //
        // __get
        //
        function __get( $f )
        {
            switch( $f )
            {
                case 'page_id':
                    return $this->page_id;
            }

            return null;
        }

        //
        // __set
        //
        function __set( $f, $v )
        {
            switch( $f )
            {
                case 'page_id':
                    $this->page_id = $v;
            }
        }

        //
        // object accessors
        //
        function &lime()
        {
            return $this->lime_object;
        }

        function &owner()
        {
            return $this->owner_object;
        }

        function &page()
        {
            return $this->page_object;
        }


        //
        // log revision of primary
        //
        private static function logRevision( $type, $id, $data )
        {
            if( strlen($data) )
            {
                WrapMySQL::void(
                    "insert ignore into context_variation_revisions (type,id,data) ".
                    "values ({$type}, {$id}, '{$data}');");
            }
        }


        //
        // log updated
        //
        public static function logUpdate( $lime, $owner, $page )
        {
            if( $lime->id > 0 && $owner > 0 && $page > 0 )
            {
                WrapMySQL::void(
                    "insert into lime_version_log (lime_type, lime_id, owner, page) ".
                    "values ({$lime->type_id}, {$lime->id}, {$owner}, {$page});");
            }
        }


        //
        // load version based on lime and owner
        //
        public static function load( $lime, $owner )
        {
            if( $lime->id > 0 && $owner > 0 )
            {
                $version = WrapMySQL::single(
                            "select P.id, P.text from lime_version V ".
                            "left join lime_page P on P.id=V.page ".
                            "where V.type={$lime->type_id} && V.id={$lime->id} && owner={$owner} ".
                            "limit 1;",
                            "Unable to load LightMeta page version");

                return $version;
            }

            return null;
        }


        //
        // store version based on lime and owner
        // id -> update version | insert page
        //
        public static function store( $lime, $owner, $page, $id=false )
        {
            // serialize page
            $text   = safe_slash(serialize($page));
            $length = strlen($text);

            if( $id>0 )
            {
                WrapMySQL::void(
                    "insert into lime_page (id, text) values ({$id}, '{$text}') ".
                    "on duplicate key update text=values(text);",
                    "Unable to update lime page");

                WrapMySQL::void(
                    "update lime_version set length={$length} ".
                    "where type={$lime->type_id} && id={$lime->id} ".
                    "&& owner={$owner} && page={$id};",
                    "Unable to update lime version");
            }
            else
            {
                $id = WrapMySQL::id(
                    "insert into lime_page (text) values ('{$text}');",
                    "Unable to insert lime page");

                WrapMySQL::void(
                    "insert into lime_version (type, id, owner, page, created, length) ".
                    "values ({$lime->type_id}, {$lime->id}, {$owner}, {$id}, CURRENT_TIMESTAMP, {$length});",
                    "Unable to update lime version");
            }

            return $id;
        }


        //
        // load page based on version
        //
        public static function loadPage( $lime, $owner, &$id=false )
        {
            $page       = null;
            $version    = self::load( $lime, $owner );

            if( !is_null( $version ) )
            {
                $id = $version['id'];

                if( $version['text'] )
                    $page = NuEvent::filter('lime_page_instance', $page, $version['text']);
            }

            return $page;
        }

    }

?>
