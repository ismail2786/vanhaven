<?php
/**
 * Registers the "Handover" custom post type.
 *
 * @package VanHaven_Handovers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VHHG_CPT {

	const POST_TYPE = 'vh_handover';

	/**
	 * Hook registration.
	 */
	public function register() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', array( $this, 'columns' ) );
		add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( $this, 'column_content' ), 10, 2 );
	}

	/**
	 * Register the CPT. Uses the standard featured-image UI for uploads.
	 */
	public function register_post_type() {
		$labels = array(
			'name'               => __( 'Handovers', 'vanhaven-handovers' ),
			'singular_name'      => __( 'Handover', 'vanhaven-handovers' ),
			'add_new'            => __( 'Add Handover', 'vanhaven-handovers' ),
			'add_new_item'       => __( 'Add New Handover', 'vanhaven-handovers' ),
			'edit_item'          => __( 'Edit Handover', 'vanhaven-handovers' ),
			'new_item'           => __( 'New Handover', 'vanhaven-handovers' ),
			'view_item'          => __( 'View Handover', 'vanhaven-handovers' ),
			'search_items'       => __( 'Search Handovers', 'vanhaven-handovers' ),
			'not_found'          => __( 'No handovers yet', 'vanhaven-handovers' ),
			'menu_name'          => __( 'Handovers', 'vanhaven-handovers' ),
		);

		register_post_type(
			self::POST_TYPE,
			array(
				'labels'        => $labels,
				'public'        => false,
				'show_ui'       => true,
				'show_in_menu'  => true,
				'show_in_rest'  => true,
				'menu_icon'     => 'dashicons-format-gallery',
				'menu_position' => 26,
				'supports'      => array( 'title', 'thumbnail', 'page-attributes' ),
				'has_archive'   => false,
				'rewrite'       => false,
			)
		);
	}

	/**
	 * Admin list columns: thumbnail + tag.
	 *
	 * @param array $cols Columns.
	 * @return array
	 */
	public function columns( $cols ) {
		$new = array();
		$new['cb'] = isset( $cols['cb'] ) ? $cols['cb'] : '';
		$new['vh_thumb'] = __( 'Image', 'vanhaven-handovers' );
		$new['title']    = __( 'Title', 'vanhaven-handovers' );
		$new['vh_tag']   = __( 'Tag / Badge', 'vanhaven-handovers' );
		$new['menu_order'] = __( 'Order', 'vanhaven-handovers' );
		$new['date']     = isset( $cols['date'] ) ? $cols['date'] : '';
		return $new;
	}

	/**
	 * Render custom column content.
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Post ID.
	 */
	public function column_content( $column, $post_id ) {
		if ( 'vh_thumb' === $column ) {
			if ( has_post_thumbnail( $post_id ) ) {
				echo get_the_post_thumbnail( $post_id, array( 70, 50 ) );
			} else {
				echo '&mdash;';
			}
		}
		if ( 'vh_tag' === $column ) {
			$tag = get_post_meta( $post_id, '_vhhg_tag', true );
			echo $tag ? esc_html( $tag ) : '&mdash;';
		}
		if ( 'menu_order' === $column ) {
			$post = get_post( $post_id );
			echo esc_html( $post ? $post->menu_order : 0 );
		}
	}
}
