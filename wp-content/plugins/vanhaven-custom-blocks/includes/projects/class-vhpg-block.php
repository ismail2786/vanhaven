<?php
/**
 * VH Project Gallery block — register + server render.
 *
 * Renders the first batch of projects in a mosaic grid (SEO-friendly), with a
 * magnify icon per tile, a "Showing X of Y" counter and a Load More button.
 * Lightbox + Load More are handled in view.js against the REST endpoint.
 *
 * @package VanHaven_Custom_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VHPG_Block {

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
			VHCB_PATH . 'blocks/project-gallery',
			array(
				'render_callback' => array( $this, 'render' ),
			)
		);

		$handle = generate_block_asset_handle( 'vanhaven/project-gallery', 'editorScript' );
		if ( $handle ) {
			wp_localize_script(
				$handle,
				'vhpgAdmin',
				array(
					'newUrl' => admin_url( 'post-new.php?post_type=' . VHPG_CPT::POST_TYPE ),
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
		$heading    = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
		$subheading = isset( $attributes['subheading'] ) ? $attributes['subheading'] : '';
		$per_page   = isset( $attributes['perPage'] ) ? max( 1, absint( $attributes['perPage'] ) ) : 5;
		$accent     = isset( $attributes['accentColor'] ) ? $attributes['accentColor'] : '#F0651F';

		$data  = VHPG_REST::get_items( 0, $per_page );
		$items = $data['items'];
		$total = $data['total'];

		if ( empty( $items ) ) {
			if ( current_user_can( 'edit_posts' ) ) {
				return '<div class="vhpg vhpg--empty"><p>' . esc_html__( 'No projects yet. Add some under VanHaven → Projects.', 'vanhaven-custom-blocks' ) . '</p></div>';
			}
			return '';
		}

		$rest_base  = esc_url_raw( rest_url( VHPG_REST::NS . '/' ) );
		$rest_nonce = is_user_logged_in() ? wp_create_nonce( 'wp_rest' ) : '';
		$shown      = count( $items );

		$wrapper = get_block_wrapper_attributes(
			array(
				'class' => 'vhpg',
				'style' => '--vhpg-accent:' . esc_attr( $accent ) . ';',
			)
		);

		ob_start();
		?>
		<div <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			data-rest="<?php echo esc_attr( $rest_base ); ?>"
			data-nonce="<?php echo esc_attr( $rest_nonce ); ?>"
			data-perpage="<?php echo esc_attr( $per_page ); ?>"
			data-total="<?php echo esc_attr( $total ); ?>"
			data-shown="<?php echo esc_attr( $shown ); ?>">

			<?php if ( $heading || $subheading ) : ?>
				<div class="vhpg__head">
					<?php if ( $heading ) : ?>
						<h2 class="vhpg__title"><?php echo wp_kses_post( $heading ); ?></h2>
					<?php endif; ?>
					<?php if ( $subheading ) : ?>
						<p class="vhpg__sub"><?php echo wp_kses_post( $subheading ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="vhpg__grid">
				<?php echo self::render_tiles( $items ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>

			<div class="vhpg__footer">
				<p class="vhpg__count">
					<?php
					/* translators: 1: number shown, 2: total. */
					printf(
						esc_html__( 'Showing %1$s of %2$s Projects', 'vanhaven-custom-blocks' ),
						'<span class="vhpg__shown">' . esc_html( $shown ) . '</span>',
						'<span class="vhpg__total">' . esc_html( $total ) . '</span>'
					);
					?>
				</p>
				<button
					type="button"
					class="vhpg__more"
					<?php echo ( $shown >= $total ) ? 'hidden' : ''; ?>>
					<?php esc_html_e( 'Load More', 'vanhaven-custom-blocks' ); ?>
				</button>
			</div>

			<!-- Lightbox -->
			<div class="vhpg__lightbox" role="dialog" aria-modal="true" aria-hidden="true">
				<button class="vhpg__lb-close" type="button" aria-label="<?php esc_attr_e( 'Close', 'vanhaven-custom-blocks' ); ?>">&times;</button>
				<button class="vhpg__lb-prev" type="button" aria-label="<?php esc_attr_e( 'Previous', 'vanhaven-custom-blocks' ); ?>">&#8592;</button>
				<figure class="vhpg__lb-figure">
					<img class="vhpg__lb-img" src="" alt="" />
					<figcaption class="vhpg__lb-caption"></figcaption>
				</figure>
				<button class="vhpg__lb-next" type="button" aria-label="<?php esc_attr_e( 'Next', 'vanhaven-custom-blocks' ); ?>">&#8594;</button>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render gallery tiles.
	 *
	 * @param array $items Items.
	 * @return string
	 */
	public static function render_tiles( $items ) {
		ob_start();
		foreach ( $items as $item ) :
			$alt = ! empty( $item['alt'] ) ? $item['alt'] : $item['title'];
			?>
			<button
				type="button"
				class="vhpg__tile"
				data-full="<?php echo esc_url( $item['full'] ); ?>"
				data-large="<?php echo esc_url( $item['large'] ); ?>"
				data-caption="<?php echo esc_attr( $item['caption'] ? $item['caption'] : $item['title'] ); ?>"
				aria-label="<?php echo esc_attr( $alt ); ?>">
				<img class="vhpg__img" src="<?php echo esc_url( $item['thumb'] ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy" />
				<span class="vhpg__zoom" aria-hidden="true">
					<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3M11 8v6M8 11h6"/></svg>
				</span>
			</button>
			<?php
		endforeach;
		return ob_get_clean();
	}
}
