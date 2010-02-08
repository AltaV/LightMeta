<?php
    
    /*
     * getLimeTypes
    */

    require_once('abstract.apimethod.php');
    require_once('lib.lime.php');

    class getLimeTypesXML extends NuclearAPIMethod
    {
        protected function build()
        {
            $q = new LimeTypesQuery();
            
            if( $q->select() )
            {
                $types = new LimeTypesXML( $q );
                return $types;
            }

            return '<response status="error" message="No defined types" />';
        }
    }

    return "getLimeTypesXML";

?>
