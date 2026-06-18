<?php
/**
 * VH Feature Grid block — register + server render.
 *
 * Covers icon/heading/description cards with optional key-value meta rows
 * or a link. 2 or 3 columns. Content is stored in block attributes (per-placement).
 *
 * @package VanHaven_Custom_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VHFG_Block {

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
			VHCB_PATH . 'blocks/feature-grid',
			array(
				'render_callback' => array( $this, 'render' ),
			)
		);
	}

	/**
	 * Server render.
	 *
	 * @param array $attributes Block attributes.
	 * @return string
	 */
	public function render( $attributes ) {
		$heading    = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
		$subheading = isset( $attributes['subheading'] ) ? $attributes['subheading'] : '';
		$columns    = isset( $attributes['columns'] ) ? absint( $attributes['columns'] ) : 3;
		$accent     = isset( $attributes['accentColor'] ) ? $attributes['accentColor'] : '#F0651F';
		$icon_style = isset( $attributes['iconStyle'] ) ? $attributes['iconStyle'] : 'filled';
		$cards      = isset( $attributes['cards'] ) && is_array( $attributes['cards'] ) ? $attributes['cards'] : array();

		if ( empty( $cards ) ) {
			if ( current_user_can( 'edit_posts' ) ) {
				return '<div class="vhfg vhfg--empty"><p>' . esc_html__( 'Add cards to this Feature Grid in the block settings.', 'vanhaven-custom-blocks' ) . '</p></div>';
			}
			return '';
		}

		$wrapper = get_block_wrapper_attributes(
			array(
				'class' => 'vhfg vhfg--cols-' . $columns . ' vhfg--icon-' . sanitize_html_class( $icon_style ),
				'style' => '--vhfg-accent:' . esc_attr( $accent ) . ';',
			)
		);

		ob_start();
		?>
		<div <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php if ( $heading || $subheading ) : ?>
				<div class="vhfg__head">
					<?php if ( $heading ) : ?>
						<h2 class="vhfg__title"><?php echo wp_kses_post( $heading ); ?></h2>
					<?php endif; ?>
					<?php if ( $subheading ) : ?>
						<p class="vhfg__sub"><?php echo wp_kses_post( $subheading ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="vhfg__grid">
				<?php foreach ( $cards as $card ) : ?>
					<?php echo self::render_card( $card ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render one card.
	 *
	 * @param array $card Card data.
	 * @return string
	 */
	public static function render_card( $card ) {
		$icon     = isset( $card['icon'] ) ? $card['icon'] : 'shield';
		$title    = isset( $card['title'] ) ? $card['title'] : '';
		$desc     = isset( $card['description'] ) ? $card['description'] : '';
		$rows     = isset( $card['rows'] ) && is_array( $card['rows'] ) ? $card['rows'] : array();
		$linkText = isset( $card['linkText'] ) ? $card['linkText'] : '';
		$linkUrl  = isset( $card['linkUrl'] ) ? $card['linkUrl'] : '';

		ob_start();
		?>
		<div class="vhfg__card">
			<span class="vhfg__icon"><?php echo self::icon_svg( $icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			<?php if ( $title ) : ?>
				<h3 class="vhfg__card-title"><?php echo esc_html( $title ); ?></h3>
			<?php endif; ?>
			<?php if ( $desc ) : ?>
				<p class="vhfg__card-desc"><?php echo esc_html( $desc ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $rows ) ) : ?>
				<dl class="vhfg__rows">
					<?php foreach ( $rows as $row ) : ?>
						<?php
						$label = isset( $row['label'] ) ? $row['label'] : '';
						$value = isset( $row['value'] ) ? $row['value'] : '';
						if ( '' === $label && '' === $value ) {
							continue;
						}
						?>
						<div class="vhfg__row">
							<dt class="vhfg__row-label"><?php echo esc_html( $label ); ?></dt>
							<dd class="vhfg__row-value"><?php echo esc_html( $value ); ?></dd>
						</div>
					<?php endforeach; ?>
				</dl>
			<?php endif; ?>

			<?php if ( $linkText ) : ?>
				<a class="vhfg__link" href="<?php echo esc_url( $linkUrl ? $linkUrl : '#' ); ?>">
					<?php echo esc_html( $linkText ); ?> <span aria-hidden="true">&#8599;</span>
				</a>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Inline SVG icon set (no external dependency). Returns an <svg> string.
	 *
	 * @param string $name Icon key.
	 * @return string
	 */
	public static function icon_svg( $name ) {
		$paths = array(
			'shield'    => '<path d="M12 2 4 5v6c0 5 3.4 8.5 8 10 4.6-1.5 8-5 8-10V5l-8-3z"/>',
			'gear'      => '<path d="M12 8a4 4 0 100 8 4 4 0 000-8z"/><path d="M19 12a7 7 0 00-.1-1l2-1.6-2-3.4-2.4 1a7 7 0 00-1.7-1L14.4 2H9.6l-.4 2.6a7 7 0 00-1.7 1l-2.4-1-2 3.4L3.1 11a7 7 0 000 2l-2 1.6 2 3.4 2.4-1a7 7 0 001.7 1l.4 2.6h4.8l.4-2.6a7 7 0 001.7-1l2.4 1 2-3.4-2-1.6c.1-.3.1-.7.1-1z" fill="none" stroke="currentColor" stroke-width="1.6"/>',
			'wrench'    => '<path d="M21 5a4 4 0 01-5 5l-7 7-3-3 7-7a4 4 0 015-5l-2.5 2.5L17 7l1.5-.5L21 4z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>',
			'briefcase' => '<rect x="3" y="7" width="18" height="13" rx="2" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2" fill="none" stroke="currentColor" stroke-width="1.6"/>',
			'users'     => '<circle cx="9" cy="8" r="3" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M3 20a6 6 0 0112 0M16 5a3 3 0 010 6M21 20a6 6 0 00-4-5.6" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>',
			'award'     => '<circle cx="12" cy="9" r="5" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M9 13l-1.5 8L12 18l4.5 3L15 13" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>',
			'layers'    => '<path d="M12 3l9 5-9 5-9-5 9-5zM3 13l9 5 9-5M3 17l9 5 9-5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>',
			'chat'      => '<path d="M21 12a8 8 0 01-11.6 7.1L3 21l1.9-6.4A8 8 0 1121 12z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>',
			'pin'       => '<path d="M12 21s7-5.7 7-11a7 7 0 10-14 0c0 5.3 7 11 7 11z" fill="none" stroke="currentColor" stroke-width="1.6"/><circle cx="12" cy="10" r="2.5" fill="none" stroke="currentColor" stroke-width="1.6"/>',
			'phone'     => '<path d="M6 3h5l2 5-3 2a11 11 0 005 5l2-3 5 2v5a2 2 0 01-2 2A17 17 0 014 5a2 2 0 012-2z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>',
			'calendar'  => '<rect x="3" y="5" width="18" height="16" rx="2" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M3 9h18M8 3v4M16 3v4" fill="none" stroke="currentColor" stroke-width="1.6"/>',
			'car'       => '<path d="M5 11l1.5-4.5A2 2 0 018.4 5h7.2a2 2 0 011.9 1.5L19 11M5 11h14v5H5zM5 16v2M19 16v2" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><circle cx="8" cy="16" r="1.3"/><circle cx="16" cy="16" r="1.3"/>',
			'gauge'     => '<path d="M4 16a8 8 0 1116 0" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M12 16l4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>',
			'sparkle'   => '<path d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8L12 3z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>',
			'check'     => '<path d="M5 12l4 4 10-10" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
			'star'      => '<path d="M12 3l2.9 6 6.1.9-4.5 4.3 1.1 6L12 17.8 6.4 20.2l1.1-6L3 9.9 9.1 9 12 3z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>',
		);

		$key  = isset( $paths[ $name ] ) ? $name : 'shield';
		$body = $paths[ $key ];

		return '<svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true" focusable="false">' . $body . '</svg>';
	}

	/**
	 * Public list of icon keys for the editor dropdown.
	 *
	 * @return array
	 */
	public static function icon_keys() {
		return array( 'shield', 'gear', 'wrench', 'briefcase', 'users', 'award', 'layers', 'chat', 'pin', 'phone', 'calendar', 'car', 'gauge', 'sparkle', 'check', 'star' );
	}
}
