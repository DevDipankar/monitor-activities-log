<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://dipankar-team.business.site
 * @since             1.0.0
 * @package           Monitor Activities Log
 *
 * @wordpress-plugin
 * Plugin Name:       Monitor Activities Log
 * Plugin URI:        domain.com
 * Description:       Get notified with all plugin activities inside your applicatin. In one place you can track plugin activities by the users such as activation, deactivation, installation, deletion and more.
 * Version:           1.0.0
 * Author:            Dipankar Pal
 * Author URI:        https://dipankar-team.business.site
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       monitor-activities-log
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'MONITOR_PLUGINS_ACTIVITIES_VERSION', '1.0.0' );
define( 'MPA_PLUGIN_SLUG', 'monitor-activities-log' );
define( 'MPA_TEXTDOMAIN', 'monitoractivitieslog' );
define( 'MPA_NAME', 'monitor_activities_log' );
define( 'MPA_PATH', plugin_dir_path( __FILE__ ) );

$plugin = plugin_basename( __FILE__ );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-monitor-plugins-activities-activator.php
 */
function activate_monitor_plugins_activities() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-monitor-plugins-activities-activator.php';
	Monitor_Plugins_Activities_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-monitor-plugins-activities-deactivator.php
 */
function deactivate_monitor_plugins_activities() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-monitor-plugins-activities-deactivator.php';
	Monitor_Plugins_Activities_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_monitor_plugins_activities' );
register_deactivation_hook( __FILE__, 'deactivate_monitor_plugins_activities' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-monitor-plugins-activities.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_monitor_plugins_activities() {

	$plugin = new Monitor_Plugins_Activities();
	$plugin->run();

}
run_monitor_plugins_activities();

