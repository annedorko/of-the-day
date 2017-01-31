<?php

/**
 * @link              https://github.com/annedorko/of-the-day/
 * @since             1.0.0
 * @package           Of_The_Day
 *
 * @of-the-day
 * Plugin Name:       Of The Day
 * Plugin URI:        https://github.com/annedorko/of-the-day/
 * Description:       Allows you to automatically feature a random post of the day, for any post type with optional filters with any taxonomy.
 * Version:           0.1.0
 * Author:            Anne Dorko
 * Author URI:        https://www.annedorko.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       of-the-day
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-of-the-day-activator.php
 */
function activate_of_the_day() {
	include_once plugin_dir_path( __FILE__ ) . 'includes/class-of-the-day-activator.php';
	Of_The_Day_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-of-the-day-deactivator.php
 */
function deactivate_of_the_day() {
	include_once plugin_dir_path( __FILE__ ) . 'includes/class-of-the-day-deactivator.php';
	Of_The_Day_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_of_the_day' );
register_deactivation_hook( __FILE__, 'deactivate_of_the_day' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-of-the-day.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 1.0.0
 */
function run_of_the_day() {

	$plugin = new Of_The_Day();
	$plugin->run();

}
run_of_the_day();
