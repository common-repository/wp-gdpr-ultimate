<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://aliayubi.com
 * @since      1.0.0
 *
 * @package    Gdpr_ultimate
 * @subpackage Gdpr_ultimate/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Gdpr_ultimate
 * @subpackage Gdpr_ultimate/public
 * @author     Ali Ayubi <info@aliayubi.com>
 */
class Gdpr_ultimate_Public {

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
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Gdpr_ultimate_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Gdpr_ultimate_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/gdpr_ultimate-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( "pnotify-css", plugin_dir_url( __FILE__ ) . 'css/pnotify.custom.min.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Gdpr_ultimate_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Gdpr_ultimate_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/gdpr_ultimate-public.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( "pnotify-js", plugin_dir_url( __FILE__ ) . 'js/pnotify.custom.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( "jquery-validate", plugin_dir_url( __FILE__ ) . 'js/jquery.validate.min.js', array( 'jquery' ), $this->version, false );

	}

}
