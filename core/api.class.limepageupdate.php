<?php
    
    /*
    * Lime Page Update
    */

    require_once( 'api.class.limemethod.php' );

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
            $owner  = $this->getAuth();
            $lime   = $this->getLime();

            $page_id    = false;

            if( is_null( $version ) )
            {
                // get version
                //
                $page = LimeVersion::page( $lime, $owner->id, $page_id );
            }
            else
            {
                // otherwise use version
                //
                $page = $version;
            }


            // raise event that page is being updated
            // pass the page object and the request data
            //
            $page = NuEvent::filter( 'lime_page_update', $page, $this->call );


            // if page null, no change
            //
            if( !is_null($page) )
            {

                // store page for lime+owner
                //
                $page_id = LimeVersion::store( $lime, $owner->id, $page, $page_id );

                if( $page_id )
                {
                    $response['message'] = "Lime version updated";
                }


                // submit for approval
                //
                $submission = $owner->level == 'default';


                // update for owner=1
                //
                if( isType( 'root|super|administrator|moderator|editor', $owner->level ) )
                {
                    // downmix?
                    LimeVersion::update( $lime, 1, $page );
                    $response['message'].= "; Live version updated";
                }
            }
            else
            {
                $response['message'] = 'No changes updated';
            }
            
            return $response;
        }
    }

?>
