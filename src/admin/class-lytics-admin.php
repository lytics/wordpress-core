<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://lytics.com
 * @since      1.0.0
 *
 * @package    LyticsWP
 * @subpackage Lytics/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    LyticsWP
 * @subpackage Lytics/admin
 * @author     Lytics <product@lytics.com>
 */
class Lytics_WP_Admin
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

		// Enqueue recommendation renderer
		wp_enqueue_script(
			$this->plugin_name . '-recommendation-renderer',
			plugin_dir_url(__FILE__) . 'js/lytics-recommendation-render.js',
			array('jquery'),
			$this->version,
			false
		);

		add_filter('script_loader_tag', function ($tag, $handle) {
			if ($handle === $this->plugin_name . '-widget-wizard') {
				return str_replace('<script ', '<script type="module" ', $tag);
			}
			if ($handle === $this->plugin_name . '-recommendation-renderer') {
				return str_replace('<script ', '<script type="module" ', $tag);
			}

			return $tag;
		}, 10, 2);


		// Bootstrap CSS
		wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', array(), '5.3.3', false);


		// CodeMirror CSS
		wp_enqueue_style('codemirror-css', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/codemirror.min.css', array(), '5.62.0', false);
		wp_enqueue_style('codemirror-theme-css', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/theme/monokai.min.css', array(), '5.62.0', false);

		// Bootstrap JS
		wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array(), '5.3.3', true);

		// CodeMirror JS
		wp_enqueue_script('codemirror-js', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/codemirror.min.js', array(), '5.62.0', true);
		wp_enqueue_script('codemirror-json-js', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.62.0/mode/javascript/javascript.min.js', array(), '5.62.0', true);
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
			null, // Callback function to render the page content
			'data:image/svg+xml;base64,' . base64_encode('<svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.99186 9.28415C5.12491 9.28415 4.28416 8.83211 3.82338 8.0263C3.13986 6.8296 3.55478 5.30533 4.75148 4.62181L9.97505 1.63442C11.1718 0.950898 12.696 1.36581 13.3795 2.56252C14.0631 3.75922 13.6482 5.28349 12.4514 5.96919L7.23006 8.9544C6.83916 9.17715 6.41333 9.28415 5.99186 9.28415Z" fill="white"/><path d="M5.99186 16.4231C5.12491 16.4231 4.28416 15.971 3.82338 15.1652C3.13986 13.9685 3.55478 12.4443 4.75148 11.7607L9.97287 8.77553C11.1696 8.09201 12.6938 8.50693 13.3774 9.70363C14.0631 10.8982 13.6481 12.4246 12.4514 13.1081L7.23006 16.0933C6.83916 16.3183 6.41333 16.4231 5.99186 16.4231Z" fill="white"/><path d="M10.2848 21.2287C9.41788 21.2287 8.57713 20.7767 8.11635 19.9709C7.43283 18.7742 7.84775 17.2499 9.04445 16.5664L14.2658 13.5812C15.4625 12.8977 16.9868 13.3126 17.6703 14.5093C18.3539 15.706 17.9389 17.2303 16.7422 17.9138L11.5208 20.899C11.1299 21.1217 10.7041 21.2287 10.2848 21.2287Z" fill="white"/></svg>'),
			56, // Menu position
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

		// Verify the nonce
		if (!isset($_POST['lytics_config_form_nonce']) || !check_admin_referer('lytics_config_form_action', 'lytics_config_form_nonce')) {
			wp_die('Nonce verification failed');
		}

		// If that we have an access token
		if (empty($_POST['access_token'])) {
			$nonce = wp_create_nonce('lytics_settings_save_nonce');
			wp_redirect(add_query_arg(array(
				'error' => 'Access Token is required.',
				'lytics_settings_save_nonce' => $nonce
			), admin_url('admin.php?page=lytics_settings')));
			exit;
		}

		// Sanitize inputs
		$access_token = sanitize_text_field($_POST['access_token']);
		$enable_tag = isset($_POST['enable_tag']) ? 1 : 0;
		$debug_mode = isset($_POST['debug_mode']) ? 1 : 0;
		$ignore_admin_users = isset($_POST['ignore_admin_users']) ? 1 : 0;
		$tag_config = sanitize_text_field($_POST['tag_config']);

		// Validate that access token is valid
		$accountDetails = $this->fetchAccountDetails($access_token);
		if (!$accountDetails) {
			wp_redirect(add_query_arg('error', 'Invalid Access Token.', admin_url('admin.php?page=lytics_settings')));
			exit;
		}

		// Update settings
		update_option('lytics_access_token', $access_token);
		update_option('lytics_account_name', $accountDetails['name']);
		update_option('lytics_account_id', $accountDetails['id']);
		update_option('lytics_aid', $accountDetails['aid']);
		update_option('lytics_domain', $accountDetails['domain']);
		update_option('lytics_enable_tag', $enable_tag);
		update_option('lytics_debug_mode', $debug_mode);
		update_option('lytics_ignore_admin_users', $ignore_admin_users);
		update_option('lytics_tag_config', $tag_config);

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

			// Check if $post is an object and if its post_type property equals 'widget'
			if (is_object($post) && $post->post_type === 'widget') {
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

		// Get stored settings
		$account_id = get_option('lytics_account_id');
		// error_log('Account ID: ' . $account_id);
		$access_token = get_option('lytics_access_token');
		// error_log('Token: ' . $access_token);
		$engines = $this->getInterestEngines();
		// error_log('Engines: ' . wp_json_encode($engines));
		$collections = $this->getAudiences('content', false);
		// error_log('Collections: ' . wp_json_encode($collections));
		$segments = $this->getAudiences('user', true);
		// error_log('Segments: ' . wp_json_encode($segments));

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
		echo '<lytics-widgetwiz accountid="' . esc_attr($account_id) . '" accesstoken="' . esc_attr($access_token) . '" pathforaconfig="' . esc_attr($config_value) . '" availableaudiences="' . esc_attr(base64_encode(wp_json_encode($segments))) . '"availablecollections="' . esc_attr(base64_encode(wp_json_encode($collections))) . '" titlefield="title" descriptionfield="lytics_widget_description" statusfield="lytics_widget_status" configurationfield="lytics_widget_configuration"></lytics-widgetwiz>';
		echo '</div>';
		echo '<textarea style="display:none;" id="lytics_widget_configuration" name="lytics_widget_configuration" rows="4" cols="50">' . esc_textarea($config_value) . '</textarea>';
		echo '<input type="text" style="display:none;" id="lytics_widget_status" name="lytics_widget_status" value="' . esc_attr($status_value) . '" size="25" />';
		echo '<textarea id="lytics_widget_description" style="display:none;" name="lytics_widget_description" rows="4" cols="50">' . esc_textarea($description_value) . '</textarea>';
		echo '</div>';
		echo '<script>document.querySelector(\'#post-body-content\').style.display = \'none\';document.querySelector(\'.notice\').style.display = \'none\';</script>';
	}

	public function save_widget_meta_box_data($post_id)
	{
		if (!isset($_POST['lytics_widget_meta_box_nonce'])) {
			return $post_id;
		}

		if (!wp_verify_nonce($_POST['lytics_widget_meta_box_nonce'], 'lytics_widget_meta_box')) {
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

	public function getAudiences($table, $onlyPublic)
	{
		$apitoken = get_option('lytics_access_token');
		if (empty($apitoken)) {
			return [
				[
					'label' => esc_html__('Uh oh, no token found!', 'text-domain'),
					'value' => 'oopsie',
					'type' => 'string'
				]
			];
		}

		$url = "https://api.lytics.io/v2/segment?table=" . esc_attr($table) . "&valid=true&kind=segment";

		$response = wp_remote_get($url, array(
			'headers' => array(
				'Authorization' => $apitoken,
			),
		));

		if (is_wp_error($response)) {
			error_log("Error: " . $response->get_error_message());
			return [];
		}

		$httpCode = wp_remote_retrieve_response_code($response);
		if ($httpCode >= 400) {
			error_log("Audience(" . $table . ") request failed with status: " . wp_remote_retrieve_body($response));
			return [];
		}

		$body = wp_remote_retrieve_body($response);
		$data = json_decode($body, true);

		if ($data === null || !isset($data['data']) || !is_array($data['data'])) {
			error_log("Unexpected response format");
			return [];
		}

		$allSegments = $data['data'];
		$publicSegments = array_filter($allSegments, function ($segment) {
			return $segment['is_public'] === true;
		});

		$filteredSegments = [];
		if ($onlyPublic) {
			$filteredSegments = $publicSegments;
		} else {
			$filteredSegments = $allSegments;
		}

		$options = array_map(function ($segment) {
			return array(
				'label' => esc_html($segment['name']),
				'value' => esc_attr($segment['slug_name']),
				'type' => 'string'
			);
		}, $filteredSegments);

		usort($options, function ($a, $b) {
			return strcmp($a['label'], $b['label']);
		});

		return $options;
	}


	public function getInterestEngines()
	{
		$apitoken = get_option('lytics_access_token');
		if (empty($apitoken)) {
			error_log("No access token found");
			return [];
		}

		$url = "https://api.lytics.io/api/content/config";

		$response = wp_remote_get($url, array(
			'headers' => array(
				'Authorization' => $apitoken,
			),
		));

		if (is_wp_error($response)) {
			error_log("Error: " . $response->get_error_message());
			return [];
		}

		$httpCode = wp_remote_retrieve_response_code($response);
		if ($httpCode >= 400) {
			error_log("Engine request failed with status: " . wp_remote_retrieve_body($response));
			return [];
		}

		$data = json_decode(wp_remote_retrieve_body($response), true);

		if ($data === null || !isset($data['data']) || !is_array($data['data'])) {
			error_log("Unexpected response format");
			return [];
		}

		$allEngines = $data['data'];

		$options = [];
		foreach ($allEngines as $engine) {
			$options[$engine['id']] = array(
				'label' => $engine['label'],
				'value' => $engine['id'],
			);
		}

		usort($options, function ($a, $b) {
			return strcmp($a['label'], $b['label']);
		});

		return $options;
	}


	public function register_lytics_recommendations_block()
	{
		// Get configuration settings
		$account_id = get_option('lytics_account_id');
		// error_log('Account ID: ' . $account_id);

		// Get available interest engines
		$engines = $this->getInterestEngines();
		// error_log('Engines: ' . wp_json_encode($engines));

		// Get available content collections
		$collections = $this->getAudiences('content', false);
		// error_log('Collections: ' . wp_json_encode($collections));

		// Register the block editor script.
		wp_register_script(
			'lytics-recommendations-editor-script',
			plugins_url('js/lytics-recommendations-block.js', __FILE__),
			array('wp-blocks', 'wp-element', 'wp-components', 'wp-i18n', 'wp-editor'),
			filemtime(plugin_dir_path(__FILE__) . 'js/lytics-recommendations-block.js'),
			true
		);

		// Localize the script with new data.
		wp_localize_script(
			'lytics-recommendations-editor-script',
			'lyticsBlockData',
			array(
				'account_id' => $account_id,
				'content_collections' => $collections,
				'engines' => $engines,
			)
		);

		add_filter('script_loader_tag', function ($tag, $handle) {
			if ($handle === 'lytics-recommendations-editor-script') {
				return str_replace('<script ', '<script type="module" ', $tag);
			}
			return $tag;
		}, 10, 2);

		// Register the block type.
		register_block_type('lytics/recommendations', array(
			'editor_script' => 'lytics-recommendations-editor-script',
		));

		wp_enqueue_style('lytics-recommendations-editor-style', plugins_url('../assets/lytics-recommendations-block.css', __FILE__), array(), '1.0.0');
	}

	public function hide_default_widget_editor()
	{
		$target_post_type = 'widget';

		if (!wp_verify_nonce($_POST['lytics_ignore_nonce'], 'lytics_ignore_nonce')) {
			// do nothing since this param comes from wordpress natively
		}

		// get the post type and set it to a valid string
		$post_type = isset($_GET['post_type']) ? sanitize_text_field($_GET['post_type']) : 'unknown';


		// Check if the current post type matches
		if (is_admin() && $post_type === $target_post_type) {
			remove_post_type_support($target_post_type, 'editor');
		}

		// Additionally, check if the current post matches
		if (is_admin() && isset($_GET['post'])) {
			$post_id = intval($_GET['post']);
			$post = get_post($post_id);
			if ($post && $post->post_type === $target_post_type) {
				remove_post_type_support($target_post_type, 'editor');
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
			wp_die(esc_html__('You do not have sufficient permissions to access this page.'));
		}
	}

	public function lytics_delete_settings()
	{
		// Check if user has permission to delete settings
		if (!current_user_can('manage_options')) {
			wp_die(esc_html__('You do not have sufficient permissions to access this page.'));
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
