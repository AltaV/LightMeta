<?php
    
    /*
    * Lime Page Update
    */

    require_once( 'api.class.limemethod.php' );
    require_once( 'lib.limeprocess.php' );

    class LimePageUpdate extends LimeMethod
    {
        protected function getAuth()
        {
            $auth = AuthorizedUser::getInstance();

            if( is_null( $auth ) )
                throw new Exception("Unauthorized",2);

            return $auth;
        }

        protected function process()
        {

            // get version key
            //
            $owner      = $this->getAuth();
            $lime       = $this->getLime();

            // init version
            //
            $version    = new LimeVersion( $lime, $owner );

            // process update
            //
            $response   = LimeProcess::update(
                            $version, 
                            $this->call, 
                            $this->call->lime_live );
            // return
            //
            return $response;

        }
    }

?>
