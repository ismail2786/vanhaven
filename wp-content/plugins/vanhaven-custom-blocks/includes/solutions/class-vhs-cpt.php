<?php
/**
 * "Solution" custom post type.
 *
 * @package VanHaven_Solutions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VHS_CPT {

	const POST_TYPE = 'vh_solution';

	/**
	 * Hooks.
	 */
	public function register() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', array( $this, 'columns' ) );
		add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( $this, 'column_content' ), 10, 2 );
	}

	/**
	 * Register CPT.
	 */
	public function register_post_type() {
		$labels = array(
			'name'          => __( 'Solutions', 'vanhaven-solutions' ),
			'singular_name' => __( 'Solution', 'vanhaven-solutions' ),
			'add_new'       => __( 'Add Solution', 'vanhaven-solutions' ),
			'add_new_item'  => __( 'Add New Solution', 'vanhaven-solutions' ),
			'edit_item'     => __( 'Edit Solution', 'vanhaven-solutions' ),
			'new_item'      => __( 'New Solution', 'vanhaven-solutions' ),
			'search_items'  => __( 'Search Solutions', 'vanhaven-solutions' ),
			'not_found'     => __( 'No solutions yet', 'vanhaven-solutions' ),
			'menu_name'     => __( 'Solutions', 'vanhaven-solutions' ),
		);

		register_post_type(
			self::POST_TYPE,
			array(
				'labels'        => $labels,
				'public'        => false,
				'show_ui'       => true,
				'show_in_menu'  => true,
				'show_in_rest'  => true,
				'menu_icon'     => 'dashicons-screenoptions',
				'menu_position' => 27,
				'supports'      => array( 'title', 'thumbnail', 'page-attributes' ),
				'has_archive'   => false,
				'rewrite'       => false,
			)
		);
	}

	/**
	 * Admin columns.
	 *
	 * @param array $cols Columns.
	 * @return array
	 */
	public function columns( $cols ) {
		$new = array();
		$new['cb']         = isset( $cols['cb'] ) ? $cols['cb'] : '';
		$new['vh_thumb']   = __( 'Image', 'vanhaven-solutions' );
		$new['title']      = __( 'Tab Label', 'vanhaven-solutions' );
		$new['vh_heading'] = __( 'Card Heading', 'vanhaven-solutions' );
		$new['vh_badge']   = __( 'Badge', 'vanhaven-solutions' );
		$new['menu_order'] = __( 'Order', 'vanhaven-solutions' );
		return $new;
	}

	/**
	 * Column content.
	 *
	 * @param string $column  Column.
	 * @param int    $post_id Post ID.
	 */
	public function column_content( $column, $post_id ) {
		switch ( $column ) {
			case 'vh_thumb':
				echo has_post_thumbnail( $post_id ) ? get_the_post_thumbnail( $post_id, array( 70, 50 ) ) : '&mdash;';
				break;
			case 'vh_heading':
				$h = get_post_meta( $post_id, VHS_Meta::HEADING, true );
				echo $h ? esc_html( $h ) : '&mdash;';
				break;
			case 'vh_badge':
				$b = get_post_meta( $post_id, VHS_Meta::BADGE, true );
				echo $b ? esc_html( $b ) : '&mdash;';
				break;
			case 'menu_order':
				$post = get_post( $post_id );
				echo esc_html( $post ? $post->menu_order : 0 );
				break;
		}
	}
}
