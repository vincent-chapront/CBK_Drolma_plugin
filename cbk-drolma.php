<?php
/**
 * Plugin Name: CBK Drolma
 * Description: Manage courses and lessons and display them via Elementor widgets.
 * Version: 0.0.5
 * Author: Drolma-VCT
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'CBK_DROLMA_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'CBK_DROLMA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once CBK_DROLMA_PLUGIN_PATH . 'includes/cbk-drolma-courses-manager.php';
require_once CBK_DROLMA_PLUGIN_PATH . 'includes/cbk-drolma-events-manager.php';

require_once CBK_DROLMA_PLUGIN_PATH . 'widgets/widgets-loader.php';

new CBK_Drolma_Courses_Manager();
new CBK_Drolma_Events_Manager();
