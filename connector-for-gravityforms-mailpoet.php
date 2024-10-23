<?php
/**
 * Plugin Name: Connector for Gravity Forms and MailPoet
 * Plugin URI: https://gravitywp.com/add-on/connector-for-gravityforms-mailpoet
 * Description: Integrate Gravity Forms with MailPoet to easily subscribe users to your MailPoet newsletters upon form submission.
 * Version: 1.0.1
 * Author: GravityWP
 * Author URI: https://gravitywp.com
 * License: GPL3
 * Text Domain: connector-for-gravityforms-mailpoet
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) || die();

define( 'GRAVITYWP_MP_GF_VERSION', '1.0.1' );

add_action( 'gform_loaded', array( 'GravityWP_Connector_MailPoet_GravityForms_AddOn_Bootstrap', 'load' ), 5 );

/**
 * GravityWP_Connector_MailPoet_GravityForms_AddOn_Bootstrap.
 *
 * @author GravityWP
 * @since v1.0
 *
 * @global
 */
class GravityWP_Connector_MailPoet_GravityForms_AddOn_Bootstrap {

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

		require_once 'class-gravitywp-connector-mailpoet-gravityforms.php';

		GFFeedAddOn::register( 'GravityWP\Connector_MailPoet_GravityForms\GravityWP_Connector_MailPoet_GravityForms' );
	}
}
