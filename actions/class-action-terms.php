<?php

/**
 * User Activity Taxonomy Actions
 *
 * @package User/Activity/Actions/Taxonomy
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Taxonomy actions
 *
 * @since 0.1.0
 */
class WP_User_Activity_Action_Taxonomy extends WP_User_Activity_Action_Base {

	/**
	 * What type of object is this?
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $object_type = 'term';

	/**
	 * Add hooks
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		add_action( 'created_term', array( $this, 'created_edited_deleted_term' ), 10, 3 );
		add_action( 'edited_term',  array( $this, 'created_edited_deleted_term' ), 10, 3 );
		add_action( 'delete_term',  array( $this, 'created_edited_deleted_term' ), 10, 4 );

		parent::__construct();
	}

	/**
	 * Handle create/edit/delete term actions
	 *
	 * @since 0.1.0
	 *
	 * @param  string  $term_id
	 * @param  int     $tt_id
	 * @param  string  $taxonomy
	 * @param  string  $deleted_term
	 */
	public function created_edited_deleted_term( $term_id, $tt_id, $taxonomy, $deleted_term = null ) {

		// Make sure do not action nav menu taxonomy.
		if ( 'nav_menu' === $taxonomy ) {
			return;
		}

		if ( 'delete_term' === current_filter() ) {
			$term = $deleted_term;
		} else {
			$term = get_term( $term_id, $taxonomy );
		}

		if ( ! empty( $term ) && ! is_wp_error( $term ) ) {

			if ( 'edited_term' === current_filter() ) {
				$action = 'update';
			} elseif ( 'delete_term' === current_filter() ) {
				$action  = 'delete';
				$term_id = '';
			} else {
				$action = 'create';
			}

			// Insert activity
			wp_insert_user_activity( array(
				'object_type'    => $this->object_type,
				'object_subtype' => $taxonomy,
				'object_name'    => $term->name,
				'object_id'      => $term_id,
				'action'         => $action
			) );
		}
	}
}
new WP_User_Activity_Action_Taxonomy();