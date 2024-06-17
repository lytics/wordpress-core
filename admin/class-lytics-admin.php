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

		// Enqueue the Lytics Widget Wizard script with a unique handle and set as module
		wp_enqueue_script(
			$this->plugin_name . '-widget-wizard',
			plugin_dir_url(__FILE__) . 'js/lytics-widget-wizard.js',
			array('jquery'),
			$this->version,
			false
		);
		add_filter('script_loader_tag', function ($tag, $handle) {
			if ($handle === $this->plugin_name . '-widget-wizard') {
				return str_replace('<script ', '<script type="module" ', $tag);
			}
			return $tag;
		}, 10, 2);

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
			null // Callback function to render the page content
		);

		add_submenu_page(
			'lytics_settings', // Parent slug
			'Settings', // Page title
			'Settings', // Menu title
			'manage_options', // Capability required to access menu item
			'lytics_settings', // Menu slug
			array($this, 'lytics_settings_page') // Callback function to render the page content
		);

		add_submenu_page(
			'lytics_settings', // Parent slug
			'Widgets', // Page title
			'Widgets', // Menu title
			'manage_options', // Capability required to access menu item
			'edit.php?post_type=widget' // Menu slug
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

	public function register_widget_post_type()
	{
		$labels = array(
			'name'               => _x('Widgets', 'post type general name', 'lytics'),
			'singular_name'      => _x('Widget', 'post type singular name', 'lytics'),
			'menu_name'          => _x('Widgets', 'admin menu', 'lytics'),
			'name_admin_bar'     => _x('Widget', 'add new on admin bar', 'lytics'),
			'add_new'            => _x('Add New', 'widget', 'lytics'),
			'add_new_item'       => __('Add New Widget', 'lytics'),
			'new_item'           => __('New Widget', 'lytics'),
			'edit_item'          => __('Edit Widget', 'lytics'),
			'view_item'          => __('View Widget', 'lytics'),
			'all_items'          => __('All Widgets', 'lytics'),
			'search_items'       => __('Search Widgets', 'lytics'),
			'parent_item_colon'  => __('Parent Widgets:', 'lytics'),
			'not_found'          => __('No widgets found.', 'lytics'),
			'not_found_in_trash' => __('No widgets found in Trash.', 'lytics'),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => false, // We will add a submenu later
			'query_var'          => true,
			'rewrite'            => array('slug' => 'widget'),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array('title', 'editor'),
		);

		// Register custom post type
		register_post_type('widget', $args);

		// Remove Quick Edit link for the custom post type
		add_filter('post_row_actions', function ($actions) {
			global $post;

			if ($post->post_type === 'widget') {
				unset($actions['inline hide-if-no-js']);
			}

			return $actions;
		}, 10, 1);
	}

	public function add_widget_meta_boxes()
	{
		add_meta_box(
			'lytics_widget_meta',
			'Configuration',
			array($this, 'lytics_widget_meta_box'),
			'widget',
			'normal',
			'high'
		);
	}

	public function lytics_widget_meta_box($post)
	{
		// Add a nonce field so we can check for it later.
		wp_nonce_field('lytics_widget_meta_box', 'lytics_widget_meta_box_nonce');

		// Retrieve existing values from the database.
		$config_value = get_post_meta($post->ID, '_lytics_widget_configuration', true);
		$status_value = get_post_meta($post->ID, '_lytics_widget_status', true);
		$description_value = get_post_meta($post->ID, '_lytics_widget_description', true);

		// If no configuration is set, set a default value
		if (empty($config_value)) {
			$config_value = '';
		} else {
			$config_value = base64_encode($config_value);
		}

		// Output the fields.
		echo '<div>';
		echo '<div>';
		echo '<lytics-widgetwiz accountid="d54abf06a9ea7e2ab4ccd2dbaa55df2b" accesstoken="" pathforaconfig="' . $config_value . '" availableaudiences="W3sibGFiZWwiOiJBbGwiLCJ2YWx1ZSI6ImFsbCIsInR5cGUiOiJzdHJpbmcifSx7ImxhYmVsIjoiQW5vbnltb3VzIFByb2ZpbGVzIiwidmFsdWUiOiJhbm9ueW1vdXNfcHJvZmlsZXMiLCJ0eXBlIjoic3RyaW5nIn0seyJsYWJlbCI6IkFub255bW91cyBQcm9maWxlcyAtIDMwIGRheXMiLCJ2YWx1ZSI6ImFub255bW91c19wcm9maWxlc18zMF9kYXlzIiwidHlwZSI6InN0cmluZyJ9LHsibGFiZWwiOiJBbm9ueW1vdXMgUHJvZmlsZXMgLSA2MCBkYXlzIiwidmFsdWUiOiJhbm9ueW1vdXNfcHJvZmlsZXNfNjBfZGF5cyIsInR5cGUiOiJzdHJpbmcifSx7ImxhYmVsIjoiQW5vbnltb3VzIFByb2ZpbGVzIC0gOTAgZGF5cyIsInZhbHVlIjoiYW5vbnltb3VzX3Byb2ZpbGVzXzkwX2RheXMiLCJ0eXBlIjoic3RyaW5nIn0seyJsYWJlbCI6Iktub3duIFByb2ZpbGVzIiwidmFsdWUiOiJrbm93bl9wcm9maWxlcyIsInR5cGUiOiJzdHJpbmcifSx7ImxhYmVsIjoiTHl0aWNzIEN1cnJlbnRseSBFbmdhZ2VkIiwidmFsdWUiOiJzbXRfYWN0aXZlIiwidHlwZSI6InN0cmluZyJ9LHsibGFiZWwiOiJMeXRpY3MgRGlzZW5nYWdlZCIsInZhbHVlIjoic210X2Rvcm1hbnQiLCJ0eXBlIjoic3RyaW5nIn0seyJsYWJlbCI6Ikx5dGljcyBIaWdobHkgRW5nYWdlZCIsInZhbHVlIjoic210X3Bvd2VyIiwidHlwZSI6InN0cmluZyJ9LHsibGFiZWwiOiJMeXRpY3MgTmV3IiwidmFsdWUiOiJzbXRfbmV3IiwidHlwZSI6InN0cmluZyJ9LHsibGFiZWwiOiJMeXRpY3MgUHJldmlvdXNseSBFbmdhZ2VkIiwidmFsdWUiOiJzbXRfaW5hY3RpdmUiLCJ0eXBlIjoic3RyaW5nIn0seyJsYWJlbCI6Ikx5dGljcyBVbnNjb3JlZCIsInZhbHVlIjoic210X3Vuc2NvcmVkIiwidHlwZSI6InN0cmluZyJ9LHsibGFiZWwiOiJVbmhlYWx0aHkgUHJvZmlsZXMiLCJ2YWx1ZSI6ImRlZmF1bHRfdW5oZWFsdGh5X3Byb2ZpbGVzIiwidHlwZSI6InN0cmluZyJ9XQ=="availablecollections="W3sibGFiZWwiOiJBbGwgRG9jdW1lbnRzIiwidmFsdWUiOiJhbGxfZG9jdW1lbnRzIiwidHlwZSI6InN0cmluZyJ9LHsibGFiZWwiOiJDb250ZW50IFdpdGggSW1hZ2VzIiwidmFsdWUiOiJjb250ZW50X3dpdGhfaW1hZ2VzIiwidHlwZSI6InN0cmluZyJ9LHsibGFiZWwiOiJEZWZhdWx0IFJlY29tbWVuZGF0aW9uIENvbGxlY3Rpb24iLCJ2YWx1ZSI6ImRlZmF1bHRfcmVjb21tZW5kYXRpb25zIiwidHlwZSI6InN0cmluZyJ9LHsibGFiZWwiOiJFbmdsaXNoIENvbnRlbnQgV2l0aCBJbWFnZXMiLCJ2YWx1ZSI6ImVuZ2xpc2hfY29udGVudF93aXRoX2ltYWdlcyIsInR5cGUiOiJzdHJpbmcifV0=" titlefield="title" descriptionfield="lytics_widget_description" statusfield="lytics_widget_status" configurationfield="lytics_widget_configuration"></lytics-widgetwiz>';
		echo '</div>';
		echo '<textarea style="display:none;" id="lytics_widget_configuration" name="lytics_widget_configuration" rows="4" cols="50">' . esc_textarea($config_value) . '</textarea>';
		echo '<input type="text" style="display:none;" id="lytics_widget_status" name="lytics_widget_status" value="' . esc_attr($status_value) . '" size="25" />';
		echo '<textarea id="lytics_widget_description" style="display:none;" name="lytics_widget_description" rows="4" cols="50">' . esc_textarea($description_value) . '</textarea>';
		echo '</div>';
		echo '<script>document.querySelector(\'#titlediv\').style.display = \'none\';document.querySelector(\'.notice\').style.display = \'none\';</script>';
	}

	public function save_widget_meta_box_data($post_id)
	{
		if (!isset($_POST['lytics_widget_meta_box_nonce'])) {
			return $post_id;
		}

		$nonce = $_POST['lytics_widget_meta_box_nonce'];

		if (!wp_verify_nonce($nonce, 'lytics_widget_meta_box')) {
			return $post_id;
		}

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $post_id;
		}

		if ('page' === $_POST['post_type']) {
			if (!current_user_can('edit_page', $post_id)) {
				return $post_id;
			}
		} else {
			if (!current_user_can('edit_post', $post_id)) {
				return $post_id;
			}
		}

		if (isset($_POST['lytics_widget_description'])) {
			$new_config_value = sanitize_text_field($_POST['lytics_widget_description']);
			update_post_meta($post_id, 'lytics_widget_description', $new_config_value);
		}

		if (isset($_POST['lytics_widget_configuration'])) {
			$new_config_value = sanitize_text_field($_POST['lytics_widget_configuration']);
			update_post_meta($post_id, '_lytics_widget_configuration', $new_config_value);
		}

		if (isset($_POST['lytics_widget_status'])) {
			$new_status_value = sanitize_text_field($_POST['lytics_widget_status']);
			update_post_meta($post_id, '_lytics_widget_status', $new_status_value);
		}
	}

	public function register_lytics_recommendations_block()
	{
		// Register the block editor script.
		wp_register_script(
			'lytics-recommendations-editor-script',
			plugins_url('js/lytics-recommendations-block.js', __FILE__),
			array('wp-blocks', 'wp-element', 'wp-editor'),
			filemtime(plugin_dir_path(__FILE__) . 'js/lytics-recommendations-block.js')
		);

		// Register the block type.
		register_block_type('lytics/recommendations', array(
			'editor_script' => 'lytics-recommendations-editor-script',
		));
	}

	public function hide_default_widget_editor()
	{
		$post_type = 'widget';

		// Check if the current post type matches
		if (is_admin() && isset($_GET['post_type']) && $_GET['post_type'] === $post_type) {
			remove_post_type_support($post_type, 'editor');
		}

		// Additionally, check if the current post matches
		if (is_admin() && isset($_GET['post'])) {
			$post_id = intval($_GET['post']);
			$post = get_post($post_id);
			if ($post && $post->post_type === $post_type) {
				remove_post_type_support($post_type, 'editor');
			}
		}
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

	public function lytics_delete_settings()
	{
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
