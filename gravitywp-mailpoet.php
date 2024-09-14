<?php
/**
 * Plugin Name: GravityWP - Mailpoet Connector for Gravity Forms
 * Plugin URI: https://gravitywp.com/add-on/mailpoet
 * Description: Integrate Gravity Forms with Mailpoet to easily subscribe users to your Mailpoet newsletters upon form submission.
 * Version: 1.0
 * Author: GravityWP
 * Author URI: https://gravitywp.com
 * License: GPL3
 * Text Domain: gravitywp-mailpoet
 * Domain Path: /languages
 */

define( 'GWP_MAILPOET_VERSION', '1.0' );

add_action( 'gform_loaded', array( 'GWP_Mailpoet_AddOn_Bootstrap', 'load' ), 5 );

/**
 * GWP_Mailpoet_AddOn_Bootstrap.
 *
 * @author GravityWP
 * @since v1.0
 *
 * @global
 */
class GWP_Mailpoet_AddOn_Bootstrap {

	/**
	 * Function: load.
	 *
	 * @author GravityWP
	 * @version v1.0
	 * @access public
	 *
	 * @return void
	 */
	public static function load() {

		if ( ! method_exists( 'GFForms', 'include_feed_addon_framework' ) ) {
			return;
		}

		require_once 'class-gwp-mailpoet.php';

		GFFeedAddOn::register( 'GWP_Mailpoet' );
	}
}
