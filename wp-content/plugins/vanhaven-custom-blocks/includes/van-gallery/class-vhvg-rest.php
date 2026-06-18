<?php
/**
 * REST endpoints for VH Gallery with filters.
 *
 *  /filters  -> the category tabs (with thumbnails) + counts
 *  /items    -> filtered, sorted, paginated media + total
 *
 * @package VanHaven_Custom_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VHVG_REST {

	const NS = 'vanhaven-gallery/v1';

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
		register_rest_route( self::NS, '/filters', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => array( $this, 'filters' ),
			'permission_callback' => '__return_true',
		) );

		register_rest_route( self::NS, '/items', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => array( $this, 'items' ),
			'permission_callback' => '__return_true',
			'args'                => array(
				'category' => array( 'sanitize_callback' => 'sanitize_text_field', 'default' => 'all' ),
				'type'     => array( 'sanitize_callback' => 'sanitize_text_field', 'default' => 'photo' ),
				'sort'     => array( 'sanitize_callback' => 'sanitize_text_field', 'default' => 'featured' ),
				'offset'   => array( 'sanitize_callback' => 'absint', 'default' => 0 ),
				'per_page' => array( 'sanitize_callback' => 'absint', 'default' => 10 ),
			),
		) );
	}

	/**
	 * Filters (category tabs).
	 *
	 * @return WP_REST_Response
	 */
	public function filters() {
		return rest_ensure_response( self::get_filters() );
	}

	/**
	 * Build the filter tabs list. "All Builds" + "Featured" are virtual, then terms.
	 *
	 * @return array
	 */
	public static function get_filters() {
		$tabs = array(
			array( 'slug' => 'all', 'name' => __( 'All Builds', 'vanhaven-custom-blocks' ), 'thumb' => '' ),
			array( 'slug' => 'featured', 'name' => __( 'Featured', 'vanhaven-custom-blocks' ), 'thumb' => '' ),
		);

		if ( taxonomy_exists( VHVG_CPT::TAXONOMY ) ) {
			$terms = get_terms( array(
				'taxonomy'   => VHVG_CPT::TAXONOMY,
				'hide_empty' => false,
				'orderby'    => 'menu_order',
			) );
			if ( ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					$thumb_id = get_term_meta( $term->term_id, 'vhvg_thumb', true );
					$tabs[]   = array(
						'slug'  => $term->slug,
						'name'  => $term->name,
						'thumb' => $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'medium' ) : '',
					);
				}
			}
		}

		return $tabs;
	}

	/**
	 * Items callback.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public function items( $request ) {
		return rest_ensure_response(
			self::get_items(
				$request->get_param( 'category' ),
				$request->get_param( 'type' ),
				$request->get_param( 'sort' ),
				(int) $request->get_param( 'offset' ),
				max( 1, (int) $request->get_param( 'per_page' ) )
			)
		);
	}

	/**
	 * Query gallery items.
	 *
	 * @param string $category Category slug, 'all', or 'featured'.
	 * @param string $type     'photo' | 'video' | 'all'.
	 * @param string $sort     'featured' | 'newest' | 'oldest'.
	 * @param int    $offset   Offset.
	 * @param int    $per_page Per page.
	 * @return array
	 */
	public static function get_items( $category = 'all', $type = 'photo', $sort = 'featured', $offset = 0, $per_page = 10 ) {
		$meta_query = array();
		$tax_query  = array();

		// Type filter.
		if ( 'all' !== $type ) {
			$type = 'video' === $type ? 'video' : 'photo';
			// Photos: type=photo OR not set (legacy). Videos: type=video.
			if ( 'photo' === $type ) {
				$meta_query[] = array(
					'relation' => 'OR',
					array( 'key' => VHVG_CPT::META_TYPE, 'value' => 'photo' ),
					array( 'key' => VHVG_CPT::META_TYPE, 'compare' => 'NOT EXISTS' ),
				);
			} else {
				$meta_query[] = array( 'key' => VHVG_CPT::META_TYPE, 'value' => 'video' );
			}
		}

		// Category filter.
		if ( 'featured' === $category ) {
			$meta_query[] = array( 'key' => VHVG_CPT::META_FEATURED, 'value' => '1' );
		} elseif ( 'all' !== $category && '' !== $category ) {
			$tax_query[] = array(
				'taxonomy' => VHVG_CPT::TAXONOMY,
				'field'    => 'slug',
				'terms'    => array( $category ),
			);
		}

		// Sort.
		$args = array(
			'post_type'      => VHVG_CPT::POST_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => $per_page,
			'offset'         => $offset,
		);
		switch ( $sort ) {
			case 'newest':
				$args['orderby'] = 'date';
				$args['order']   = 'DESC';
				break;
			case 'oldest':
				$args['orderby'] = 'date';
				$args['order']   = 'ASC';
				break;
			case 'featured':
			default:
				// Featured first, then menu order.
				$args['meta_key'] = VHVG_CPT::META_FEATURED; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				$args['orderby']  = array( 'meta_value' => 'DESC', 'menu_order' => 'ASC', 'date' => 'DESC' );
				break;
		}

		if ( ! empty( $meta_query ) ) {
			$args['meta_query'] = count( $meta_query ) > 1 ? array_merge( array( 'relation' => 'AND' ), $meta_query ) : $meta_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		}
		if ( ! empty( $tax_query ) ) {
			$args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		}

		$query = new WP_Query( $args );

		$items = array();
		foreach ( $query->posts as $post ) {
			$thumb_id = get_post_thumbnail_id( $post->ID );
			$poster   = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'medium_large' ) : '';
			$full     = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'full' ) : '';
			$itemType = get_post_meta( $post->ID, VHVG_CPT::META_TYPE, true );
			$itemType = 'video' === $itemType ? 'video' : 'photo';
			$video    = get_post_meta( $post->ID, VHVG_CPT::META_VIDEO, true );

			if ( ! $poster && 'video' !== $itemType ) {
				continue;
			}

			$items[] = array(
				'id'       => $post->ID,
				'title'    => get_the_title( $post ),
				'type'     => $itemType,
				'thumb'    => $poster ? $poster : '',
				'full'     => $full ? $full : $poster,
				'video'    => $itemType === 'video' ? $video : '',
				'featured' => get_post_meta( $post->ID, VHVG_CPT::META_FEATURED, true ) === '1',
				'alt'      => $thumb_id ? get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ) : '',
			);
		}

		// Total for current filter (for the "Showing X of Y" counter).
		$count_args = $args;
		$count_args['posts_per_page'] = -1;
		$count_args['offset']         = 0;
		$count_args['fields']         = 'ids';
		$count_args['no_found_rows']  = false;
		$count_query = new WP_Query( $count_args );
		$total       = (int) $count_query->found_posts;

		return array(
			'items'    => $items,
			'total'    => $total,
			'offset'   => $offset,
			'per_page' => $per_page,
		);
	}
}
