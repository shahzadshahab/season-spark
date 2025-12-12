<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Central asset loader helpers (UPDATED)
 * - Enqueues tsparticles and the public initializer
 * - Enqueues only effect JS files for enabled effects (performance)
 */
class KSS_Assets {

    /**
     * Enqueue front-end assets
     *
     * @param bool $only_if_enabled Whether to bail early if none enabled.
     */
    public static function enqueue_front_assets( $only_if_enabled = true ) {
        $opts = get_option( 'kss_settings', array() );

        // if requested, bail when no effects enabled
        if ( $only_if_enabled ) {
            $enabled_any = false;
            if ( ! empty( $opts['effects'] ) && is_array( $opts['effects'] ) ) {
                foreach ( $opts['effects'] as $eff ) {
                    if ( ! empty( $eff['enabled'] ) ) {
                        $enabled_any = true;
                        break;
                    }
                }
            }
            if ( ! $enabled_any ) {
                return;
            }
        }

        // Load tsparticles from CDN if enabled
        $load_cdn = isset( $opts['global']['load_cdn'] ) ? (int) $opts['global']['load_cdn'] : 1;
        if ( $load_cdn ) {
            wp_register_script( 'kss-tsparticles', 'https://cdn.jsdelivr.net/npm/tsparticles@2.11.0/tsparticles.bundle.min.js', array(), null, true );
            wp_enqueue_script( 'kss-tsparticles' );
        }

        // public initializer (depends on tsparticles)
        wp_register_script( 'kss-public', KSS_PLUGIN_URL . 'assets/js/kss-public.js', array( 'kss-tsparticles' ), KSS_VERSION, true );

        /** ----------------------------
         * Localize settings for front-end JS
         * ---------------------------- */
        wp_localize_script(
            'kss-public',
            'kssSettings',
            apply_filters( 'kss_settings_for_js', get_option( 'kss_settings', array() ) )
        );

        /** ----------------------------
         * Localize images for effects
         * ---------------------------- */
        $images_dir = KSS_PLUGIN_URL . 'assets/images/';

        $images = [
            // Christmas
            'snowflake' => $images_dir . 'snowflake.svg',
            'light'     => $images_dir . 'light.svg',

            // Halloween
            'ghost'     => $images_dir . 'ghost.svg',
            'pumpkin'   => $images_dir . 'pumpkin.svg',

            // Valentine’s
            'heart'     => $images_dir . 'heart.svg',

            // New Year
            'firework'  => $images_dir . 'firework.svg',

            // Easter
            'egg'       => $images_dir . 'egg.svg',
            'bunny'     => $images_dir . 'bunny.svg',

            // Thanksgiving
            'leaf'      => $images_dir . 'leaf.svg',
            'turkey'    => $images_dir . 'turkey.svg',

            // Independence Day
            'us_flag'   => $images_dir . 'us-flag.svg',

            // Diwali
            'diya'      => $images_dir . 'diya.svg',
            'rangoli'   => $images_dir . 'rangoli.svg',

            // Hanukkah
            'menorah'   => $images_dir . 'menorah.svg',

            // Generic
            'rain_drop' => $images_dir . 'rain-drop.svg',
            'star'      => $images_dir . 'star.svg',
            'bubble'    => $images_dir . 'bubble.svg',
        ];

        /**
         * Allow developers to filter image list
         * (WP.org requires extensibility)
         */
        $images = apply_filters( 'kss_images_for_js', $images );

        /**
         * Finally pass images to JS
         */
        wp_localize_script(
            'kss-public',
            'kssImages',
            $images
        );


        wp_enqueue_script( 'kss-public' );

        // enqueue per-effect scripts conditionally
        if ( ! empty( $opts['effects'] ) && is_array( $opts['effects'] ) ) {
            foreach ( $opts['effects'] as $key => $cfg ) {
                if ( ! empty( $cfg['enabled'] ) ) {
                    $effect_file = 'assets/js/effects/' . sanitize_file_name( $key ) . '.js';
                    $effect_path = KSS_PLUGIN_DIR . $effect_file;
                    $effect_url  = KSS_PLUGIN_URL . $effect_file;
                    if ( file_exists( $effect_path ) ) {
                        wp_register_script( 'kss-effect-' . $key, $effect_url, array( 'kss-public', 'kss-tsparticles' ), KSS_VERSION, true );
                        wp_enqueue_script( 'kss-effect-' . $key );
                    }
                }
            }
        }

        // Register public stylesheet
        wp_enqueue_style( 'kss-public-css', KSS_PLUGIN_URL . 'assets/css/kss-public.css', array(), KSS_VERSION );
    }

    /**
     * Enqueue admin assets for settings page only
     */
    public static function enqueue_admin_assets( $hook ) {
        // Only load on our plugin settings page
        if ( 'toplevel_page_kss-settings' !== $hook ) {
            return;
        }

        wp_enqueue_style( 'kss-admin-css', KSS_PLUGIN_URL . 'assets/css/kss-admin.css', array(), KSS_VERSION );
        wp_enqueue_script( 'kss-admin-js', KSS_PLUGIN_URL . 'assets/js/kss-admin.js', array( 'jquery', 'wp-color-picker' ), KSS_VERSION, true );

        // For color pickers and UI niceties
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
    }
}
