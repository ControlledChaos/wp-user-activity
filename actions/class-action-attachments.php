<?php

/**
 * User Activity Attachment Actions
 *
 * @package User/Activity/Actions/Attachments
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Attachment actions
 *
 * @since 0.1.0
 */
class WP_User_Activity_Action_Attachment extends WP_User_Activity_Action_Base {

	/**
	 * What type of object is this?
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $object_type = 'attachment';

	/**
	 * Add hooks
	 *
	 * @since 0.1.0
	 */
	public function __construct() {

		// Actions
		add_action( 'add_attachment',    array( $this, 'add_attachment'    ) );
		add_action( 'edit_attachment',   array( $this, 'edit_attachment'   ) );
		add_action( 'delete_attachment', array( $this, 'delete_attachment' ) );

		// Setup callbacks
		parent::__construct( array(
			'create' => array( $this, 'create_callback' ),
			'update' => array( $this, 'update_callback' ),
			'delete' => array( $this, 'delete_callback' )
		) );
	}

	/** Actions ***************************************************************/

	/**
	 * Callback for returning human-readable output.
	 *
	 * @since 0.1.0
	 *
	 * @param  object  $post
	 * @param  array   $meta
	 *
	 * @return string
	 */
	public function create_callback( $post, $meta = array() ) {
		$user = $this->get_user( $post );

		return sprintf( '%1$s uploaded a "%2$s" named "%3$s" %4$s.',
			$user->display_name,
			$meta->object_subtype,
			$meta->object_name,
			$this->get_how_long_ago( $post )
		);
	}

	/**
	 * Callback for returning human-readable output.
	 *
	 * @since 0.1.0
	 *
	 * @param  object  $post
	 * @param  array   $meta
	 *
	 * @return string
	 */
	public function update_callback( $post, $meta = array() ) {
		$user = $this->get_user( $post );

		return sprintf( '%1$s edited a "%2$s" named "%3$s" %4$s.',
			$user->display_name,
			$meta->object_subtype,
			$meta->object_name,
			$this->get_how_long_ago( $post )
		);
	}

	/**
	 * Callback for returning human-readable output.
	 *
	 * @since 0.1.0
	 *
	 * @param  object  $post
	 * @param  array   $meta
	 *
	 * @return string
	 */
	public function delete_callback( $post, $meta = array() ) {
		$user = $this->get_user( $post );

		return sprintf( '%1$s deleted a "%2$s" named "%3$s" %4$s.',
			$user->display_name,
			$meta->object_subtype,
			$meta->object_name,
			$this->get_how_long_ago( $post )
		);
	}

	/** Logging ***************************************************************/

	/**
	 * Helper function for logging attachment actions
	 *
	 * @since 0.1.0
	 *
	 * @param  string  $action
	 * @param  int     $attachment_id
	 */
	protected function add_attachment_activity( $action = '', $attachment_id = 0 ) {
		$post = get_post( $attachment_id );

		// Insert activity
		wp_insert_user_activity( array(
			'object_type'    => $this->object_type,
			'object_subtype' => get_post_mime_type( $post->ID ),
			'object_name'    => get_the_title( $post->ID ),
			'object_id'      => $attachment_id,
			'action'         => $action,
		) );
	}

	/**
	 * Attachment added
	 *
	 * @since 0.1.0
	 *
	 * @param int $attachment_id
	 */
	public function add_attachment( $attachment_id ) {
		$this->add_attachment_activity( 'create', $attachment_id );
	}

	/**
	 * Attachment edited
	 *
	 * @since 0.1.0
	 *
	 * @param int $attachment_id
	 */
	public function edit_attachment( $attachment_id ) {
		$this->add_attachment_activity( 'update', $attachment_id );
	}

	/**
	 * Attachment deleted
	 *
	 * @since 0.1.0
	 *
	 * @param int $attachment_id
	 */
	public function delete_attachment( $attachment_id = 0 ) {
		$this->add_attachment_activity( 'delete', $attachment_id );
	}
}
new WP_User_Activity_Action_Attachment();