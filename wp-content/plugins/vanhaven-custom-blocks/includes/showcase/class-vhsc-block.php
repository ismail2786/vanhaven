<?php
/**
 * Block registration & dynamic server-side render.
 *
 * @package VanHaven_Showcase
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VHSC_Block {

	/**
	 * Register the block type from block.json with a render callback.
	 */
	public function register() {
		add_action( 'init', array( $this, 'register_block' ) );
	}

	/**
	 * Register block.
	 */
	public function register_block() {
		register_block_type(
			VHSC_PATH . 'blocks/product-showcase',
			array(
				'render_callback' => array( $this, 'render' ),
			)
		);
	}

	/**
	 * Server render. We output the tab headers + the first tab's products as
	 * SEO-friendly HTML, then a small JS slider/tabs script hydrates it.
	 *
	 * @param array $attributes Block attributes.
	 * @return string
	 */
	public function render( $attributes ) {
		$heading     = isset( $attributes['heading'] ) ? $attributes['heading'] : 'Premium Builds & Performance Parts';
		$subheading  = isset( $attributes['subheading'] ) ? $attributes['subheading'] : '';
		$limit       = isset( $attributes['limit'] ) ? absint( $attributes['limit'] ) : 8;
		$selected    = isset( $attributes['categories'] ) && is_array( $attributes['categories'] ) ? array_map( 'absint', $attributes['categories'] ) : array();
		$badge_meta  = isset( $attributes['badgeMetaKey'] ) ? sanitize_key( $attributes['badgeMetaKey'] ) : '';
		$cta_label   = isset( $attributes['ctaLabel'] ) ? $attributes['ctaLabel'] : 'View Details';
		$accent      = isset( $attributes['accentColor'] ) ? $attributes['accentColor'] : '#F0651F';

		$tabs = VHSC_Query::get_tabs( $selected );
		if ( empty( $tabs ) ) {
			return '';
		}

		$wrapper_attributes = get_block_wrapper_attributes(
			array(
				'class' => 'vhsc',
				'style' => '--vhsc-accent:' . esc_attr( $accent ) . ';',
			)
		);

		$first_id       = (int) $tabs[0]['id'];
		$first_products = VHSC_Query::get_products( $first_id, $limit, array( 'badgeMetaKey' => $badge_meta ) );

		// REST base for this site (works on sub-folder/sub-domain installs and pretty/plain permalinks).
		$rest_base = esc_url_raw( rest_url( 'vanhaven/v1/' ) );

		// Only emit a nonce for logged-in users. Anonymous (cacheable) visitors hit the
		// public, permission-free endpoint without a nonce, avoiding stale-nonce 403s.
		$rest_nonce = is_user_logged_in() ? wp_create_nonce( 'wp_rest' ) : '';

		ob_start();
		?>
		<div <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			data-limit="<?php echo esc_attr( $limit ); ?>"
			data-badge="<?php echo esc_attr( $badge_meta ); ?>"
			data-cta="<?php echo esc_attr( $cta_label ); ?>"
			data-rest="<?php echo esc_attr( $rest_base ); ?>"
			data-nonce="<?php echo esc_attr( $rest_nonce ); ?>">

			<div class="vhsc__head">
				<h2 class="vhsc__title"><?php echo wp_kses_post( $heading ); ?></h2>
				<?php if ( $subheading ) : ?>
					<p class="vhsc__sub"><?php echo wp_kses_post( $subheading ); ?></p>
				<?php endif; ?>
			</div>

			<div class="vhsc__tabs" role="tablist">
				<?php foreach ( $tabs as $i => $tab ) : ?>
					<button
						type="button"
						class="vhsc__tab<?php echo 0 === $i ? ' is-active' : ''; ?>"
						role="tab"
						aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>"
						data-cat="<?php echo esc_attr( $tab['id'] ); ?>">
						<?php echo esc_html( $tab['name'] ); ?>
					</button>
				<?php endforeach; ?>
			</div>

			<div class="vhsc__viewport">
				<button class="vhsc__nav vhsc__nav--prev" type="button" aria-label="<?php esc_attr_e( 'Previous', 'vanhaven-showcase' ); ?>">&#8592;</button>
				<div class="vhsc__rail" data-active-cat="<?php echo esc_attr( $first_id ); ?>">
					<?php echo self::render_cards( $first_products, $cta_label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<button class="vhsc__nav vhsc__nav--next" type="button" aria-label="<?php esc_attr_e( 'Next', 'vanhaven-showcase' ); ?>">&#8594;</button>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render product cards markup (shared by PHP render + can mirror JS render).
	 *
	 * @param array  $products  Product cards.
	 * @param string $cta_label CTA label.
	 * @return string
	 */
	public static function render_cards( $products, $cta_label ) {
		if ( empty( $products ) ) {
			return '<p class="vhsc__empty">' . esc_html__( 'No products found in this category.', 'vanhaven-showcase' ) . '</p>';
		}

		ob_start();
		foreach ( $products as $p ) :
			$specs = ! empty( $p['attributes'] ) ? implode( '  |  ', array_map( 'esc_html', $p['attributes'] ) ) : '';
			?>
			<article class="vhsc__card">
				<div class="vhsc__media">
					<?php if ( ! empty( $p['badge'] ) ) : ?>
						<span class="vhsc__badge"><?php echo esc_html( $p['badge'] ); ?></span>
					<?php endif; ?>
					<img class="vhsc__img" src="<?php echo esc_url( $p['image'] ); ?>" alt="<?php echo esc_attr( $p['title'] ); ?>" loading="lazy" />
					<div class="vhsc__overlay"></div>
					<div class="vhsc__body">
						<h3 class="vhsc__name"><?php echo esc_html( $p['title'] ); ?></h3>
						<?php if ( $specs ) : ?>
							<p class="vhsc__specs"><?php echo $specs; // phpcs:ignore ?></p>
						<?php endif; ?>
						<div class="vhsc__foot">
							<span class="vhsc__price"><?php echo wp_kses_post( $p['priceHtml'] ); ?></span>
							<a class="vhsc__cta" href="<?php echo esc_url( $p['permalink'] ); ?>">
								<?php echo esc_html( $cta_label ); ?> <span aria-hidden="true">&#8599;</span>
							</a>
						</div>
					</div>
				</div>
			</article>
			<?php
		endforeach;
		return ob_get_clean();
	}
}
