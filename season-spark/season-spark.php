<?php
/**
 * Plugin Name: Season Spark
 * Plugin URI: https://creatingbee.com
 * Description: Site-wide lightweight front-end holiday visual effects (snow, hearts, fireworks, etc.) with a premium admin UI and accessibility toggles.
 * Version: 1.0.0
 * Author: Shahzad Shahab
 * Author URI: https://creatingbee.com
 * Text Domain: season-spark
 * Domain Path: /languages
 *
 * @package SeasonSpark
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Plugin constants
 */
define( 'KSS_PLUGIN_FILE', __FILE__ );
define( 'KSS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'KSS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'KSS_VERSION', '1.0.0' );

/**
 * Autoload or include key files
 */
require_once KSS_PLUGIN_DIR . 'includes/class-kss-assets.php';
require_once KSS_PLUGIN_DIR . 'includes/admin/class-kss-admin.php';
require_once KSS_PLUGIN_DIR . 'includes/public/class-kss-public.php';

/**
 * Activation hook
 */
function kss_activate() {
    // set default options
    $defaults = array(
        'effects' => array(
            'christmas'     => array( 'enabled' => 1, 'start' => '', 'end' => '' ),
            'halloween'     => array( 'enabled' => 1, 'start' => '', 'end' => '' ),
            'valentines'    => array( 'enabled' => 1, 'start' => '', 'end' => '' ),
            'newyear'       => array( 'enabled' => 1, 'start' => '', 'end' => '' ),
            'easter'        => array( 'enabled' => 0, 'start' => '', 'end' => '' ),
            'thanksgiving'  => array( 'enabled' => 0, 'start' => '', 'end' => '' ),
            'independence'  => array( 'enabled' => 0, 'start' => '', 'end' => '' ),
            'diwali'        => array( 'enabled' => 0, 'start' => '', 'end' => '' ),
            'hanukkah'      => array( 'enabled' => 0, 'start' => '', 'end' => '' ),
            'generic'       => array( 'enabled' => 0, 'start' => '', 'end' => '' ),
        ),
        'global' => array(
            'motion_reduced' => 0, // site-level disable for motion (admin)
            'default_density' => 50,
            'default_speed' => 1.0,
            'load_cdn' => 1,
        ),
    );

    if ( ! get_option( 'kss_settings' ) ) {
        add_option( 'kss_settings', $defaults );
    }
}
register_activation_hook( __FILE__, 'kss_activate' );

/**
 * Initialize plugin (admin + public)
 */
function kss_init_plugin() {
    // load translations
    load_plugin_textdomain( 'season-spark', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

    // instantiate admin and public classes
    if ( is_admin() ) {
        KSS_Admin::get_instance();
    }
    KSS_Public::get_instance();
}
add_action( 'plugins_loaded', 'kss_init_plugin' );
