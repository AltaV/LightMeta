<?php
    
    /*
     * getLimeTypes
    */

    require_once('abstract.apimethod.php');

    class getLimeTypes extends NuclearAPIMethod
    {
        protected function build()
        {
            $q = new LimeTypesQuery();
            
            if( $q->select() )
            {
                $types = new LimeTypesJSON();

                while( $type = $q->hash() )
                {
                    $types->append(
                        array( "id"=>$type['id'], "type"=>$type['type'], "label"=>$type['label'] )
                    );
                }

                return $types;
            }

            return array("response"=>"error","message"=>"No defined types");
        }
    }

    return "getLimeTypes";

?>
