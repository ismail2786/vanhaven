<?php
/**
 * VH Gallery with filters — register + server render.
 *
 * @package VanHaven_Custom_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VHVG_Block {

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
			VHCB_PATH . 'blocks/van-gallery',
			array(
				'render_callback' => array( $this, 'render' ),
			)
		);

		$handle = generate_block_asset_handle( 'vanhaven/van-gallery', 'editorScript' );
		if ( $handle ) {
			wp_localize_script( $handle, 'vhvgAdmin', array(
				'newUrl'  => admin_url( 'post-new.php?post_type=' . VHVG_CPT::POST_TYPE ),
				'catUrl'  => admin_url( 'edit-tags.php?taxonomy=' . VHVG_CPT::TAXONOMY . '&post_type=' . VHVG_CPT::POST_TYPE ),
			) );
		}
	}

	/**
	 * Server render.
	 *
	 * @param array $attributes Attributes.
	 * @return string
	 */
	public function render( $attributes ) {
		$per_page    = isset( $attributes['perPage'] ) ? max( 1, absint( $attributes['perPage'] ) ) : 10;
		$accent      = isset( $attributes['accentColor'] ) ? $attributes['accentColor'] : '#F0651F';
		$show_video  = isset( $attributes['showVideoToggle'] ) ? (bool) $attributes['showVideoToggle'] : true;
		$show_sort   = isset( $attributes['showSort'] ) ? (bool) $attributes['showSort'] : true;

		$filters = VHVG_REST::get_filters();
		$data    = VHVG_REST::get_items( 'all', 'photo', 'featured', 0, $per_page );
		$items   = $data['items'];
		$total   = $data['total'];

		if ( empty( $items ) && count( $filters ) <= 2 ) {
			if ( current_user_can( 'edit_posts' ) ) {
				return '<div class="vhvg vhvg--empty"><p>' . esc_html__( 'No media yet. Add images under VanHaven → Van Gallery, and create categories under Van Gallery → Categories.', 'vanhaven-custom-blocks' ) . '</p></div>';
			}
			return '';
		}

		$rest_base  = esc_url_raw( rest_url( VHVG_REST::NS . '/' ) );
		$rest_nonce = is_user_logged_in() ? wp_create_nonce( 'wp_rest' ) : '';
		$shown      = count( $items );

		$wrapper = get_block_wrapper_attributes( array(
			'class' => 'vhvg',
			'style' => '--vhvg-accent:' . esc_attr( $accent ) . ';',
		) );

		ob_start();
		?>
		<div <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			data-rest="<?php echo esc_attr( $rest_base ); ?>"
			data-nonce="<?php echo esc_attr( $rest_nonce ); ?>"
			data-perpage="<?php echo esc_attr( $per_page ); ?>"
			data-total="<?php echo esc_attr( $total ); ?>"
			data-shown="<?php echo esc_attr( $shown ); ?>"
			data-category="all" data-type="photo" data-sort="featured">

			<!-- Filter 1: category tabs -->
			<div class="vhvg__cats" role="tablist">
				<?php foreach ( $filters as $i => $f ) : ?>
					<button
						type="button"
						class="vhvg__cat<?php echo 0 === $i ? ' is-active' : ''; ?>"
						data-cat="<?php echo esc_attr( $f['slug'] ); ?>"
						<?php echo $f['thumb'] ? 'style="background-image:url(' . esc_url( $f['thumb'] ) . ')"' : ''; ?>>
						<span class="vhvg__cat-label"><?php echo esc_html( $f['name'] ); ?></span>
					</button>
				<?php endforeach; ?>
			</div>

			<!-- Filter 2: type toggle + sort -->
			<div class="vhvg__controls">
				<?php if ( $show_video ) : ?>
					<div class="vhvg__types" role="tablist">
						<button type="button" class="vhvg__type is-active" data-type="photo"><?php esc_html_e( 'Photos', 'vanhaven-custom-blocks' ); ?></button>
						<button type="button" class="vhvg__type" data-type="video"><?php esc_html_e( 'Videos', 'vanhaven-custom-blocks' ); ?></button>
					</div>
				<?php endif; ?>

				<?php if ( $show_sort ) : ?>
					<label class="vhvg__sort">
						<span class="vhvg__sort-label"><?php esc_html_e( 'Sort by:', 'vanhaven-custom-blocks' ); ?></span>
						<select class="vhvg__sort-select">
							<option value="featured"><?php esc_html_e( 'Featured', 'vanhaven-custom-blocks' ); ?></option>
							<option value="newest"><?php esc_html_e( 'Newest', 'vanhaven-custom-blocks' ); ?></option>
							<option value="oldest"><?php esc_html_e( 'Oldest', 'vanhaven-custom-blocks' ); ?></option>
						</select>
					</label>
				<?php endif; ?>
			</div>

			<!-- Grid -->
			<div class="vhvg__grid">
				<?php echo self::render_tiles( $items ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>

			<!-- Footer -->
			<div class="vhvg__footer">
				<p class="vhvg__count">
					<?php
					/* translators: 1: shown, 2: total. */
					printf(
						esc_html__( 'Showing %1$s of %2$s Projects', 'vanhaven-custom-blocks' ),
						'<span class="vhvg__shown">' . esc_html( $shown ) . '</span>',
						'<span class="vhvg__total">' . esc_html( $total ) . '</span>'
					);
					?>
				</p>
				<button type="button" class="vhvg__more" <?php echo ( $shown >= $total ) ? 'hidden' : ''; ?>>
					<?php esc_html_e( 'Load More', 'vanhaven-custom-blocks' ); ?>
				</button>
			</div>

			<!-- Lightbox -->
			<div class="vhvg__lightbox" role="dialog" aria-modal="true" aria-hidden="true">
				<button class="vhvg__lb-close" type="button" aria-label="<?php esc_attr_e( 'Close', 'vanhaven-custom-blocks' ); ?>">&times;</button>
				<button class="vhvg__lb-prev" type="button" aria-label="<?php esc_attr_e( 'Previous', 'vanhaven-custom-blocks' ); ?>">&#8592;</button>
				<div class="vhvg__lb-stage"></div>
				<button class="vhvg__lb-next" type="button" aria-label="<?php esc_attr_e( 'Next', 'vanhaven-custom-blocks' ); ?>">&#8594;</button>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render tiles.
	 *
	 * @param array $items Items.
	 * @return string
	 */
	public static function render_tiles( $items ) {
		ob_start();
		foreach ( $items as $item ) :
			$alt    = ! empty( $item['alt'] ) ? $item['alt'] : $item['title'];
			$is_vid = 'video' === $item['type'];
			?>
			<button
				type="button"
				class="vhvg__tile<?php echo $is_vid ? ' vhvg__tile--video' : ''; ?>"
				data-type="<?php echo esc_attr( $item['type'] ); ?>"
				data-full="<?php echo esc_url( $item['full'] ); ?>"
				data-video="<?php echo esc_url( $item['video'] ); ?>"
				data-caption="<?php echo esc_attr( $item['title'] ); ?>"
				aria-label="<?php echo esc_attr( $alt ); ?>">
				<?php if ( $item['thumb'] ) : ?>
					<img class="vhvg__img" src="<?php echo esc_url( $item['thumb'] ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy" />
				<?php endif; ?>
				<span class="vhvg__zoom" aria-hidden="true">
					<?php if ( $is_vid ) : ?>
						<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
					<?php else : ?>
						<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3M11 8v6M8 11h6"/></svg>
					<?php endif; ?>
				</span>
			</button>
			<?php
		endforeach;
		return ob_get_clean();
	}
}
