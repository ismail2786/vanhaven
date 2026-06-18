<?php
/**
 * Block registration & server render for the handovers gallery.
 *
 * @package VanHaven_Handovers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VHHG_Block {

	/**
	 * Hook.
	 */
	public function register() {
		add_action( 'init', array( $this, 'register_block' ) );
	}

	/**
	 * Register block from block.json.
	 */
	public function register_block() {
		register_block_type(
			VHHG_PATH . 'blocks/handovers',
			array(
				'render_callback' => array( $this, 'render' ),
			)
		);

		// Pass the "new handover" admin URL to the editor script.
		$handle = generate_block_asset_handle( 'vanhaven/handovers', 'editorScript' );
		if ( $handle ) {
			wp_localize_script(
				$handle,
				'vhhgAdmin',
				array(
					'newUrl' => admin_url( 'post-new.php?post_type=' . VHHG_CPT::POST_TYPE ),
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
		$heading    = isset( $attributes['heading'] ) ? $attributes['heading'] : 'The Moment Your New Chapter Begins';
		$subheading = isset( $attributes['subheading'] ) ? $attributes['subheading'] : '';
		$limit      = isset( $attributes['limit'] ) ? absint( $attributes['limit'] ) : 12;
		$rows       = isset( $attributes['rows'] ) ? absint( $attributes['rows'] ) : 2;
		$accent     = isset( $attributes['accentColor'] ) ? $attributes['accentColor'] : '#F0651F';

		$items = VHHG_REST::get_items( $limit );
		if ( empty( $items ) ) {
			if ( current_user_can( 'edit_posts' ) ) {
				return '<div class="vhhg vhhg--empty"><p>' . esc_html__( 'No handovers yet. Add some under "Handovers" in the admin menu.', 'vanhaven-handovers' ) . '</p></div>';
			}
			return '';
		}

		$wrapper = get_block_wrapper_attributes(
			array(
				'class' => 'vhhg',
				'style' => '--vhhg-accent:' . esc_attr( $accent ) . ';--vhhg-rows:' . esc_attr( max( 1, $rows ) ) . ';',
			)
		);

		ob_start();
		?>
		<div <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			data-rows="<?php echo esc_attr( max( 1, $rows ) ); ?>">
			<div class="vhhg__head">
				<h2 class="vhhg__title"><?php echo wp_kses_post( $heading ); ?></h2>
				<?php if ( $subheading ) : ?>
					<p class="vhhg__sub"><?php echo wp_kses_post( $subheading ); ?></p>
				<?php endif; ?>
			</div>

			<div class="vhhg__viewport">
				<div class="vhhg__grid">
					<?php echo self::render_items( $items ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</div>

			<div class="vhhg__nav">
				<button class="vhhg__btn vhhg__btn--prev" type="button" aria-label="<?php esc_attr_e( 'Previous', 'vanhaven-handovers' ); ?>">&#8592;</button>
				<button class="vhhg__btn vhhg__btn--next" type="button" aria-label="<?php esc_attr_e( 'Next', 'vanhaven-handovers' ); ?>">&#8594;</button>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render item tiles.
	 *
	 * @param array $items Items.
	 * @return string
	 */
	public static function render_items( $items ) {
		ob_start();
		foreach ( $items as $item ) :
			$alt = ! empty( $item['alt'] ) ? $item['alt'] : $item['title'];
			?>
			<figure class="vhhg__tile">
				<?php if ( ! empty( $item['tag'] ) ) : ?>
					<figcaption class="vhhg__badge"><?php echo esc_html( $item['tag'] ); ?></figcaption>
				<?php endif; ?>
				<img class="vhhg__img" src="<?php echo esc_url( $item['image'] ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy" />
			</figure>
			<?php
		endforeach;
		return ob_get_clean();
	}
}
