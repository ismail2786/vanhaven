<?php
/**
 * Query helpers: fetch product categories (tabs) and products.
 *
 * @package VanHaven_Showcase
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VHSC_Query {

	/**
	 * Get product categories usable as tabs.
	 *
	 * @param array $include Optional array of term IDs to limit to (ordered).
	 * @return array List of [ id, slug, name, count ].
	 */
	public static function get_tabs( $include = array() ) {
		if ( ! taxonomy_exists( 'product_cat' ) ) {
			return self::demo_tabs();
		}

		$args = array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'orderby'    => 'menu_order',
		);

		if ( ! empty( $include ) ) {
			$args['include'] = array_map( 'absint', $include );
			$args['orderby'] = 'include';
		}

		$terms = get_terms( $args );

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return self::demo_tabs();
		}

		$out = array();
		foreach ( $terms as $term ) {
			$out[] = array(
				'id'    => $term->term_id,
				'slug'  => $term->slug,
				'name'  => $term->name,
				'count' => (int) $term->count,
			);
		}
		return $out;
	}

	/**
	 * Get products for a given category.
	 *
	 * @param int   $category_id Product category term ID. 0 = all.
	 * @param int   $limit       Max products.
	 * @param array $atts        Extra display attributes (badge meta key, etc).
	 * @return array Normalised product cards.
	 */
	public static function get_products( $category_id = 0, $limit = 8, $atts = array() ) {
		if ( ! function_exists( 'wc_get_products' ) ) {
			return self::demo_products();
		}

		$query_args = array(
			'status'  => 'publish',
			'limit'   => absint( $limit ),
			'orderby' => 'menu_order',
			'order'   => 'ASC',
		);

		if ( $category_id ) {
			$term = get_term( $category_id, 'product_cat' );
			if ( $term && ! is_wp_error( $term ) ) {
				// Match by term ID (reliable) and include products in child categories.
				$query_args['tax_query'] = array(
					array(
						'taxonomy'         => 'product_cat',
						'field'            => 'term_id',
						'terms'            => array( $category_id ),
						'include_children' => true,
					),
				);
			}
		}

		$products = wc_get_products( $query_args );
		if ( empty( $products ) ) {
			return array();
		}

		$badge_meta = isset( $atts['badgeMetaKey'] ) ? sanitize_key( $atts['badgeMetaKey'] ) : '';
		$cards      = array();

		foreach ( $products as $product ) {
			$image_id  = $product->get_image_id();
			$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'large' ) : wc_placeholder_img_src( 'large' );

			$badge = '';
			if ( $badge_meta ) {
				$badge = (string) $product->get_meta( $badge_meta );
			}
			if ( ! $badge && $product->is_featured() ) {
				$badge = __( 'Featured', 'vanhaven-showcase' );
			}

			$cards[] = array(
				'id'         => $product->get_id(),
				'title'      => $product->get_name(),
				'permalink'  => get_permalink( $product->get_id() ),
				'image'      => $image_url,
				'priceHtml'  => $product->get_price_html(),
				'price'      => wc_get_price_to_display( $product ),
				'badge'      => $badge,
				'sku'        => $product->get_sku(),
				'shortDesc'  => wp_strip_all_tags( $product->get_short_description() ),
				'attributes' => self::flatten_attributes( $product ),
				'addToCart'  => $product->add_to_cart_url(),
				'type'       => $product->get_type(),
			);
		}

		return $cards;
	}

	/**
	 * Flatten the first few product attributes into a "T6.1 | 204PS | Automatic" style spec line.
	 *
	 * @param WC_Product $product Product.
	 * @return array
	 */
	private static function flatten_attributes( $product ) {
		$specs = array();
		$attrs = $product->get_attributes();
		$i     = 0;
		foreach ( $attrs as $attr ) {
			if ( $i >= 3 ) {
				break;
			}
			if ( $attr->is_taxonomy() ) {
				$terms = wc_get_product_terms( $product->get_id(), $attr->get_name(), array( 'fields' => 'names' ) );
				if ( ! empty( $terms ) ) {
					$specs[] = $terms[0];
				}
			} else {
				$opts = $attr->get_options();
				if ( ! empty( $opts ) ) {
					$specs[] = $opts[0];
				}
			}
			$i++;
		}
		return $specs;
	}

	/**
	 * Demo tabs used when WooCommerce is absent.
	 *
	 * @return array
	 */
	public static function demo_tabs() {
		return array(
			array( 'id' => 0, 'slug' => 'vans-rail', 'name' => __( 'Vans Rail', 'vanhaven-showcase' ), 'count' => 2 ),
			array( 'id' => 0, 'slug' => 'parts-rail', 'name' => __( 'Parts Rail', 'vanhaven-showcase' ), 'count' => 2 ),
		);
	}

	/**
	 * Demo products used when WooCommerce is absent.
	 *
	 * @return array
	 */
	public static function demo_products() {
		return array(
			array(
				'id'         => 0,
				'title'      => 'VanHaven Edition 73',
				'permalink'  => '#',
				'image'      => VHSC_URL . 'blocks/product-showcase/placeholder.jpg',
				'priceHtml'  => 'From <strong>&pound;74,995</strong> + VAT',
				'price'      => 74995,
				'badge'      => 'Ready To Drive',
				'sku'        => 'VH-73',
				'shortDesc'  => 'A premium custom van build.',
				'attributes' => array( 'T6.1', '204PS', 'Automatic' ),
				'addToCart'  => '#',
				'type'       => 'simple',
			),
			array(
				'id'         => 0,
				'title'      => 'VanHaven Edition 74',
				'permalink'  => '#',
				'image'      => VHSC_URL . 'blocks/product-showcase/placeholder.jpg',
				'priceHtml'  => 'From <strong>&pound;74,995</strong> + VAT',
				'price'      => 74995,
				'badge'      => 'Ready To Drive',
				'sku'        => 'VH-74',
				'shortDesc'  => 'A premium custom van build.',
				'attributes' => array( 'T6.1', '204PS', 'Automatic' ),
				'addToCart'  => '#',
				'type'       => 'simple',
			),
		);
	}
}
