<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://aliayubi.com
 * @since      1.0.0
 *
 * @package    Gdpr_ultimate
 * @subpackage Gdpr_ultimate/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Gdpr_ultimate
 * @subpackage Gdpr_ultimate/includes
 * @author     Ali Ayubi <info@aliayubi.com>
 */
class Gdpr_ultimate_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'gdpr_ultimate',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
