<?php
/**
 * VH Process Steps block — register + server render.
 *
 * Numbered step cards (01, 02, 03...) with title + description. Content stored
 * in block attributes.
 *
 * @package VanHaven_Custom_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VHPS_Block {

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
			VHCB_PATH . 'blocks/process-steps',
			array(
				'render_callback' => array( $this, 'render' ),
			)
		);
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
		$columns    = isset( $attributes['columns'] ) ? absint( $attributes['columns'] ) : 4;
		$accent     = isset( $attributes['accentColor'] ) ? $attributes['accentColor'] : '#F0651F';
		$pad        = isset( $attributes['zeroPad'] ) ? (bool) $attributes['zeroPad'] : true;
		$steps      = isset( $attributes['steps'] ) && is_array( $attributes['steps'] ) ? $attributes['steps'] : array();

		if ( empty( $steps ) ) {
			if ( current_user_can( 'edit_posts' ) ) {
				return '<div class="vhps vhps--empty"><p>' . esc_html__( 'Add steps to this Process block in the block settings.', 'vanhaven-custom-blocks' ) . '</p></div>';
			}
			return '';
		}

		$wrapper = get_block_wrapper_attributes(
			array(
				'class' => 'vhps vhps--cols-' . $columns,
				'style' => '--vhps-accent:' . esc_attr( $accent ) . ';',
			)
		);

		ob_start();
		?>
		<div <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php if ( $heading || $subheading ) : ?>
				<div class="vhps__head">
					<?php if ( $heading ) : ?>
						<h2 class="vhps__title"><?php echo wp_kses_post( $heading ); ?></h2>
					<?php endif; ?>
					<?php if ( $subheading ) : ?>
						<p class="vhps__sub"><?php echo wp_kses_post( $subheading ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="vhps__grid">
				<?php foreach ( $steps as $i => $step ) : ?>
					<?php
					$num   = isset( $step['number'] ) && '' !== $step['number'] ? $step['number'] : (string) ( $i + 1 );
					$num   = $pad && is_numeric( $num ) ? str_pad( $num, 2, '0', STR_PAD_LEFT ) : $num;
					$title = isset( $step['title'] ) ? $step['title'] : '';
					$desc  = isset( $step['description'] ) ? $step['description'] : '';
					?>
					<div class="vhps__step">
						<span class="vhps__num"><?php echo esc_html( $num ); ?></span>
						<?php if ( $title ) : ?>
							<h3 class="vhps__step-title"><?php echo esc_html( $title ); ?></h3>
						<?php endif; ?>
						<?php if ( $desc ) : ?>
							<p class="vhps__step-desc"><?php echo esc_html( $desc ); ?></p>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
