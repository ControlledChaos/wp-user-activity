<?php

/**
 * User Activity User Widgets
 *
 * @package User/Activity/Actions/Widgets
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Widgets actions
 *
 * @since 0.1.0
 */
class WP_User_Activity_Action_Widgets extends WP_User_Activity_Action_Base {

	/**
	 * What type of object is this?
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $object_type = 'widget';

	/**
	 * Add hooks
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		add_action( 'widget_update_callback', array( $this, 'widget_update_callback' ), 9999, 4 );
		add_action( 'sidebar_admin_setup',    array( $this, 'widget_delete' ) );

		parent::__construct();
	}

	/**
	 * Widgets updated
	 *
	 * @since 0.1.0
	 *
	 * @param  object     $instance
	 * @param  object     $new_instance
	 * @param  object     $old_instance
	 * @param  WP_Widget  $widget
	 */
	public function widget_update_callback( $instance, $new_instance, $old_instance, WP_Widget $widget ) {
		wp_insert_user_activity( array(
			'object_type'    => $this->object_type,
			'object_subtype' => $this->get_sidebar(),
			'object_name'    => $widget->id_base,
			'object_id'      => 0,
			'action'         => 'update'
		) );
	}

	/**
	 * Widget deleted
	 *
	 * @since 0.1.0
	 */
	public function widget_delete() {

		// Bail if not widget deletion request
		if ( ! $this->is_widget_delete() ) {
			return;
		}

		// Insert activity
		wp_insert_user_activity( array(
			'object_type'    => $this->object_type,
			'object_subtype' => $this->get_sidebar(),
			'object_name'    => $_REQUEST['id_base'],
			'object_id'      => 0,
			'action'         => 'delete',
		) );
	}

	/**
	 * Is a user attempting to delete a widget?
	 *
	 * @since 0.1.0
	 *
	 * @return boolean
	 */
	protected function is_widget_delete() {

		// Bail if not post request
		if ( 'post' !== strtolower( $_SERVER['REQUEST_METHOD'] ) ) {
			return false;
		}

		// Bail if no widget ID passed
		if ( empty( $_REQUEST['widget-id'] ) ) {
			return false;
		}

		if ( empty( $_REQUEST['delete_widget'] ) ) {
			return false;
		}

		// Backwards, but so be it
		return true;
	}

	/**
	 * Get the "sidebar" that a widget is for
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	protected function get_sidebar() {

		// Get sidebar request
		if ( isset( $_REQUEST['sidebar'] ) ) {
			return strtolower( $_REQUEST['sidebar'] );
		}

		// Unknown sidebar
		return 'unknown';
	}
}
new WP_User_Activity_Action_Widgets();