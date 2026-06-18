<?php
/**
 * Meta box: all the fields for a Solution (heading, description, badge, CTA,
 * and a repeatable list of feature bullets).
 *
 * @package VanHaven_Solutions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VHS_Meta {

	const HEADING     = '_vhs_heading';
	const DESCRIPTION = '_vhs_description';
	const BADGE       = '_vhs_badge';
	const CTA_LABEL   = '_vhs_cta_label';
	const CTA_URL     = '_vhs_cta_url';
	const FEATURES    = '_vhs_features';

	/**
	 * Hooks.
	 */
	public function register() {
		add_action( 'add_meta_boxes', array( $this, 'add_box' ) );
		add_action( 'save_post_' . VHS_CPT::POST_TYPE, array( $this, 'save' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
		add_action( 'init', array( $this, 'register_meta' ) );
	}

	/**
	 * Register scalar meta for REST exposure (features handled in REST class).
	 */
	public function register_meta() {
		$strings = array( self::HEADING, self::DESCRIPTION, self::BADGE, self::CTA_LABEL, self::CTA_URL );
		foreach ( $strings as $key ) {
			register_post_meta(
				VHS_CPT::POST_TYPE,
				$key,
				array(
					'type'              => 'string',
					'single'            => true,
					'show_in_rest'      => true,
					'sanitize_callback' => ( self::CTA_URL === $key ) ? 'esc_url_raw' : 'sanitize_text_field',
					'auth_callback'     => function () {
						return current_user_can( 'edit_posts' );
					},
				)
			);
		}
	}

	/**
	 * Enqueue admin JS for the repeatable features UI.
	 *
	 * @param string $hook Admin page hook.
	 */
	public function assets( $hook ) {
		$screen = get_current_screen();
		if ( ! $screen || VHS_CPT::POST_TYPE !== $screen->post_type ) {
			return;
		}
		if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
			return;
		}
		wp_enqueue_script(
			'vhs-admin',
			VHS_URL . 'includes/solutions/admin.js',
			array(),
			VHS_VERSION,
			true
		);
	}

	/**
	 * Small inline CSS for the meta box.
	 *
	 * @return string
	 */
	private function inline_css() {
		return '
			.vhs-field{margin:0 0 16px}
			.vhs-field label{display:block;font-weight:600;margin-bottom:4px}
			.vhs-field input[type=text],.vhs-field input[type=url],.vhs-field textarea{width:100%}
			.vhs-feature-row{display:flex;gap:8px;margin-bottom:8px;align-items:center}
			.vhs-feature-row input{flex:1}
			.vhs-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
			@media(max-width:782px){.vhs-grid{grid-template-columns:1fr}}
		';
	}

	/**
	 * Add meta box.
	 */
	public function add_box() {
		add_meta_box(
			'vhs_fields',
			__( 'Solution Content', 'vanhaven-solutions' ),
			array( $this, 'render_box' ),
			VHS_CPT::POST_TYPE,
			'normal',
			'high'
		);
	}

	/**
	 * Render meta box.
	 *
	 * @param WP_Post $post Post.
	 */
	public function render_box( $post ) {
		wp_nonce_field( 'vhs_save', 'vhs_nonce' );
		?>
		<style><?php echo $this->inline_css(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></style>
		<?php
		$heading = get_post_meta( $post->ID, self::HEADING, true );
		$desc    = get_post_meta( $post->ID, self::DESCRIPTION, true );
		$badge   = get_post_meta( $post->ID, self::BADGE, true );
		$cta_lbl = get_post_meta( $post->ID, self::CTA_LABEL, true );
		$cta_url = get_post_meta( $post->ID, self::CTA_URL, true );
		$features = get_post_meta( $post->ID, self::FEATURES, true );
		if ( ! is_array( $features ) || empty( $features ) ) {
			$features = array( '' );
		}
		?>
		<p class="description" style="margin-bottom:16px;">
			<?php esc_html_e( 'The post Title is used as the tab label. Set the image via "Featured image".', 'vanhaven-solutions' ); ?>
		</p>

		<div class="vhs-field">
			<label for="vhs_heading"><?php esc_html_e( 'Card heading', 'vanhaven-solutions' ); ?></label>
			<input type="text" id="vhs_heading" name="vhs_heading" value="<?php echo esc_attr( $heading ); ?>" placeholder="<?php esc_attr_e( 'e.g. The Executive Shuttle', 'vanhaven-solutions' ); ?>" />
		</div>

		<div class="vhs-field">
			<label for="vhs_description"><?php esc_html_e( 'Description', 'vanhaven-solutions' ); ?></label>
			<textarea id="vhs_description" name="vhs_description" rows="3" placeholder="<?php esc_attr_e( 'Premium corporate transport for high-end hospitality and VIP travel.', 'vanhaven-solutions' ); ?>"><?php echo esc_textarea( $desc ); ?></textarea>
		</div>

		<div class="vhs-field">
			<label><?php esc_html_e( 'Feature bullets', 'vanhaven-solutions' ); ?></label>
			<div id="vhs-features">
				<?php foreach ( $features as $f ) : ?>
					<div class="vhs-feature-row">
						<input type="text" name="vhs_features[]" value="<?php echo esc_attr( $f ); ?>" placeholder="<?php esc_attr_e( 'Feature text', 'vanhaven-solutions' ); ?>" />
						<button type="button" class="button vhs-remove-feature" aria-label="<?php esc_attr_e( 'Remove', 'vanhaven-solutions' ); ?>">&times;</button>
					</div>
				<?php endforeach; ?>
			</div>
			<button type="button" class="button button-secondary" id="vhs-add-feature">+ <?php esc_html_e( 'Add feature', 'vanhaven-solutions' ); ?></button>
		</div>

		<div class="vhs-grid">
			<div class="vhs-field">
				<label for="vhs_badge"><?php esc_html_e( 'Badge text', 'vanhaven-solutions' ); ?></label>
				<input type="text" id="vhs_badge" name="vhs_badge" value="<?php echo esc_attr( $badge ); ?>" placeholder="<?php esc_attr_e( 'e.g. Bespoke Build', 'vanhaven-solutions' ); ?>" />
			</div>
			<div class="vhs-field">
				<label for="vhs_cta_label"><?php esc_html_e( 'Button label', 'vanhaven-solutions' ); ?></label>
				<input type="text" id="vhs_cta_label" name="vhs_cta_label" value="<?php echo esc_attr( $cta_lbl ); ?>" placeholder="<?php esc_attr_e( 'e.g. Enquire for Hospitality', 'vanhaven-solutions' ); ?>" />
			</div>
		</div>

		<div class="vhs-field">
			<label for="vhs_cta_url"><?php esc_html_e( 'Button link (URL)', 'vanhaven-solutions' ); ?></label>
			<input type="url" id="vhs_cta_url" name="vhs_cta_url" value="<?php echo esc_attr( $cta_url ); ?>" placeholder="https://" />
		</div>
		<?php
	}

	/**
	 * Save handler.
	 *
	 * @param int $post_id Post ID.
	 */
	public function save( $post_id ) {
		if ( ! isset( $_POST['vhs_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['vhs_nonce'] ) ), 'vhs_save' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$map = array(
			self::HEADING   => 'vhs_heading',
			self::DESCRIPTION => 'vhs_description',
			self::BADGE     => 'vhs_badge',
			self::CTA_LABEL => 'vhs_cta_label',
		);
		foreach ( $map as $meta_key => $field ) {
			if ( isset( $_POST[ $field ] ) ) {
				update_post_meta( $post_id, $meta_key, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
			}
		}

		if ( isset( $_POST['vhs_cta_url'] ) ) {
			update_post_meta( $post_id, self::CTA_URL, esc_url_raw( wp_unslash( $_POST['vhs_cta_url'] ) ) );
		}

		// Repeatable features.
		$features = array();
		if ( isset( $_POST['vhs_features'] ) && is_array( $_POST['vhs_features'] ) ) {
			foreach ( wp_unslash( $_POST['vhs_features'] ) as $f ) {
				$clean = sanitize_text_field( $f );
				if ( '' !== trim( $clean ) ) {
					$features[] = $clean;
				}
			}
		}
		update_post_meta( $post_id, self::FEATURES, $features );
	}
}
