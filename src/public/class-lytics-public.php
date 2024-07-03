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

	public function enqueue_published_widgets()
	{
		include plugin_dir_path(__FILE__) . 'partials/lytics-public-display.php';
	}

	public function lytics_greeting_shortcode()
	{
		return 'Hello, World!';
	}
}
