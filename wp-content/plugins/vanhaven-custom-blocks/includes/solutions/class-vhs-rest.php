<?php
/**
 * REST endpoint returning solution slides.
 *
 * @package VanHaven_Solutions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VHS_REST {

	const NS = 'vanhaven-solutions/v1';

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
					'limit' => array(
						'sanitize_callback' => 'absint',
						'default'           => 20,
					),
				),
			)
		);
	}

	/**
	 * Callback.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function items( $request ) {
		return rest_ensure_response( self::get_items( (int) $request->get_param( 'limit' ) ) );
	}

	/**
	 * Build normalised solution slides.
	 *
	 * @param int $limit Max items.
	 * @return array
	 */
	public static function get_items( $limit = 20 ) {
		$posts = get_posts(
			array(
				'post_type'      => VHS_CPT::POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => absint( $limit ),
				'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
			)
		);

		$items = array();
		foreach ( $posts as $post ) {
			$thumb_id = get_post_thumbnail_id( $post->ID );
			$img      = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'large' ) : '';

			$features = get_post_meta( $post->ID, VHS_Meta::FEATURES, true );
			$features = is_array( $features ) ? array_values( array_filter( $features ) ) : array();

			$items[] = array(
				'id'          => $post->ID,
				'tabLabel'    => get_the_title( $post ),
				'heading'     => (string) get_post_meta( $post->ID, VHS_Meta::HEADING, true ),
				'description' => (string) get_post_meta( $post->ID, VHS_Meta::DESCRIPTION, true ),
				'badge'       => (string) get_post_meta( $post->ID, VHS_Meta::BADGE, true ),
				'ctaLabel'    => (string) get_post_meta( $post->ID, VHS_Meta::CTA_LABEL, true ),
				'ctaUrl'      => (string) get_post_meta( $post->ID, VHS_Meta::CTA_URL, true ),
				'features'    => $features,
				'image'       => $img,
				'alt'         => $thumb_id ? get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ) : '',
			);
		}
		return $items;
	}
}
