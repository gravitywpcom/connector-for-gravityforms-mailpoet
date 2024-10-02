<?php
/**
 * GWP_MP_GF_Connector
 *
 * @package GravityWP\Mailpoet
 */

class_exists( 'GFForms' ) || die();

GFForms::include_feed_addon_framework();


/**
 * Class GWP_MP_GF_Connector
 *
 * @author GravityWP
 * @since v1.0.0
 * @see GFAddOn
 *
 * @global
 */
class GWP_MP_GF_Connector extends GFFeedAddOn {

	/**
	 * Version of the plugin.
	 *
	 * @var string $_version
	 */
	protected $_version = GWP_MP_GF_CONNECTOR_VERSION;

	/**
	 * Minimum Gravity Forms version required.
	 *
	 * @var string $_min_gravityforms_version
	 */
	protected $_min_gravityforms_version = '2.4';

	/**
	 * Plugin slug.
	 *
	 * @var string $_slug
	 */
	protected $_slug = 'gravitywp-mp-gf-connector';

	/**
	 * Plugin path.
	 *
	 * @var string $_path
	 */
	protected $_path = 'gravitywp-mp-gf-connector/gravitywp-mp-gf-connector.php';

	/**
	 * Full plugin file path.
	 *
	 * @var string $_full_path
	 */
	protected $_full_path = __FILE__;

	/**
	 * Plugin title.
	 *
	 * @var string $_title
	 */
	protected $_title = 'Connector for Mailpoet and Gravity Forms';

	/**
	 * Short plugin title.
	 *
	 * @var string $_short_title
	 */
	protected $_short_title = 'Mailpoet Connector';

	/**
	 * Instance of this class.
	 *
	 * @var GWP_MP_GF_Connector $_instance
	 */
	private static $_instance = null;

	/**
	 * Get an instance of this class.
	 *
	 * @return GWP_MP_GF_Connector
	 */
	public static function get_instance() {
		if ( self::$_instance === null ) {
			self::$_instance = new GWP_MP_GF_Connector();
		}

		return self::$_instance;
	}

		/**
		 * Configures which columns should be displayed on the feed list page.
		 *
		 * @return array<mixed>
		 */
	public function feed_list_columns() {
		return array(
			'feedname'      => esc_html__( 'Name', 'gravitywp-mp-gf-connector' ),
			'mailpoet_list' => esc_html__( 'MailPoet Lists', 'gravitywp-mp-gf-connector' ),
		);
	}

	/**
	 * Feed settings fields.
	 *
	 * @return array<mixed>
	 */
	public function feed_settings_fields() {
		return array(
			array(
				'title'  => esc_html__( 'MailPoet Feed Settings', 'gravitywp-mp-gf-connector' ),
				'fields' => array(
					array(
						'label'    => esc_html__( 'Feed name', 'gravitywp-mp-gf-connector' ),
						'type'     => 'text',
						'name'     => 'feedname',
						'class'    => '',
						'required' => true,
					),
					array(
						'name'      => 'mappedfields',
						'label'     => esc_html__( 'Map Fields', 'gravitywp-mp-gf-connector' ),
						'type'      => 'field_map',
						'tooltip'   => esc_html__( 'Map the Gravity Form fields to the Mailpoet subscriber fields', 'gravitywp-mp-gf-connector' ),
						'field_map' => array(
							array(
								'name'  => 'first_name',
								'label' => esc_html__( 'First Name', 'gravitywp-mp-gf-connector' ),
							),
							array(
								'name'  => 'last_name',
								'label' => esc_html__( 'Last Name', 'gravitywp-mp-gf-connector' ),

							),
							array(
								'name'       => 'email',
								'label'      => esc_html__( 'Email', 'gravitywp-mp-gf-connector' ),
								'field_type' => array( 'email', 'hidden' ),
							),
						),
					),
					array(
						'label'   => esc_html__( 'Existing subscribers', 'gravitywp-mp-gf-connector' ),
						'type'    => 'checkbox',
						'name'    => 'existing_subscriber',
						'choices' => array(
							array(
								'label' => esc_html__( 'Update the name of the existing subscriber', 'gravitywp-mp-gf-connector' ),
								'name'  => 'update_existing_subscriber',
							),
						),
					),
					$this->get_mplists_setting_array(),
					array(
						'name'           => 'condition',
						'label'          => esc_html__( 'Condition', 'gravitywp-mp-gf-connector' ),
						'type'           => 'feed_condition',
						'checkbox_label' => esc_html__( 'Enable Condition', 'gravitywp-mp-gf-connector' ),
						'instructions'   => esc_html__( 'Process this feed if', 'gravitywp-mp-gf-connector' ),
					),
				),
			),
		);
	}


