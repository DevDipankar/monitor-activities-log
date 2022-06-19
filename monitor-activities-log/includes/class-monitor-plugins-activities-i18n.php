<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://dipankar-team.business.site
 * @since      1.0.0
 *
 * @package    Monitor_Plugins_Activities
 * @subpackage Monitor_Plugins_Activities/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Monitor_Plugins_Activities
 * @subpackage Monitor_Plugins_Activities/includes
 * @author     Dipankar Pal <dipankarpal212@gmail.com>
 */
class Monitor_Plugins_Activities_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'monitor-plugins-activities',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
