<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

/**
 * Clean up plugin options when plugin is uninstalled
 */
delete_option( 'kss_settings' );

// Optionally remove transients or other database entries if created in future
