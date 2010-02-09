<?php
    
    require_once('api.class.limepageupdate.php');

    class postLimePageUpdateJSON extends LimePageUpdate
    {

        protected function build()
        {
            $resp   = new JSON( $this->time );
            $result = $this->process();
            
            $resp->status   = $results['status'];
            $resp->message  = $result['message'];

            return $resp;
        }

    }

    return "postLimePageUpdateJSON";

?>
