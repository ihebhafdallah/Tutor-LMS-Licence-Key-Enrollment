<?php

/**
 *
 * @link              https://ihebhafdallah.com
 * @since             1.1.2
 *
 * Plugin Name:       Tutor LMS Licence Key Enrollment
 * Description:       Tutor LMS Licence Key Enrollment lets users enroll in courses using a licence key. Admins generate course licence keys and share them with users, who can submit their key via a frontend form using the shortcode <code>[tutor_licence_key]</code> to get enrolled instantly.
 * Version:           1.1.2
 * Author:            Iheb HAFDALLAH
 * Author URI:        https://ihebhafdallah.com/
 * Text Domain:       tutor-lms-licence-key-enrollment
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (! defined('ABSPATH')) exit;

register_activation_hook(__FILE__, 'tutor_licence_key_create_table');

function tutor_licence_key_create_table()
{
	global $wpdb;

	$table_name = $wpdb->prefix . 'tutor_licence_keys';
	$charset_collate = $wpdb->get_charset_collate();

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        course_id BIGINT(20) UNSIGNED NOT NULL,
        licence_key VARCHAR(191) NOT NULL,
        status ENUM('active','sent','expired') NOT NULL DEFAULT 'active',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY licence_key (licence_key)
    ) $charset_collate;";

	dbDelta($sql);
}

require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';

require_once plugin_dir_path(__FILE__) . 'includes/class-admin.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-licence-key-manager.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-licence-key-table.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-licence-key-shortcode.php';

add_action('plugins_loaded', function () {
	new Tutor_Licence_Key_Admin();
    new Tutor_Licence_Key_Shortcode();
    load_plugin_textdomain('tutor-lms-licence-key-enrollment', false, dirname(plugin_basename(__FILE__)) . '/languages/');
});
