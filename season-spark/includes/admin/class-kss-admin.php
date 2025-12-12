<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Admin class for Season Spark
 */
class KSS_Admin {

    /**
     * Singleton instance
     *
     * @var KSS_Admin
     */
    private static $instance = null;

    /**
     * Get instance
     *
     * @return KSS_Admin
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
            self::$instance->hooks();
        }
        return self::$instance;
    }

    private function hooks() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_enqueue_scripts', array( 'KSS_Assets', 'enqueue_admin_assets' ) );
    }

    /**
     * Register top-level menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __( 'Season Spark', 'season-spark' ),
            __( 'Season Spark', 'season-spark' ),
            'manage_options',
            'kss-settings',
            array( $this, 'render_settings_page' ),
            'dashicons-admin-appearance',
            61
        );
    }

    /**
     * Register settings via Settings API
     */
    public function register_settings() {
        register_setting( 'kss_settings_group', 'kss_settings', array( $this, 'sanitize_settings' ) );

        add_settings_section( 'kss_main_section', __( 'General', 'season-spark' ), null, 'kss-settings' );

        add_settings_field(
            'kss_load_cdn',
            __( 'Load particle library (CDN)', 'season-spark' ),
            array( $this, 'render_checkbox' ),
            'kss-settings',
            'kss_main_section',
            array(
                'label_for' => 'global[load_cdn]',
                'name'      => 'global[load_cdn]',
                'desc'      => __( 'Load tsparticles from CDN. Uncheck if you host it locally.', 'season-spark' ),
            )
        );

        add_settings_field(
            'kss_motion_reduced',
            __( 'Accessibility: Reduce motion', 'season-spark' ),
            array( $this, 'render_checkbox' ),
            'kss-settings',
            'kss_main_section',
            array(
                'label_for' => 'global[motion_reduced]',
                'name'      => 'global[motion_reduced]',
                'desc'      => __( 'When checked, site-wide motion is reduced/disabled by default.', 'season-spark' ),
            )
        );

        // Effects fields -- we'll render a custom block
        add_settings_section( 'kss_effects_section', __( 'Effects & Calendar', 'season-spark' ), null, 'kss-settings' );
    }

    /**
     * Sanitize settings before saving
     *
     * @param array $input raw
     * @return array sanitized
     */
    public function sanitize_settings( $input ) {
        $out = get_option( 'kss_settings', array() );

        // Simple sanitization and merging
        if ( isset( $input['global'] ) ) {
            $out['global']['load_cdn'] = ! empty( $input['global']['load_cdn'] ) ? 1 : 0;
            $out['global']['motion_reduced'] = ! empty( $input['global']['motion_reduced'] ) ? 1 : 0;
            $out['global']['default_density'] = isset( $input['global']['default_density'] ) ? intval( $input['global']['default_density'] ) : $out['global']['default_density'];
            $out['global']['default_speed']   = isset( $input['global']['default_speed'] ) ? floatval( $input['global']['default_speed'] ) : $out['global']['default_speed'];
        }

        // Effects: loop through known keys, otherwise allow filters to add more
        $registered = apply_filters( 'kss_get_registered_effects', $this->default_effects() );

        foreach ( $registered as $key => $ef ) {
            $enabled = ! empty( $input['effects'][ $key ]['enabled'] ) ? 1 : 0;
            $start   = ! empty( $input['effects'][ $key ]['start'] ) ? sanitize_text_field( $input['effects'][ $key ]['start'] ) : '';
            $end     = ! empty( $input['effects'][ $key ]['end'] ) ? sanitize_text_field( $input['effects'][ $key ]['end'] ) : '';
            $color   = ! empty( $input['effects'][ $key ]['color'] ) ? sanitize_text_field( $input['effects'][ $key ]['color'] ) : ( isset( $out['effects'][ $key ]['color'] ) ? $out['effects'][ $key ]['color'] : '#ffffff' );
            $density = isset( $input['effects'][ $key ]['density'] ) ? intval( $input['effects'][ $key ]['density'] ) : ( isset( $out['effects'][ $key ]['density'] ) ? $out['effects'][ $key ]['density'] : $out['global']['default_density'] );
            $speed   = isset( $input['effects'][ $key ]['speed'] ) ? floatval( $input['effects'][ $key ]['speed'] ) : ( isset( $out['effects'][ $key ]['speed'] ) ? $out['effects'][ $key ]['speed'] : $out['global']['default_speed'] );

            $out['effects'][ $key ] = array(
                'enabled' => $enabled,
                'start'   => $start,
                'end'     => $end,
                'color'   => $color,
                'density' => $density,
                'speed'   => $speed,
            );
        }

        return $out;
    }

    /**
     * Default effects list (key => title)
     *
     * @return array
     */
    private function default_effects() {
        return array(
            'christmas'    => __( 'Christmas', 'season-spark' ),
            'halloween'    => __( 'Halloween', 'season-spark' ),
            'valentines'   => __( 'Valentine\'s Day', 'season-spark' ),
            'newyear'      => __( 'New Year', 'season-spark' ),
            'easter'       => __( 'Easter', 'season-spark' ),
            'thanksgiving' => __( 'Thanksgiving', 'season-spark' ),
            'independence' => __( 'Independence Day (US)', 'season-spark' ),
            'diwali'       => __( 'Diwali', 'season-spark' ),
            'hanukkah'     => __( 'Hanukkah', 'season-spark' ),
            'generic'      => __( 'Generic (rain, stars, bubbles)', 'season-spark' ),
        );
    }

