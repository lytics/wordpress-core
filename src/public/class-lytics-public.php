<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://lytics.com
 * @since      1.0.0
 *
 * @package    Lytics
 * @subpackage Lytics/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Lytics
 * @subpackage Lytics/public
 * @author     Lytics <product@lytics.com>
 */
class Lytics_Public
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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/lytics-public.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		error_log(plugin_dir_url(__FILE__) . 'js/lytics-public.js');
		wp_enqueue_script($this->plugin_name . '-public', plugin_dir_url(__FILE__) . 'js/lytics-public.js', array('jquery'), $this->version, false);

		// Enqueue recommendation renderer
		wp_enqueue_script(
			$this->plugin_name . '-recommendation-renderer',
			plugin_dir_url(__FILE__) . 'js/lytics-recommendation-render.js',
			array('jquery'),
			$this->version,
			true
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
	}

	public function enqueue_tag_install()
	{
		$account_id = get_option('lytics_account_id');
		$tag_enabled = get_option('lytics_enable_tag');
		$custom_config = get_option('lytics_tag_config');
		$debug_mode = get_option('lytics_debug_mode');

		if (!$tag_enabled) {
			return;
		}

		if (!$account_id) {
			return;
		}

		if (!$custom_config) {
			$custom_config = '{}';
		}

		// decode the custom config json
		$custom_config = json_decode($custom_config, true);
		$js_file = $debug_mode ? 'latest.js' : 'latest.min.js';
		$custom_config['src'] = 'https://c.lytics.io/api/tag/' . $account_id . '/' . $js_file;

		$config = array(
			'config' => json_encode($custom_config)
		);

		wp_enqueue_script($this->plugin_name . '-tag-install', plugin_dir_url(__FILE__) . 'js/lytics-tag-install.js', array(), $this->version, false);


		wp_localize_script($this->plugin_name . '-tag-install', 'lytics_tag_vars', $config);
	}

	public function enqueue_published_widgets()
	{
		include plugin_dir_path(__FILE__) . 'partials/lytics-public-display.php';
	}

	public function lytics_greeting_shortcode()
	{
		return 'Hello, World!';
	}
}
