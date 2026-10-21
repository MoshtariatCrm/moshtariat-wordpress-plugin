<?php
/**
 * Plugin Name: Moshtariat CRM Integration
 * Plugin URI: https://github.com/MoshtariatCrm/moshtariat-wordpress-plugin
 * Description: Connect WordPress forms (Contact Form 7, Gravity Forms, WPForms) and WooCommerce orders directly to Moshtariat CRM (سی ار ام ابری مشتریات) pipeline.
 * Version: 1.0.0
 * Author: Moshtariat
 * Author URI: https://moshtariat.ir
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: moshtariat-crm
 * Domain Path: /languages
 * Requires at least: 5.6
 * Requires PHP: 7.4
 */

defined('ABSPATH') || exit;

define('MOSHTARIAT_CRM_VERSION', '1.0.0');
define('MOSHTARIAT_CRM_PATH', plugin_dir_path(__FILE__));
define('MOSHTARIAT_CRM_URL', plugin_dir_url(__FILE__));

require_once MOSHTARIAT_CRM_PATH . 'includes/class-moshtariat-api.php';
require_once MOSHTARIAT_CRM_PATH . 'includes/class-moshtariat-admin.php';
require_once MOSHTARIAT_CRM_PATH . 'includes/class-moshtariat-hooks.php';

/**
 * Initialize Moshtariat CRM plugin.
 */
function moshtariat_crm_init() {
    $api   = new Moshtariat_CRM_API();
    $admin = new Moshtariat_CRM_Admin($api);
    $hooks = new Moshtariat_CRM_Hooks($api);

    $admin->init();
    $hooks->init();
}
add_action('plugins_loaded', 'moshtariat_crm_init');

/**
 * Plugin action links.
 */
function moshtariat_crm_action_links($links) {
    $settings_link = '<a href="' . esc_url(admin_url('admin.php?page=moshtariat-crm')) . '">' . esc_html__('تنظیمات', 'moshtariat-crm') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'moshtariat_crm_action_links');
