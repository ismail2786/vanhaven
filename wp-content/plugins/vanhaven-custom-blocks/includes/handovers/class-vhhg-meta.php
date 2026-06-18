<?php
/**
 * Tag/badge meta box for handovers.
 *
 * @package VanHaven_Handovers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VHHG_Meta {

	const META_KEY = '_vhhg_tag';

	/**
	 * Hooks.
	 */
	public function register() {
		add_action( 'add_meta_boxes', array( $this, 'add_box' ) );
		add_action( 'save_post_' . VHHG_CPT::POST_TYPE, array( $this, 'save' ), 10, 2 );

		// Register meta so it is available via REST for the block.
		add_action( 'init', array( $this, 'register_meta' ) );
	}

	/**
	 * Register the meta field for REST exposure.
	 */
	public function register_meta() {
		register_post_meta(
			VHHG_CPT::POST_TYPE,
			self::META_KEY,
			array(
				'type'          => 'string',
				'single'        => true,
				'show_in_rest'  => true,
				'auth_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
	}

	/**
	 * Add the meta box.
	 */
	public function add_box() {
		add_meta_box(
			'vhhg_tag_box',
			__( 'Handover Tag / Badge', 'vanhaven-handovers' ),
			array( $this, 'render_box' ),
			VHHG_CPT::POST_TYPE,
			'side',
			'high'
		);
	}

	/**
	 * Render meta box.
	 *
	 * @param WP_Post $post Post.
	 */
	public function render_box( $post ) {
		wp_nonce_field( 'vhhg_save_tag', 'vhhg_tag_nonce' );
		$value = get_post_meta( $post->ID, self::META_KEY, true );
		?>
		<p>
			<label for="vhhg_tag_field"><strong><?php esc_html_e( 'Badge text', 'vanhaven-handovers' ); ?></strong></label>
			<input
				type="text"
				id="vhhg_tag_field"
				name="vhhg_tag_field"
				value="<?php echo esc_attr( $value ); ?>"
				placeholder="<?php esc_attr_e( 'e.g. Overland Series', 'vanhaven-handovers' ); ?>"
				style="width:100%;margin-top:6px;" />
		</p>
		<p class="description"><?php esc_html_e( 'Shown as the pill badge over the image. Set the image via "Featured image".', 'vanhaven-handovers' ); ?></p>
		<?php
	}

	/**
	 * Save handler.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 */
	public function save( $post_id, $post ) {
		if ( ! isset( $_POST['vhhg_tag_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['vhhg_tag_nonce'] ) ), 'vhhg_save_tag' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( isset( $_POST['vhhg_tag_field'] ) ) {
			update_post_meta( $post_id, self::META_KEY, sanitize_text_field( wp_unslash( $_POST['vhhg_tag_field'] ) ) );
		}
	}
}
