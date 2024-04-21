<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://lytics.com
 * @since      1.0.0
 *
 * @package    Lytics
 * @subpackage Lytics/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Lytics
 * @subpackage Lytics/admin
 * @author     Lytics <product@lytics.com>
 */
class Lytics_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Lytics_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Lytics_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/lytics-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/lytics-admin.js', array('jquery'), $this->version, false);

		// Bootstrap CSS
		wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');

		// CodeMirror CSS
		wp_enqueue_style('codemirror-css', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/codemirror.min.css');
		wp_enqueue_style('codemirror-theme-css', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/theme/monokai.min.css');

		// Bootstrap JS
		wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js');

		// CodeMirror JS
		wp_enqueue_script('codemirror-js', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/codemirror.min.js');
		wp_enqueue_script('codemirror-json-js', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/mode/javascript/javascript.min.js');
	}

	/**
	 * Administrative menus for the plugin
	 */
	public function lytics_add_menu()
	{
		add_menu_page(
			'Lytics', // Page title
			'Lytics', // Menu title
			'manage_options', // Capability required to access menu item
			'lytics_settings', // Menu slug
			array($this, 'lytics_settings_page'), // Callback function to render the page content
		);

		add_submenu_page(
			'lytics_settings', // Parent slug
			'Settings', // Page title
			'Settings', // Menu title
			'manage_options', // Capability required to access menu item
			'lytics_settings', // Menu slug
			array($this, 'lytics_settings_page') // Callback function to render the page content
		);
	}

	public function lytics_plugin_settings_link($links)
	{
		$settings_link = '<a href="admin.php?page=lytics_settings">Settings</a>';
		array_unshift($links, $settings_link);
		return $links;
	}

	public function lytics_settings_page()
	{
		include plugin_dir_path(__FILE__) . 'partials/lytics-admin-display.php';
	}

	public function lytics_handle_form_submission()
	{
		if (!current_user_can('manage_options')) {
			wp_die('Unauthorized user');
		}

		// If that we have an access token
		if (empty($_POST['access_token'])) {
			wp_redirect(add_query_arg('error', 'Access Token is required.', admin_url('admin.php?page=lytics_settings')));
			exit;
		}

		// Validate that access token is valid
		$accountDetails = $this->fetchAccountDetails($_POST['access_token']);
		if (!$accountDetails) {
			wp_redirect(add_query_arg('error', 'Invalid Access Token.', admin_url('admin.php?page=lytics_settings')));
			exit;
		}

		// Update settings
		update_option('lytics_access_token', $_POST['access_token']);
		update_option('lytics_account_name', $accountDetails['name']);
		update_option('lytics_account_id', $accountDetails['id']);
		update_option('lytics_aid', $accountDetails['aid']);
		update_option('lytics_domain', $accountDetails['domain']);
		update_option('lytics_enable_tag', isset($_POST['enable_tag']) ? 1 : 0);
		update_option('lytics_debug_mode', isset($_POST['debug_mode']) ? 1 : 0);
		update_option('lytics_ignore_admin_users', isset($_POST['ignore_admin_users']) ? 1 : 0);
		update_option('lytics_tag_config', $_POST['tag_config']);

		// Redirect back to the settings page
		wp_redirect(admin_url('admin.php?page=lytics_settings'));
		exit;
	}

	private function fetchAccountDetails($token)
	{
		if (empty($token)) {
			return FALSE;
		}

		$url = 'https://api.lytics.io/api/account';
		$args = array(
			'headers' => array(
				'Authorization' => $token,
			),
		);

		$response = wp_remote_get($url, $args);

		if (is_wp_error($response)) {
			error_log('Failed to fetch account details: ' . $response->get_error_message());
			return FALSE;
		}

		$response_code = wp_remote_retrieve_response_code($response);
		$body = wp_remote_retrieve_body($response);

		if ($response_code === 200) {
			$data = json_decode($body, TRUE);
			if (isset($data['data']) && count($data['data']) > 0 && isset($data['data'][0]['name'], $data['data'][0]['id'], $data['data'][0]['domain'])) {
				return [
					'name' => $data['data'][0]['name'],
					'id' => $data['data'][0]['id'],
					'domain' => $data['data'][0]['domain'],
					'aid' => $data['data'][0]['aid'],
					'packages' => $data['data'][0]['packages'],
				];
			}
		} else {
			error_log('Failed to fetch account details. Response code: ' . $response_code);
			return FALSE;
		}

		return FALSE;
	}

	public function lytics_register_settings()
	{
		register_setting('lytics_settings_group', 'lytics_access_token');
		register_setting('lytics_settings_group', 'lytics_enable_tag');
		register_setting('lytics_settings_group', 'lytics_debug_mode');
		register_setting('lytics_settings_group', 'lytics_ignore_admin_users');
		register_setting('lytics_settings_group', 'lytics_tag_config');
		register_setting('lytics_settings_group', 'lytics_account_name');
		register_setting('lytics_settings_group', 'lytics_account_id');
		register_setting('lytics_settings_group', 'lytics_aid');
		register_setting('lytics_settings_group', 'lytics_domain');
	}

	public function lytics_reset_settings()
	{
		if (!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
	}

	public function lytics_delete_settings() {
		// Check if user has permission to delete settings
		if (!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}

		// Delete all options related to your plugin
		delete_option('lytics_access_token');
		delete_option('lytics_account_name');
		delete_option('lytics_account_id');
		delete_option('lytics_aid');
		delete_option('lytics_domain');
		delete_option('lytics_enable_tag');
		delete_option('lytics_debug_mode');
		delete_option('lytics_ignore_admin_users');
		delete_option('lytics_tag_config');

		wp_redirect(admin_url('admin.php?page=lytics_settings'));
		exit;
	}
}