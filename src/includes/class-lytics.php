<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 */

class Lyticswp
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 */
	protected $version;

	/**
	 * Core plugin class constructor.
	 */
	public function __construct()
	{
		if (defined('LYTICSWP_VERSION')) {
			$this->version = LYTICSWP_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'lytics-wp';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 */
	private function load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-lytics-loader.php';

		/**
		 * The class responsible for defining internationalization functionality.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-lytics-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-lytics-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-lytics-public.php';

		$this->loader = new Lyticswp_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Lyticswp_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 */
	private function set_locale()
	{

		$plugin_i18n = new Lyticswp_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality.
	 */
	private function define_admin_hooks()
	{

		$plugin_admin = new Lyticswp_Admin($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		$this->loader->add_action('admin_menu', $plugin_admin, 'add_main_menu');
		$this->loader->add_filter('plugin_action_links_lytics/' . $this->get_plugin_name() . '.php', $plugin_admin, 'lyticswp_settings_link');
		$this->loader->add_action('admin_post_lytics_process_form', $plugin_admin, 'lyticswp_settings_handle_form_submission');
		$this->loader->add_action('admin_init', $plugin_admin, 'register_lyticswp_settings');
		$this->loader->add_action('admin_post_delete_lyticswp_settings', $plugin_admin, 'delete_lyticswp_settings');

		// Widget
		$this->loader->add_action('init', $plugin_admin, 'register_lytics_widget_post_type');
		$this->loader->add_action('init', $plugin_admin, 'hide_default_lytics_widget_editor');
		$this->loader->add_action('add_meta_boxes', $plugin_admin, 'add_lytics_widget_meta_box');
		$this->loader->add_action('save_post', $plugin_admin, 'save_lytics_widget_meta_box_data');

		// Gutenberg
		$this->loader->add_action('init', $plugin_admin, 'register_lytics_recommendation_block');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality.
	 */
	private function define_public_hooks()
	{

		$plugin_public = new Lyticswp_Public($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_published_widgets');
		$this->loader->add_shortcode('lytics_greeting', $plugin_public, 'render_greeting_shortcode');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of WordPress and to define internationalization functionality.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}
}
