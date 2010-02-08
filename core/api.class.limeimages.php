<?php
    
    require_once( 'api.class.limemethod.php' );

    class getLimeImages extends LimeMethod
    {
        protected function query( $context )
        {
            $q = new NuSelect('lime_image_meta as I');
            $q->field(array("id","meta","context_type","context_id"));

            $q->where("I.context_type='{$context->type_id}'");
            $q->where("I.context_id={$context->id}");

            return $q;
        }
    }

?>
