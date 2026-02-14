<?php
/**
 * Plugin Name: CBK Drolma
 * Description: A dedicated plugin for the Drolma Rennes center. Adds specific features to make the regular updates easier.
 * Version: 0.0.5
 * Author: Drolma-VCT
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'CBK_DROLMA_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'CBK_DROLMA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once CBK_DROLMA_PLUGIN_PATH . 'includes/cbk-drolma-courses-manager.php';
require_once CBK_DROLMA_PLUGIN_PATH . 'includes/cbk-drolma-pass-manager.php';

require_once CBK_DROLMA_PLUGIN_PATH . 'widgets/widgets-loader.php';

new CBK_Drolma_Courses_Manager();
