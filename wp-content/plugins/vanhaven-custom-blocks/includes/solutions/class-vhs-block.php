<?php
/**
 * Block registration & server render.
 *
 * @package VanHaven_Solutions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VHS_Block {

	/**
	 * Hook.
	 */
	public function register() {
		add_action( 'init', array( $this, 'register_block' ) );
	}

	/**
	 * Register block.
	 */
	public function register_block() {
		register_block_type(
			VHS_PATH . 'blocks/solutions',
			array(
				'render_callback' => array( $this, 'render' ),
			)
		);

		$handle = generate_block_asset_handle( 'vanhaven/solutions', 'editorScript' );
		if ( $handle ) {
			wp_localize_script(
				$handle,
				'vhsAdmin',
				array(
					'newUrl' => admin_url( 'post-new.php?post_type=' . VHS_CPT::POST_TYPE ),
				)
			);
		}
	}

	/**
	 * Server render.
	 *
	 * @param array $attributes Attributes.
	 * @return string
	 */
	public function render( $attributes ) {
		$heading    = isset( $attributes['heading'] ) ? $attributes['heading'] : 'Bespoke Automotive Solutions For Your Industry';
		$subheading = isset( $attributes['subheading'] ) ? $attributes['subheading'] : '';
		$limit      = isset( $attributes['limit'] ) ? absint( $attributes['limit'] ) : 20;
		$accent     = isset( $attributes['accentColor'] ) ? $attributes['accentColor'] : '#F0651F';

		$items = VHS_REST::get_items( $limit );
		if ( empty( $items ) ) {
			if ( current_user_can( 'edit_posts' ) ) {
				return '<div class="vhs vhs--empty"><p>' . esc_html__( 'No solutions yet. Add some under "Solutions" in the admin menu.', 'vanhaven-solutions' ) . '</p></div>';
			}
			return '';
		}

		$wrapper = get_block_wrapper_attributes(
			array(
				'class' => 'vhs',
				'style' => '--vhs-accent:' . esc_attr( $accent ) . ';',
			)
		);

		ob_start();
		?>
		<div <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<div class="vhs__head">
				<h2 class="vhs__title"><?php echo wp_kses_post( $heading ); ?></h2>
				<?php if ( $subheading ) : ?>
					<p class="vhs__sub"><?php echo wp_kses_post( $subheading ); ?></p>
				<?php endif; ?>
			</div>

			<div class="vhs__tabs" role="tablist">
				<?php foreach ( $items as $i => $item ) : ?>
					<button
						type="button"
						class="vhs__tab<?php echo 0 === $i ? ' is-active' : ''; ?>"
						role="tab"
						aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>"
						data-index="<?php echo esc_attr( $i ); ?>">
						<?php echo esc_html( $item['tabLabel'] ); ?>
					</button>
				<?php endforeach; ?>
			</div>

			<div class="vhs__viewport">
				<button class="vhs__nav vhs__nav--prev" type="button" aria-label="<?php esc_attr_e( 'Previous', 'vanhaven-solutions' ); ?>">&#8592;</button>
				<div class="vhs__track">
					<?php foreach ( $items as $i => $item ) : ?>
						<?php echo self::render_slide( $item, 0 === $i ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php endforeach; ?>
				</div>
				<button class="vhs__nav vhs__nav--next" type="button" aria-label="<?php esc_attr_e( 'Next', 'vanhaven-solutions' ); ?>">&#8594;</button>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render a single slide (two-column card).
	 *
	 * @param array $item   Item.
	 * @param bool  $active Whether this slide is active.
	 * @return string
	 */
	public static function render_slide( $item, $active = false ) {
		$alt = ! empty( $item['alt'] ) ? $item['alt'] : $item['heading'];
		ob_start();
		?>
		<article class="vhs__slide<?php echo $active ? ' is-active' : ''; ?>" role="tabpanel">
			<div class="vhs__card">
				<div class="vhs__content">
					<h3 class="vhs__heading"><?php echo esc_html( $item['heading'] ); ?></h3>
					<?php if ( ! empty( $item['description'] ) ) : ?>
						<p class="vhs__desc"><?php echo esc_html( $item['description'] ); ?></p>
					<?php endif; ?>

					<?php if ( ! empty( $item['features'] ) ) : ?>
						<ul class="vhs__features">
							<?php foreach ( $item['features'] as $feature ) : ?>
								<li class="vhs__feature"><?php echo esc_html( $feature ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php if ( ! empty( $item['ctaLabel'] ) ) : ?>
						<a class="vhs__cta" href="<?php echo esc_url( $item['ctaUrl'] ? $item['ctaUrl'] : '#' ); ?>">
							<?php echo esc_html( $item['ctaLabel'] ); ?> <span aria-hidden="true">&#8599;</span>
						</a>
					<?php endif; ?>
				</div>

				<div class="vhs__media">
					<?php if ( ! empty( $item['badge'] ) ) : ?>
						<span class="vhs__badge"><?php echo esc_html( $item['badge'] ); ?></span>
					<?php endif; ?>
					<?php if ( ! empty( $item['image'] ) ) : ?>
						<img class="vhs__img" src="<?php echo esc_url( $item['image'] ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy" />
					<?php endif; ?>
				</div>
			</div>
		</article>
		<?php
		return ob_get_clean();
	}
}
