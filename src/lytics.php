<?php

/**
 * Lytics core WordPres plugin file
 *
 * @link              https://lytics.com
 * @since             1.0.0
 * @package           Lytics
 *
 * @wordpress-plugin
 * Plugin Name:       Lytics
 * Plugin URI:        https://https://wordpress.com/plugins/lytics
 * Description:       The Lytics WordPress Plugin seamlessly integrates your website with the Lytics Customer Data Platform (CDP). This integration empowers site administrators to leverage extensive visitor data and personalization capabilities, enhancing user experiences and building a robust first-party data asset within their WordPress site.
 * Version:           1.0.0
 * Author:            Lytics
 * Author URI:        https://lytics.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       lytics
 * Domain Path:       /languages
 */

// Prevent file from being called directly
if (!defined('WPINC')) {
	die;
}

/**
 * Current plugin version.
 */
define('LYTICS_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-lytics-activator.php
 */
function activate_lytics()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-lytics-activator.php';
	Lytics_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-lytics-deactivator.php
 */
function deactivate_lytics()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-lytics-deactivator.php';
	Lytics_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_lytics');
register_deactivation_hook(__FILE__, 'deactivate_lytics');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-lytics.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_lytics()
{

	$plugin = new Lytics();
	$plugin->run();
}
run_lytics();
