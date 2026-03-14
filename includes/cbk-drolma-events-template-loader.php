<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Template loader for CBK Drolma Events
class CBK_Drolma_Events_Template_Loader {
    public function __construct() {
        add_filter( 'single_template', [ $this, 'load_single_event_template' ] );
    }

    public function load_single_event_template( $single ) {
        global $post;
        if ( $post->post_type === 'cbk_drolma_event' ) {
            $plugin_template = dirname(__FILE__) . '/templates/single-cbk_drolma_event.php';
            if ( file_exists( $plugin_template ) ) {
                return $plugin_template;
            }
        }
        return $single;
    }
}
