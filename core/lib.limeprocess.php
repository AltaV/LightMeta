<?php
    
    /*
    * Lime Page Library
    */

    class LimeProcess
    {
        
        //
        // Updating
        //
        protected function update( $version, $request, $live=false );
        {
            $response   = array();
            $lime       = $version->lime();
            $owner      = $version->owner();
            $page_id    = $version->page_id;
            $page_object= $version->page();

            // raise event
            //
            $page_object = NuEvent::filter( 'lime_page_update', $page_object, $request );


            // no change on null page
            //
            if( !is_null($page_object) )
            {

                // store page for lime+owner
                //
                $page_id = LimeVersion::store( $lime, $owner->id, $page_object, $page_id );

                if( $page_id )
                {
                    $response['message'] = "Lime version updated";
                }

                
                // check for live updating
                //
                if( $live )
                {

                    // log versioning
                    //
                    LimeVersion::logUpdate( $lime, $owner->id, $page_id );


                    // raise specific level updates
                    //
                    switch( $owner->level )
                    {
                        case 'root':
                        case 'super':
                        case 'administrator':
                            NuEvent::action( 'lime_page_updated_admin', $page_object );
                            break;

                        case 'moderator':
                            NuEvent::action( 'lime_page_updated_moderator', $page_object );
                        break;
                    
                        case 'editor':
                            NuEvent::action( 'lime_page_updated_editor', $page_object );
                            break;
                    }
                }
            }
            else
            {
                $response['message'] = 'No changes updated';
            }

            $response['status'] = "ok";
            return $response;
        }


        //
        // Merging
        //
        protected function merge( $version, $request, $live=false )
        {
            $response   = array();
            $lime       = $version->lime();
            $owner      = $version->owner();
            $page_id    = $version->page_id;
            $page_object= $version->page();

            // check for live merge
            //
            if( $live )
            {
                // raise specific level updates
                //
                switch( $owner->level )
                {
                    case 'root':
                    case 'super':
                    case 'administrator':
                        $version  = NuEvent::filter( 'lime_page_mergelive_admin', $version, $request );
                        break;

                    case 'moderator':
                        $version  = NuEvent::filter( 'lime_page_mergelive_moderator', $version, $request );
                        break;
                
                    case 'editor':
                        $version = NuEvent::filter( 'lime_page_mergelive_editor', $version, $request );
                        break;

                    default:
                        $version = null;
                        break;
                }
            }
            else
            {
                // merge with version
                //
                $page_object = NuEvent::filter( 'lime_page_merge', $page_object, $request );

                // no change on null page
                //
                if( !is_null($page_object) )
                {
                    // store page for lime+owner
                    //
                    $page_id = LimeVersion::store( $lime, $owner->id, $page_object, $page_id );

                    if( $page_id )
                    {
                        $response['message'] = "Lime version updated";
                    }
                }
                else
                {
                    $response['message'] = 'No changes merged';
                }
            }

            $response['status'] = 'ok';
            return $response;
        }
    }

?>
