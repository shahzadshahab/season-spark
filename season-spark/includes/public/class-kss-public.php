<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Public-facing class to inject effects
 */
class KSS_Public {

    private static $instance = null;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
            self::$instance->hooks();
        }
        return self::$instance;
    }

    private function hooks() {
        add_action( 'wp_enqueue_scripts', array( 'KSS_Assets', 'enqueue_front_assets' ) );
        add_action( 'wp_footer', array( $this, 'print_effect_containers' ), 20 );
    }

    /**
     * Output DOM containers for effects (hidden if none active). 
     * Containers are aria-hidden and non-interactive.
     */
    public function print_effect_containers() {
        $opts = get_option( 'kss_settings', array() );
        if ( empty( $opts['effects'] ) || ! is_array( $opts['effects'] ) ) {
            return;
        }

        // Check if global reduced motion
        $global_reduced = ! empty( $opts['global']['motion_reduced'] );

        // Determine current date
        $today = new DateTime( 'now', new DateTimeZone( 'UTC' ) );

        // For each effect, decide to print container if enabled and date matches
        foreach ( $opts['effects'] as $key => $cfg ) {
            if ( empty( $cfg['enabled'] ) ) {
                continue;
            }

            // If scheduled, check start/end
            if ( ! empty( $cfg['start'] ) || ! empty( $cfg['end'] ) ) {
                try {
                    $start = ! empty( $cfg['start'] ) ? new DateTime( $cfg['start'], new DateTimeZone( 'UTC' ) ) : null;
                    $end   = ! empty( $cfg['end'] ) ? new DateTime( $cfg['end'], new DateTimeZone( 'UTC' ) ) : null;
                } catch ( Exception $e ) {
                    $start = $end = null;
                }

                if ( $start && $today < $start ) {
                    continue;
                }
                if ( $end && $today > $end ) {
                    continue;
                }
            }

            // If global reduced motion is enabled, skip effects unless user explicitly allows
            if ( $global_reduced ) {
                // print a container with data-disabled for JS; JS will check local override
                printf( '<div class="kss-effect kss-effect-%s" data-kss-effect="%s" aria-hidden="true"></div>' . "\n", esc_attr( $key ), esc_attr( $key ) );
            } else {
                printf( '<div class="kss-effect kss-effect-%s" data-kss-effect="%s" aria-hidden="true"></div>' . "\n", esc_attr( $key ), esc_attr( $key ) );
            }
        }

        // Provide a small accessible toggle for users to disable motion (stored locally)
        echo '<button id="kss-toggle-motion" aria-pressed="false" class="kss-toggle" title="Toggle motion effects" style="position:fixed;right:12px;bottom:12px;z-index:99999;border-radius:60px;padding:10px 14px;background:#222;color:#fff;border:none;box-shadow:0 4px 10px rgba(0,0,0,.2);">✨</button>';
    }
}
