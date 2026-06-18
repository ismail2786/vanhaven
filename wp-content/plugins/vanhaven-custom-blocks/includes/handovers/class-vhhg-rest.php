<?php
/**
 * REST endpoint returning handover gallery items.
 *
 * @package VanHaven_Handovers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VHHG_REST {

	const NS = 'vanhaven-handovers/v1';

	/**
	 * Hook.
	 */
	public function register() {
		add_action( 'rest_api_init', array( $this, 'routes' ) );
	}

	/**
	 * Define routes.
	 */
	public function routes() {
		register_rest_route(
			self::NS,
			'/items',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'items' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'limit' => array(
						'sanitize_callback' => 'absint',
						'default'           => 12,
					),
				),
			)
		);
	}

	/**
	 * Return items.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function items( $request ) {
		$limit = (int) $request->get_param( 'limit' );
		return rest_ensure_response( self::get_items( $limit ) );
	}

	/**
	 * Query handover items into normalised cards.
	 *
	 * @param int $limit Max items.
	 * @return array
	 */
	public static function get_items( $limit = 12 ) {
		$posts = get_posts(
			array(
				'post_type'      => VHHG_CPT::POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => absint( $limit ),
				'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
			)
		);

		$items = array();
		foreach ( $posts as $post ) {
			$thumb_id = get_post_thumbnail_id( $post->ID );
			$img      = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'large' ) : '';
			if ( ! $img ) {
				continue;
			}
			$items[] = array(
				'id'    => $post->ID,
				'title' => get_the_title( $post ),
				'image' => $img,
				'tag'   => (string) get_post_meta( $post->ID, VHHG_Meta::META_KEY, true ),
				'alt'   => get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ),
			);
		}
		return $items;
	}
}
