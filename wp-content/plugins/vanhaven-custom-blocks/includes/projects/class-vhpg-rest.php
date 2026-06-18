<?php
/**
 * REST endpoint for the VH Project Gallery.
 * Supports pagination (offset/per_page) and returns the total count so the
 * frontend can show "Showing X of Y" and a working Load More button.
 *
 * @package VanHaven_Custom_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VHPG_REST {

	const NS = 'vanhaven-projects/v1';

	/**
	 * Hook.
	 */
	public function register() {
		add_action( 'rest_api_init', array( $this, 'routes' ) );
	}

	/**
	 * Routes.
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
					'offset'   => array(
						'sanitize_callback' => 'absint',
						'default'           => 0,
					),
					'per_page' => array(
						'sanitize_callback' => 'absint',
						'default'           => 5,
					),
				),
			)
		);
	}

	/**
	 * Items callback.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function items( $request ) {
		$offset   = (int) $request->get_param( 'offset' );
		$per_page = max( 1, (int) $request->get_param( 'per_page' ) );

		$result = self::get_items( $offset, $per_page );
		return rest_ensure_response( $result );
	}

	/**
	 * Query projects with pagination + total.
	 *
	 * @param int $offset   Offset.
	 * @param int $per_page Items per page.
	 * @return array { items: [], total: int, offset: int, per_page: int }
	 */
	public static function get_items( $offset = 0, $per_page = 5 ) {
		$query = new WP_Query(
			array(
				'post_type'      => VHPG_CPT::POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => $per_page,
				'offset'         => $offset,
				'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
				'no_found_rows'  => false,
			)
		);

		$items = array();
		foreach ( $query->posts as $post ) {
			$thumb_id = get_post_thumbnail_id( $post->ID );
			if ( ! $thumb_id ) {
				continue;
			}
			$full  = wp_get_attachment_image_url( $thumb_id, 'full' );
			$large = wp_get_attachment_image_url( $thumb_id, 'large' );
			$med   = wp_get_attachment_image_url( $thumb_id, 'medium_large' );
			if ( ! $full ) {
				continue;
			}
			$items[] = array(
				'id'       => $post->ID,
				'title'    => get_the_title( $post ),
				'caption'  => (string) get_post_meta( $post->ID, '_vhpg_caption', true ),
				'thumb'    => $med ? $med : $large,
				'large'    => $large ? $large : $full,
				'full'     => $full,
				'alt'      => get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ),
			);
		}

		// Total count of all published projects (with a featured image).
		$total = self::total_count();

		return array(
			'items'    => $items,
			'total'    => $total,
			'offset'   => $offset,
			'per_page' => $per_page,
		);
	}

	/**
	 * Count published projects.
	 *
	 * @return int
	 */
	public static function total_count() {
		$counts = wp_count_posts( VHPG_CPT::POST_TYPE );
		return isset( $counts->publish ) ? (int) $counts->publish : 0;
	}
}
