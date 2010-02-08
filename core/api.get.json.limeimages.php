<?php
    
    require_once( 'api.class.limeimages.php' );

    class getLimeImagesJSON extends getLimeImages
    {
        protected function build()
        {
            // get Lime, get query
            $context    = $this->getLime();
            $q          = $this->query($context);

            // build resp
            $resp = new JSON($this->time);

            if( $q->select() )
            {
                $resp->status = "ok";

                $images = array();
                while( $image = $q->hash() )
                {
                    $data = new Object();
                    $meta = unserialize($image['meta']);
                    $data->id = $image['id'];

                    $data = NuEvent::filter('lime_image_json', $data, $image );

                    // default data
                    $data->width = $meta->info[0];
                    $data->height= $meta->info[1];

                    $data->timestamp    = $meta->gmts;
                    $data->utc          = date('r', $meta->gmts);
                    $data->uploader     = $meta->user;
                    $data->method       = $meta->method;

                    if( $meta->method == 'url' )
                        $data->source   = $meta->source;

                    $images[] = $data;
                }

                $resp->images = $images;
            }

            return $resp;
        }
    }

    return "getLimeImagesJSON";

?>
