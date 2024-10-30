<?php

/*
  Plugin Name: Kads SEO
  Plugin URI: https://wordpress.org/plugins/kads-seo/
  Description: Use Kads SEO to optimize your WordPress site for SEO. It’s easy and works out of the box for beginners.
  Author: huynhduy1985
  Version: 1.3.7
  Author URI: https://www.kadrealestate.com/plugins/kads-seo/
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
function kseo_get_file_uri($path) {
    return plugins_url($path, __FILE__);
}

function kseo_get_file($path = '') {
    return plugin_dir_path( __FILE__ ) . $path;
}
require kseo_get_file('includes/params-meta.php');
require kseo_get_file('includes/params-social-settings.php');
require kseo_get_file('includes/params-general-settings.php');

require kseo_get_file('includes/kseo-functions.php');

require kseo_get_file('includes/kseo-controls-settings.php');
require kseo_get_file('includes/kseo-controls.php');
require kseo_get_file('includes/class-kseo-seo-settings.php');

/**
 * Main instance of Kads SEO.
 *
 * Returns the main instance of WC to prevent the need to use globals.
 *
 * @since  1.0
 * @return Kads SEO
 */
function kads_seo() {
    return KadsSeoSettings::instance();
}

// Global for backwards compatibility.
$GLOBALS['kseo'] = kads_seo();

require kseo_get_file('includes/class-kseo-seo.php');

/**
 * Activation hook
 * Create table if they don't exist and add plugin options
 */
function kseo_install() {
    global $wpdb;
    $version = '1.2.8';
    $old_version = get_option('_kseo_db_setup_version');
    if($old_version == $version)
    {
        return;
    }
    $tabledb = $wpdb->base_prefix . 'kads_seo';
    // Get the correct character collate
     $charset_collate = 'DEFAULT';
    $collate = '';
    if (!empty($wpdb->charset)) {
        $charset_collate .= " CHARSET=" . $wpdb->charset;
    }
    
    if (!empty($wpdb->collate)) {
        $charset_collate .= " COLLATE=" . $wpdb->collate;
        $collate = "COLLATE " . $wpdb->collate;
    }
    if ($wpdb->get_var('SHOW TABLES LIKE "' . $tabledb . '" ') != $tabledb) {
        // Setup chat message table
        $sql = "CREATE TABLE IF NOT EXISTS `" . $tabledb . "` (
                `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `ip` varchar(200) $collate NOT NULL DEFAULT '',
                PRIMARY KEY (`id`)
                ) ENGINE=MyISAM $charset_collate;";
        $wpdb->query($sql);
        update_option('_kseo_db_setup_version', $version);
    }
}

/**
 * Deactivation hook
 * Clear table
 */
function kseo_deactivation() {
    global $wpdb;
    $tabledb = $wpdb->base_prefix . 'kads_seo';
    $sql = 'TRUNCATE TABLE `' . $tabledb . '`';
    $wpdb->query($sql);
}

/**
 * Uninstall hook
 * Remove table and plugin options
 */
function kseo_uninstall() {
    global $wpdb;
    $tabledb = $wpdb->base_prefix . 'kads_seo';
    //remove table
    $sql = 'DROP TABLE IF EXISTS `' . $tabledb . '`';
    $wpdb->query($sql);
}

register_activation_hook(__FILE__,  'kseo_install');
register_deactivation_hook(__FILE__,'kseo_deactivation');
register_uninstall_hook(__FILE__, 'kseo_uninstall');
