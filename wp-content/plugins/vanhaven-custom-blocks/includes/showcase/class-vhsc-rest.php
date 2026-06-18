<?php
/**
 * REST API endpoints.
 *
 * @package VanHaven_Showcase
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VHSC_REST {

	const NS = 'vanhaven/v1';

	/**
	 * Register routes.
	 */
	public function register() {
		add_action( 'rest_api_init', array( $this, 'routes' ) );
	}

	/**
	 * Define REST routes.
	 */
	public function routes() {
		register_rest_route(
			self::NS,
			'/tabs',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_tabs' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			self::NS,
			'/products',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_products' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'category' => array(
						'sanitize_callback' => 'absint',
						'default'           => 0,
					),
					'limit'    => array(
						'sanitize_callback' => 'absint',
						'default'           => 8,
					),
					'badgeMetaKey' => array(
						'sanitize_callback' => 'sanitize_key',
						'default'           => '',
					),
				),
			)
		);
	}

	/**
	 * Tabs callback.
	 *
	 * @return WP_REST_Response
	 */
	public function get_tabs() {
		return rest_ensure_response( VHSC_Query::get_tabs() );
	}

	/**
	 * Products callback.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function get_products( $request ) {
		$category = (int) $request->get_param( 'category' );
		$limit    = (int) $request->get_param( 'limit' );
		$atts     = array( 'badgeMetaKey' => $request->get_param( 'badgeMetaKey' ) );

		return rest_ensure_response( VHSC_Query::get_products( $category, $limit, $atts ) );
	}
}
