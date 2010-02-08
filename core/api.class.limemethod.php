<?php
    
    //
    // require nuclear
    //
    require_once('abstract.apimethod.php');

    /**
     * LimeMethod - API method using Lime object
    */

    abstract class LimeMethod extends NuclearAPIMethod
    {
        protected function &getLime()
        {
            $lime = new Object();

            if( $object = LimeFocus::getInstance() )
            {
                return $object;
            }

            throw new Exception("Invalid or missing Lime focus",4);
            return null;
        }
    }

?>