    /**
     * Render a checkbox (callback)
     */
    public function render_checkbox( $args ) {
        $opts = get_option( 'kss_settings', array() );
        $name = $args['name'] ?? '';
        $value = '';
        if ( isset( $opts ) ) {
            // name like global[motion_reduced]
            if ( preg_match( '/^([^\[]+)\[([^\]]+)\]/', $name, $m ) ) {
                $group = $m[1];
                $key   = $m[2];
                $value = isset( $opts[ $group ][ $key ] ) ? $opts[ $group ][ $key ] : '';
            }
        }
        $checked = $value ? 'checked' : '';
        echo '<label><input type="checkbox" name="kss_settings[' . esc_attr( $name ) . ']" value="1" ' . $checked . '> ' . esc_html( $args['desc'] ) . '</label>';
    }

    /**
     * Render the whole settings page
     */
    public function render_settings_page() {
        $opts = get_option( 'kss_settings', array() );
        ?>
        <div class="wrap kss-wrap">
            <h1 class="kss-title"><?php esc_html_e( 'Season Spark — Visual Holiday Effects', 'season-spark' ); ?></h1>
            <p class="kss-sub"><?php esc_html_e( 'Add lightweight, accessible seasonal visual effects across your site. Changes are saved instantly when you hit Save Changes below.', 'season-spark' ); ?></p>

            <form method="post" action="options.php">
                <?php
                settings_fields( 'kss_settings_group' );
                // Render general controls
                do_settings_sections( 'kss-settings' );
                ?>

                <h2><?php esc_html_e( 'Effects', 'season-spark' ); ?></h2>

                <div class="kss-effects-grid">
                    <?php
                    $registered = apply_filters( 'kss_get_registered_effects', $this->default_effects() );
                    foreach ( $registered as $key => $title ) :
                        $eff = isset( $opts['effects'][ $key ] ) ? $opts['effects'][ $key ] : array();
                        $enabled = ! empty( $eff['enabled'] ) ? 1 : 0;
                        $color = ! empty( $eff['color'] ) ? $eff['color'] : '#ffffff';
                        $start = ! empty( $eff['start'] ) ? $eff['start'] : '';
                        $end   = ! empty( $eff['end'] ) ? $eff['end'] : '';
                        $density = isset( $eff['density'] ) ? intval( $eff['density'] ) : ( isset( $opts['global']['default_density'] ) ? intval( $opts['global']['default_density'] ) : 50 );
                        $speed   = isset( $eff['speed'] ) ? floatval( $eff['speed'] ) : ( isset( $opts['global']['default_speed'] ) ? floatval( $opts['global']['default_speed'] ) : 1.0 );
                    ?>
                        <div class="kss-effect-card">
                            <div class="kss-effect-header">
                                <h3><?php echo esc_html( $title ); ?></h3>
                                <label class="kss-switch">
                                    <input type="checkbox" name="kss_settings[effects][<?php echo esc_attr( $key ); ?>][enabled]" value="1" <?php checked( $enabled, 1 ); ?>>
                                    <span class="kss-slider"></span>
                                </label>
                            </div>

                            <div class="kss-effect-body">
                                <p class="kss-note"><?php esc_html_e( 'Schedule dates (optional):', 'season-spark' ); ?></p>
                                <input class="kss-date" placeholder="YYYY-MM-DD" name="kss_settings[effects][<?php echo esc_attr( $key ); ?>][start]" value="<?php echo esc_attr( $start ); ?>">
                                <input class="kss-date" placeholder="YYYY-MM-DD" name="kss_settings[effects][<?php echo esc_attr( $key ); ?>][end]" value="<?php echo esc_attr( $end ); ?>">

                                <p class="kss-note"><?php esc_html_e( 'Color & performance', 'season-spark' ); ?></p>
                                <input class="kss-color" name="kss_settings[effects][<?php echo esc_attr( $key ); ?>][color]" value="<?php echo esc_attr( $color ); ?>">

                                <div class="kss-row">
                                    <label><?php esc_html_e( 'Density', 'season-spark' ); ?>
                                        <input type="number" min="0" max="200" name="kss_settings[effects][<?php echo esc_attr( $key ); ?>][density]" value="<?php echo esc_attr( $density ); ?>">
                                    </label>
                                    <label><?php esc_html_e( 'Speed', 'season-spark' ); ?>
                                        <input type="number" step="0.1" min="0" max="10" name="kss_settings[effects][<?php echo esc_attr( $key ); ?>][speed]" value="<?php echo esc_attr( $speed ); ?>">
                                    </label>
                                </div>

                                <p class="kss-help"><?php esc_html_e( 'Tip: leave dates blank to have the effect run year-round (for generic). Use low density on mobile for performance.', 'season-spark' ); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php submit_button(); ?>
            </form>

            <hr>

            <div class="kss-pro-tips">
                <h3><?php esc_html_e( 'Developer / Pro Tips', 'season-spark' ); ?></h3>
                <ul>
                    <li><?php esc_html_e( 'Use the filter kss_get_registered_effects to add your own effects.', 'season-spark' ); ?></li>
                    <li><?php esc_html_e( 'Assets keep small. Prefer SVGs and compressed images.', 'season-spark' ); ?></li>
                </ul>
            </div>
        </div>
        <?php
    }
}
