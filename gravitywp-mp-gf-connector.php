<?php
/**
 * Plugin Name: Connector for Gravity Forms and MailPoet
 * Plugin URI: https://gravitywp.com/add-on/connector-for-gravity-forms-and-mailpoet
 * Description: Integrate Gravity Forms with Mailpoet to easily subscribe users to your Mailpoet newsletters upon form submission.
 * Version: 1.0
 * Author: GravityWP
 * Author URI: https://gravitywp.com
 * License: GPL3
 * Text Domain: gravitywp-mp-gf-connector
 * Domain Path: /languages
 */

define( 'GWP_MP_GF_CONNECTOR_VERSION', '1.0' );

add_action( 'gform_loaded', array( 'GWP_MP_GF_Connector_AddOn_Bootstrap', 'load' ), 5 );

/**
 * GWP_MP_GF_Connector_AddOn_Bootstrap.
 *
 * @author GravityWP
 * @since v1.0
 *
 * @global
 */
class GWP_MP_GF_Connector_AddOn_Bootstrap {

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

		GFFeedAddOn::register( 'GWP_MP_GF_Connector' );
	}
}
