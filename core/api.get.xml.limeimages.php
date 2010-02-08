<?php
    
    require_once( 'api.class.limeimages.php' );

    class getLimeImagesXML extends getLimeImages
    {
        protected function build()
        {
            $context    = $this->getLime();
            $q          = $this->query($context);

            $resp = new XMLResponse();
            $cnode    = $resp->attach('context');

            $cnode->appendChild( $resp->attach('type', $context->type) );
            $cnode->appendChild( $resp->attach('id', $context->id) );
            $cnode->appendChild( $resp->attach('name', $context->name) );
            $resp->append($cnode);


            if( $q->select() )
            {
                $resp->status = "ok";
                while( $image = $q->hash() )
                {
                    $node = $resp->attach('image');
                    $meta = unserialize($image['meta']);
                    $node->appendChild( $resp->attach('id', $image['id']) );

                    NuEvent::filter("lime_image_xml", $node, $image);

                    $node->appendChild( $resp->attach('timestamp', $meta->gmts) );
                    $node->appendChild( $resp->attach('utc', date('r',$meta->gmts)) );
                    $node->appendChild( $resp->attach('uploader', $meta->user) );
                    $node->appendChild( $resp->attach('method', $meta->method) );

                    if( $meta->method == 'url' )
                        $node->appendChild( $resp->attach('source', $meta->source) );

                    $node->appendChild( $resp->attach('width', $meta->info[0]) );
                    $node->appendChild( $resp->attach('height', $meta->info[1]) );

                    $resp->append($node);
                }
            }

            return $resp;
        }
    }

    return "getLimeImagesXML";

?>
