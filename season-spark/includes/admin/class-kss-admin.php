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
        // Top-level menu (points to Effects by default)
        add_menu_page(
            __( 'Season Spark', 'season-spark' ),
            __( 'Season Spark', 'season-spark' ),
            'manage_options',
            'kss-settings',
            array( $this, 'render_effects_page' ),
            'dashicons-buddicons-community',
            61
        );

        // Sub-pages: Effects (main), Settings, For Devs
        add_submenu_page( 'kss-settings', __( 'Effects', 'season-spark' ), __( 'Effects', 'season-spark' ), 'manage_options', 'kss-settings', array( $this, 'render_effects_page' ) );
        add_submenu_page( 'kss-settings', __( 'Settings', 'season-spark' ), __( 'Settings', 'season-spark' ), 'manage_options', 'kss-settings-general', array( $this, 'render_settings_page' ) );
        add_submenu_page( 'kss-settings', __( 'For Devs', 'season-spark' ), __( 'For Devs', 'season-spark' ), 'manage_options', 'kss-settings-dev', array( $this, 'render_devs_page' ) );
    }

    /**
     * Register settings via Settings API
     */
    public function register_settings() {
        register_setting( 'kss_settings_group', 'kss_settings', array( $this, 'sanitize_settings' ) );

        // General settings registered on the Settings subpage
        add_settings_section( 'kss_main_section', __( 'General', 'season-spark' ), null, 'kss-settings-general' );

        add_settings_field(
            'kss_load_cdn',
            __( 'Load particle library (CDN)', 'season-spark' ),
            array( $this, 'render_checkbox' ),
            'kss-settings-general',
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
            'kss-settings-general',
            'kss_main_section',
            array(
                'label_for' => 'global[motion_reduced]',
                'name'      => 'global[motion_reduced]',
                'desc'      => __( 'When checked, site-wide motion is reduced/disabled by default.', 'season-spark' ),
            )
        );

        // Effects fields -- displayed on the Effects subpage (custom HTML)
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
            $enabled  = ! empty( $input['effects'][ $key ]['enabled'] ) ? 1 : 0;
            $schedule = ! empty( $input['effects'][ $key ]['schedule'] ) ? 1 : 0;
            $start    = '';
            $end      = '';

            if ( $schedule ) {
                $start = ! empty( $input['effects'][ $key ]['start'] ) ? sanitize_text_field( $input['effects'][ $key ]['start'] ) : '';
                $end   = ! empty( $input['effects'][ $key ]['end'] ) ? sanitize_text_field( $input['effects'][ $key ]['end'] ) : '';
            }
            $color   = ! empty( $input['effects'][ $key ]['color'] ) ? sanitize_text_field( $input['effects'][ $key ]['color'] ) : ( isset( $out['effects'][ $key ]['color'] ) ? $out['effects'][ $key ]['color'] : '#ffffff' );
            $density = isset( $input['effects'][ $key ]['density'] ) ? intval( $input['effects'][ $key ]['density'] ) : ( isset( $out['effects'][ $key ]['density'] ) ? $out['effects'][ $key ]['density'] : $out['global']['default_density'] );
            $speed   = isset( $input['effects'][ $key ]['speed'] ) ? floatval( $input['effects'][ $key ]['speed'] ) : ( isset( $out['effects'][ $key ]['speed'] ) ? $out['effects'][ $key ]['speed'] : $out['global']['default_speed'] );
            $custom_cursor = ! empty( $input['effects'][ $key ]['custom_cursor'] ) ? 1 : 0;

            $out['effects'][ $key ] = array(
                'enabled'      => $enabled,
                'schedule'     => $schedule,
                'start'        => $start,
                'end'          => $end,
                'color'        => $color,
                'density'      => $density,
                'speed'        => $speed,
                'custom_cursor'=> $custom_cursor,
                // allow generic to store custom image URLs
                'custom_bg' => isset( $input['effects'][ $key ]['custom_bg'] ) ? esc_url_raw( $input['effects'][ $key ]['custom_bg'] ) : ( isset( $out['effects'][ $key ]['custom_bg'] ) ? $out['effects'][ $key ]['custom_bg'] : '' ),
                'custom_cursor_image' => isset( $input['effects'][ $key ]['custom_cursor_image'] ) ? esc_url_raw( $input['effects'][ $key ]['custom_cursor_image'] ) : ( isset( $out['effects'][ $key ]['custom_cursor_image'] ) ? $out['effects'][ $key ]['custom_cursor_image'] : '' ),
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
            'generic'      => __( 'Custom Graphics', 'season-spark' ),
        );
    }

    /**
     * Render a checkbox (callback)
     */
    public function render_checkbox( $args ) {
        $opts  = get_option( 'kss_settings', array() );
        $name  = $args['name'] ?? '';
        $value = '';

        if ( isset( $opts ) ) {
            // name like global[motion_reduced]
            if ( preg_match( '/^([^\[]+)\[([^\]]+)\]/', $name, $m ) ) {
                $group = $m[1];
                $key   = $m[2];
                $value = isset( $opts[ $group ][ $key ] ) ? $opts[ $group ][ $key ] : '';
            }
        }

        echo '<label><input type="checkbox" name="kss_settings[' . esc_attr( $name ) . ']" value="1" ' . checked( $value, 1, false ) . '> ' . esc_html( $args['desc'] ) . '</label>';
    }


    /**
     * Render the whole settings page
     */
    public function render_settings_page() {
        $opts = get_option( 'kss_settings', array() );
        ?>
        <div class="wrap kss-wrap">
            <?php $banner = KSS_PLUGIN_URL . 'assets/images/banner.png'; ?>
            <?php if ( $banner ) : ?>
                <img class="kss-header-banner" src="<?php echo esc_url( $banner ); ?>" alt="<?php esc_attr_e( 'Season Spark banner', 'season-spark' ); ?>" />
            <?php endif; ?>
            <h1 class="kss-title"><?php esc_html_e( 'Season Spark — Settings', 'season-spark' ); ?></h1>
            <p class="kss-sub"><?php esc_html_e( 'Core configuration for Season Spark. Lightweight, modular, and performance-minded — this plugin ships assets locally and only initialises enabled effects.', 'season-spark' ); ?></p>

            <div class="kss-pro-tips">
                <strong><?php esc_html_e( 'Quick tips:', 'season-spark' ); ?></strong>
                <ul>
                    <li><?php esc_html_e( 'Keep density low on mobile for best performance.', 'season-spark' ); ?></li>
                    <li><?php esc_html_e( 'Use the accessibility toggle to honour prefers-reduced-motion.', 'season-spark' ); ?></li>
                    <li><?php esc_html_e( 'Set `Load particle library (CDN)` off to use bundled local copy (recommended for WP.org compliance).', 'season-spark' ); ?></li>
                </ul>
            </div>

            <?php if ( filter_input( INPUT_GET, 'settings-updated', FILTER_VALIDATE_BOOLEAN ) ) : ?>
                <div class="notice notice-success is-dismissible kss-save-notice"><p><?php esc_html_e( 'Season Spark settings saved.', 'season-spark' ); ?></p></div>
            <?php endif; ?>

            <form method="post" action="options.php">
                <?php
                settings_fields( 'kss_settings_group' );
                // Render general controls registered on the Settings subpage
                do_settings_sections( 'kss-settings-general' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Render Effects page (separate subpage)
     */
    public function render_effects_page() {
        $opts = get_option( 'kss_settings', array() );
        ?>
        <div class="wrap kss-wrap">
            <?php $banner = KSS_PLUGIN_URL . 'assets/images/banner.png'; ?>
            <?php if ( $banner ) : ?>
                <img class="kss-header-banner" src="<?php echo esc_url( $banner ); ?>" alt="<?php esc_attr_e( 'Season Spark banner', 'season-spark' ); ?>" />
            <?php endif; ?>
            <h1 class="kss-title"><?php esc_html_e( 'Season Spark — Effects', 'season-spark' ); ?></h1>
            <p class="kss-sub"><?php esc_html_e( 'Enable tasteful, accessible visual effects across your site. Configure color, density, speed and optional per-effect scheduling. Only enabled effects load their JS to keep the front-end lightweight.', 'season-spark' ); ?></p>

            <?php if ( filter_input( INPUT_GET, 'settings-updated', FILTER_VALIDATE_BOOLEAN ) ) : ?>
                <div class="notice notice-success is-dismissible kss-save-notice"><p><?php esc_html_e( 'Season Spark settings saved.', 'season-spark' ); ?></p></div>
            <?php endif; ?>

            <form id="kss-effects-form" method="post" action="options.php">
                <?php
                settings_fields( 'kss_settings_group' );
                ?>

                <div class="kss-savebar" style="display:flex;justify-content:flex-end;margin-bottom:12px;gap:12px;">
                    <button type="submit" class="button button-primary kss-save-btn"><?php esc_html_e( 'Save Changes', 'season-spark' ); ?></button>
                </div>

                <h2><?php esc_html_e( 'Effects', 'season-spark' ); ?></h2>

                <div class="kss-effects-grid">
                    <?php
                    $registered = apply_filters( 'kss_get_registered_effects', $this->default_effects() );
                    foreach ( $registered as $key => $title ) :
                        $eff = isset( $opts['effects'][ $key ] ) ? $opts['effects'][ $key ] : array();
                        $enabled = ! empty( $eff['enabled'] ) ? 1 : 0;
                        $custom_cursor = ! empty( $eff['custom_cursor'] ) ? 1 : 0;
                        $color = ! empty( $eff['color'] ) ? $eff['color'] : '#ffffff';
                        $start = ! empty( $eff['start'] ) ? $eff['start'] : '';
                        $end   = ! empty( $eff['end'] ) ? $eff['end'] : '';
                        $density = isset( $eff['density'] ) ? intval( $eff['density'] ) : ( isset( $opts['global']['default_density'] ) ? intval( $opts['global']['default_density'] ) : 50 );
                        $speed   = isset( $eff['speed'] ) ? floatval( $eff['speed'] ) : ( isset( $opts['global']['default_speed'] ) ? floatval( $opts['global']['default_speed'] ) : 1.0 );
                    ?>
                        <div class="kss-effect-card">
                            <div class="kss-effect-body-inner">
                                <!-- Row 1: title (left) and enabled toggle (right) -->
                                <div class="kss-row kss-row-1">
                                    <div class="kss-col kss-col-50">
                                        <h3 class="kss-effect-title"><?php echo esc_html( $title ); ?></h3>
                                    </div>
                                    <div class="kss-col kss-col-50 kss-col-right">
                                        <label class="kss-switch kss-switch-right">
                                            <input type="checkbox" name="kss_settings[effects][<?php echo esc_attr( $key ); ?>][enabled]" value="1" <?php checked( $enabled, 1 ); ?>>
                                            <span class="kss-slider"></span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Row 2: density and speed -->
                                <div class="kss-row kss-row-2" style="display:flex;gap:12px;margin-top:10px;">
                                    <div style="flex:1;">
                                        <label class="kss-grid-label"><?php esc_html_e( 'Density', 'season-spark' ); ?></label>
                                        <input type="number" min="0" max="200" placeholder="0-200" name="kss_settings[effects][<?php echo esc_attr( $key ); ?>][density]" value="<?php echo esc_attr( $density ); ?>" <?php echo $enabled ? '' : 'disabled="disabled"'; ?> style="width:100%;">
                                    </div>
                                    <div style="flex:1;">
                                        <label class="kss-grid-label"><?php esc_html_e( 'Speed', 'season-spark' ); ?></label>
                                        <input type="number" step="0.1" min="0" max="10" placeholder="0.0-10.0" name="kss_settings[effects][<?php echo esc_attr( $key ); ?>][speed]" value="<?php echo esc_attr( $speed ); ?>" <?php echo $enabled ? '' : 'disabled="disabled"'; ?> style="width:100%;">
                                    </div>
                                </div>

                                <!-- Row 3: schedule toggle + label, custom cursor toggle + label, color picker -->
                                <div class="kss-row kss-row-3" style="display:flex;gap:12px;align-items:center;margin-top:12px;">
                                    <div class="kss-inline-label">
                                        <label class="kss-switch kss-schedule-switch" style="margin-right:8px;">
                                            <input type="checkbox" class="kss-schedule-toggle" name="kss_settings[effects][<?php echo esc_attr( $key ); ?>][schedule]" value="1" <?php checked( $eff['schedule'] ?? 0, 1 ); ?> <?php echo $enabled ? '' : 'disabled="disabled"'; ?> />
                                            <span class="kss-slider"></span>
                                        </label>
                                        <span><?php esc_html_e( 'Schedule', 'season-spark' ); ?></span>
                                    </div>

                                    <div class="kss-inline-label">
                                        <label class="kss-switch kss-cursor-switch" style="margin-right:8px;">
                                            <input type="checkbox" class="kss-cursor-toggle" name="kss_settings[effects][<?php echo esc_attr( $key ); ?>][custom_cursor]" value="1" <?php checked( $custom_cursor, 1 ); ?> <?php echo $enabled ? '' : 'disabled="disabled"'; ?> />
                                            <span class="kss-slider"></span>
                                        </label>
                                        <span><?php esc_html_e( 'Custom Cursor', 'season-spark' ); ?></span>
                                    </div>

                                                <div style="margin-left:auto;">
                                                    <?php
                                                    // Only show color picker for effects that actually use color in their implementation
                                                    $color_effects = array( 'christmas', 'valentines', 'diwali', 'hanukkah' );
                                                    if ( $key === 'generic' ) : // show custom image fields for generic/custom graphics ?>
                                                        <div class="kss-media-wrap">
                                                            <input type="hidden" class="kss-generic-bg-input" name="kss_settings[effects][<?php echo esc_attr( $key ); ?>][custom_bg]" value="<?php echo esc_url( $eff['custom_bg'] ?? '' ); ?>" <?php echo $enabled ? '' : 'disabled="disabled"'; ?> />
                                                            <button type="button" class="button kss-media-btn kss-bg-btn" <?php echo $enabled ? '' : 'disabled="disabled"'; ?>><?php esc_html_e( 'Select Background', 'season-spark' ); ?></button>
                                                            <span class="kss-media-label kss-bg-label"><?php echo ! empty( $eff['custom_bg'] ) ? esc_html( wp_basename( $eff['custom_bg'] ) ) : ''; ?></span>
                                                            <button type="button" class="button kss-media-remove kss-bg-remove" style="<?php echo ! empty( $eff['custom_bg'] ) ? '' : 'display:none;'; ?>"><?php esc_html_e( 'Remove', 'season-spark' ); ?></button>
                                                        </div>
                                                        <div class="kss-media-wrap" style="margin-top:6px;">
                                                            <input type="hidden" class="kss-generic-cursor-input" name="kss_settings[effects][<?php echo esc_attr( $key ); ?>][custom_cursor_image]" value="<?php echo esc_url( $eff['custom_cursor_image'] ?? '' ); ?>" <?php echo $enabled ? '' : 'disabled="disabled"'; ?> />
                                                            <button type="button" class="button kss-media-btn kss-cursor-btn" style="<?php echo $custom_cursor ? '' : 'display:none;'; ?>" <?php echo ( $enabled && $custom_cursor ) ? '' : 'disabled="disabled"'; ?>><?php esc_html_e( 'Select Cursor', 'season-spark' ); ?></button>
                                                            <span class="kss-media-label kss-cursor-label" style="<?php echo ! empty( $eff['custom_cursor_image'] ) ? '' : 'display:none;'; ?>"><?php echo ! empty( $eff['custom_cursor_image'] ) ? esc_html( wp_basename( $eff['custom_cursor_image'] ) ) : ''; ?></span>
                                                            <button type="button" class="button kss-media-remove kss-cursor-remove" style="<?php echo ! empty( $eff['custom_cursor_image'] ) ? '' : 'display:none;'; ?>"><?php esc_html_e( 'Remove', 'season-spark' ); ?></button>
                                                        </div>
                                                    <?php elseif ( in_array( $key, $color_effects, true ) ) : ?>
                                                        <label class="kss-grid-label"><?php esc_html_e( 'Pick Color', 'season-spark' ); ?></label>
                                                        <input class="kss-color" name="kss_settings[effects][<?php echo esc_attr( $key ); ?>][color]" value="<?php echo esc_attr( $color ); ?>" <?php echo $enabled ? '' : 'disabled="disabled"'; ?> style="min-width:140px;" />
                                                    <?php else : ?>
                                                        <!-- This effect uses images only; color picker not shown -->
                                                        <span class="kss-help kss-no-color"><?php esc_html_e( 'Uses image assets — color not required.', 'season-spark' ); ?></span>
                                                    <?php endif; ?>
                                                </div>
                                </div>

                                <!-- schedule date block (shown when schedule toggle enabled) -->
                                <div class="kss-schedule-block" style="<?php echo empty( $eff['schedule'] ) ? 'display:none;' : ''; ?>;margin-top:10px;">
                                    <div class="kss-schedule-dates">
                                        <input class="kss-date kss-date-start" placeholder="YYYY-MM-DD" name="kss_settings[effects][<?php echo esc_attr( $key ); ?>][start]" value="<?php echo esc_attr( $start ); ?>" <?php echo ( $enabled && ! empty( $eff['schedule'] ) ) ? '' : 'disabled="disabled"'; ?> >
                                        <input class="kss-date kss-date-end" placeholder="YYYY-MM-DD" name="kss_settings[effects][<?php echo esc_attr( $key ); ?>][end]" value="<?php echo esc_attr( $end ); ?>" <?php echo ( $enabled && ! empty( $eff['schedule'] ) ) ? '' : 'disabled="disabled"'; ?> >
                                    </div>
                                    <p class="kss-help kss-schedule-help"><?php esc_html_e( 'Tip: leave dates blank to have the effect run year-round (for generic). Use low density on mobile for performance.', 'season-spark' ); ?></p>
                                </div>

                                <!-- per-card save removed -->
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    /**
     * Render Developer / Pro tips page
     */
    public function render_devs_page() {
        $readme_path = KSS_PLUGIN_DIR . 'README.md';
        $readme_md = '';
        if ( file_exists( $readme_path ) ) {
            $readme_md = file_get_contents( $readme_path );
        }

        // Simple markdown -> HTML converter (minimal, safe)
        $rendered = $this->simple_markdown_to_html( $readme_md );

        ?>
        <div class="wrap kss-wrap kss-dev-wrap">
            <?php $banner = KSS_PLUGIN_URL . 'assets/images/banner.png'; ?>
            <?php if ( $banner ) : ?>
                <img class="kss-header-banner" src="<?php echo esc_url( $banner ); ?>" alt="<?php esc_attr_e( 'Season Spark banner', 'season-spark' ); ?>" />
            <?php endif; ?>
            <h1 class="kss-title"><?php esc_html_e( 'Season Spark — Developer', 'season-spark' ); ?></h1>
            <p class="kss-sub"><?php esc_html_e( 'Documentation, API notes and examples.', 'season-spark' ); ?></p>
            <p class="kss-note"><?php esc_html_e( 'Season Spark focuses on being lightweight, modular and performant — only enabled effects load their scripts and assets. Use the filters below to extend behavior without impacting front-end performance.', 'season-spark' ); ?></p>

            <div class="kss-dev-tabs">
                <nav class="kss-tabnav">
                    <button class="kss-tab kss-tab-active" data-tab="doc"><?php esc_html_e( 'Documentation', 'season-spark' ); ?></button>
                    <button class="kss-tab" data-tab="api"><?php esc_html_e( 'API', 'season-spark' ); ?></button>
                    <button class="kss-tab" data-tab="examples"><?php esc_html_e( 'Examples', 'season-spark' ); ?></button>
                </nav>

                <div class="kss-tab-content kss-tab-active" data-content="doc">
                    <div class="kss-doc-inner">
                        <?php echo wp_kses_post( $rendered ); // sanitize allowed HTML from converter ?>
                    </div>
                </div>

                <div class="kss-tab-content" data-content="api">
                    <h2><?php esc_html_e( 'Filters & Hooks', 'season-spark' ); ?></h2>
                    <p><?php esc_html_e( 'Use these focused filters to customize behavior while keeping the front-end minimal:', 'season-spark' ); ?></p>
                    <ul>
                        <li><code>kss_get_registered_effects</code> — modify or extend the list of effects (return key => title).</li>
                        <li><code>kss_settings_for_js</code> — filter the settings object passed to front-end JS (`kssSettings`). Use this to adjust per-effect defaults server-side.</li>
                        <li><code>kss_images_for_js</code> — filter the image mapping (used for effect assets and the custom cursor feature).</li>
                    </ul>

                    <h3><?php esc_html_e( 'Data Shape', 'season-spark' ); ?></h3>
                    <p><?php esc_html_e( 'Front-end receives `kssSettings` with `effects` (per-effect cfg) and `global` keys. Per-effect object contains: `enabled`, `schedule` (0|1), `start`, `end`, `color`, `density`, `speed`, and `custom_cursor` (0|1). Images are available via the global `kssImages` map.', 'season-spark' ); ?></p>
                </div>

                <div class="kss-tab-content" data-content="examples">
                    <h2><?php esc_html_e( 'Registering an Effect', 'season-spark' ); ?></h2>
                    <pre><code>window.kssRegisterEffect('myeffect', function(ts, id, cfg){
    // ts.load(options).then(...) — initialize particles for container id
});</code></pre>

                    <h3><?php esc_html_e( 'Adding to registered effects', 'season-spark' ); ?></h3>
                    <pre><code>add_filter('kss_get_registered_effects', function($effects){
    $effects['myeffect'] = 'My Effect';
    return $effects;
});</code></pre>

                    <h3><?php esc_html_e( 'Extending image map (custom cursors)', 'season-spark' ); ?></h3>
                    <p><?php esc_html_e( 'Filter `kss_images_for_js` to add or override image URLs sent to the front-end (used by effects and the custom cursor mapping).', 'season-spark' ); ?></p>
                    <pre><code>add_filter('kss_images_for_js', function($images){
    $images['my_icon'] = plugin_dir_url( __FILE__ ) . '../assets/images/my-icon.svg';
    return $images;
});</code></pre>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Minimal, safe markdown to HTML converter for README display.
     * Supports headings, lists, paragraphs, code blocks and inline code.
     */
    private function simple_markdown_to_html( $md ) {
        if ( empty( $md ) ) {
            return '<p>' . esc_html__( 'No documentation found.', 'season-spark' ) . '</p>';
        }

        // Normalize line endings
        $md = str_replace( "\r\n", "\n", $md );

        // Escape HTML
        $md = esc_html( $md );

        // Convert code blocks ```
        $md = preg_replace_callback('/```(.*?)```/s', function($m){
            $code = trim($m[1]);
            return '<pre><code>' . $code . '</code></pre>';
        }, $md);

        // Convert headings
        $md = preg_replace('/^### (.*?)$/m', '<h3>$1</h3>', $md);
        $md = preg_replace('/^## (.*?)$/m', '<h2>$1</h2>', $md);
        $md = preg_replace('/^# (.*?)$/m', '<h1>$1</h1>', $md);

        // Convert unordered lists
        $md = preg_replace_callback('/(^|\n)(?:- |\* )(.*?)(?=\n|$)/s', function($m){
            $items = preg_split('/\n(?=- |\* )/', trim($m[0]));
            $out = "\n<ul>\n";
            foreach ($items as $it) {
                $it = preg_replace('/^- |^\* /', '', trim($it));
                $out .= '<li>' . $it . '</li>' . "\n";
            }
            $out .= "</ul>\n";
            return $out;
        }, $md);

        // Convert inline code `...`
        $md = preg_replace('/`([^`]+)`/', '<code>$1</code>', $md);

        // Paragraphs: split by double newlines
        $parts = preg_split('/\n\s*\n/', trim($md));
        $html = '';
        foreach ( $parts as $p ) {
            $p = trim($p);
            if ( preg_match('/^<h[1-3]>|^<ul>|^<pre>/', $p) ) {
                $html .= $p;
            } else {
                $html .= '<p>' . nl2br( $p ) . '</p>';
            }
        }

        return $html;
    }
}
