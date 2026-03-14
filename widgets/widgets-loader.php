<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'elementor/widgets/register', function( $widgets_manager ) {

    // Elementor not active
    if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
        return;
    }

    require_once CBK_DROLMA_PLUGIN_PATH . 'widgets/cbk-drolma-courses-extract-widget.php';
    require_once CBK_DROLMA_PLUGIN_PATH . 'widgets/cbk-drolma-courses-calendar-widget.php';
    require_once CBK_DROLMA_PLUGIN_PATH . 'widgets/cbk-drolma-passes-widget.php';
    require_once CBK_DROLMA_PLUGIN_PATH . 'widgets/cbk-drolma-event-widget.php';

    $widgets_manager->register( new CBK_Drolma_Courses_Extract_Widget() );
    $widgets_manager->register( new CBK_Drolma_Courses_Calendar_Widget() );
    $widgets_manager->register( new CBK_Drolma_Passes_Widget() );
    $widgets_manager->register( new CBK_Drolma_Event_Widget() );
});