	/**
	 * Returns the Mailpoet lists as settings array.
	 *
	 * @since 1.0
	 *
	 * @return array<mixed>
	 */
	protected function get_mplists_setting_array() {
		$mp_lists = $this->get_mailpoet_lists();
		$choices  = array();

		foreach ( $mp_lists as $list ) {
			$choices[] = array(
				'label' => $list['label'],
				'name'  => 'mp_lists_' . $list['value'],
			);

		}

		if ( empty( $choices ) ) {
			return array(
				'name'  => 'no_lists',
				'label' => esc_html__( 'Mailpoet Lists', 'gravitywp-mp-gf-connector' ),
				'type'  => 'html',
				'html'  => esc_html__( "You don't have any lists set up.", 'gravitywp-mp-gf-connector' ),
			);
		}

		return array(
			'name'     => 'mailpoet_lists',
			'required' => true,
			'label'    => esc_html__( 'Mailpoet Lists', 'gravitywp-mp-gf-connector' ),
			'type'     => 'checkbox',
			'choices'  => $choices,
		);
	}

	/**
	 * Retrieve available MailPoet lists.
	 *
	 * @return array<mixed>
	 */
	private function get_mailpoet_lists() {
		if ( ! class_exists( \MailPoet\API\API::class ) ) {
			$this->log_error( __METHOD__ . '(): MailPoet API class does not exist.' );
			return array();
		}

		$mailpoet_api = \MailPoet\API\API::MP( 'v1' );
		$lists        = array();

		try {
			$mailpoet_lists = $mailpoet_api->getLists();
			foreach ( $mailpoet_lists as $list ) {
				$lists[] = array(
					'label' => esc_html( $list['name'] ),
					'value' => esc_attr( $list['id'] ),
				);
			}
		} catch ( \Exception $e ) {
			$this->log_error( __METHOD__ . '(): MailPoet API Error: ' . $e->getMessage() );
		}

		return $lists;
	}

	/**
	 * Process feed on form submission.
	 *
	 * @param array<mixed> $feed The current feed.
	 * @param array<mixed> $entry The Entry object.
	 * @param array<mixed> $form The Form object.
	 *
	 * @return null|array<mixed> Return null or modified entry.
	 */
	public function process_feed( $feed, $entry, $form ) {
		if ( ! class_exists( \MailPoet\API\API::class ) ) {
			$this->log_error( __METHOD__ . '(): MailPoet API class does not exist.' );
			return $entry;
		}

		$mp_lists = $this->get_mailpoet_lists();
		// Get the selected MailPoet lists from the feed meta.
		$selected_lists = array();
		foreach ( $mp_lists as $list ) {
			$selected_list = rgar( $feed['meta'], 'mp_lists_' . $list['value'] );
			if ( $selected_list === '1' ) {
				$selected_lists[] = $list['value'];
			}
		}

		// Collect email and other fields from the entry..
		$email      = rgar( $entry, $feed['meta']['mappedfields_email'] );
		$first_name = rgar( $entry, $feed['meta']['mappedfields_first_name'] );
		$last_name  = rgar( $entry, $feed['meta']['mappedfields_last_name'] );

		$subscriber = array(
			'email'      => $email,
			'first_name' => $first_name,
			'last_name'  => $last_name,
		);

		// Default options.
		$options = array();

		// Get MailPoet API instance.
		$mailpoet_api = \MailPoet\API\API::MP( 'v1' );

		try {
			// Check if subscriber exists.
			$existing_subscriber = $mailpoet_api->getSubscriber( $subscriber['email'] );
		} catch ( \Exception $e ) {
			$existing_subscriber = false;
		}

		try {
			if ( ! $existing_subscriber ) {
				$mailpoet_api->addSubscriber( $subscriber, $selected_lists, $options );
			} else {
				// Optionally update the subscriber..
				if ( rgar( $feed['meta'], 'update_existing_subscriber' ) ) {
					$mailpoet_api->updateSubscriber( $subscriber['email'], $subscriber );
				}
				// Subscriber exists, add to new lists..
				$mailpoet_api->subscribeToLists( $subscriber['email'], $selected_lists, $options );
			}
		} catch ( \Exception $e ) {
			$this->log_error( __METHOD__ . '(): MailPoet API Error: ' . $e->getMessage() );
		}

		return $entry;
	}
}