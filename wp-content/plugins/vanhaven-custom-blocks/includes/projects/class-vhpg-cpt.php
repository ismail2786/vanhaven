<?php
/**
 * "Project" custom post type for the VH Project Gallery.
 *
 * @package VanHaven_Custom_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VHPG_CPT {

	const POST_TYPE = 'vh_project';

	/**
	 * Hooks.
	 */
	public function register() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', array( $this, 'columns' ) );
		add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( $this, 'column_content' ), 10, 2 );
		add_action( 'add_meta_boxes', array( $this, 'add_box' ) );
		add_action( 'save_post_' . self::POST_TYPE, array( $this, 'save' ), 10, 1 );
		add_action( 'init', array( $this, 'register_meta' ) );
	}

	/**
	 * Register the CPT.
	 */
	public function register_post_type() {
		$labels = array(
			'name'          => __( 'Projects', 'vanhaven-custom-blocks' ),
			'singular_name' => __( 'Project', 'vanhaven-custom-blocks' ),
			'add_new'       => __( 'Add Project', 'vanhaven-custom-blocks' ),
			'add_new_item'  => __( 'Add New Project', 'vanhaven-custom-blocks' ),
			'edit_item'     => __( 'Edit Project', 'vanhaven-custom-blocks' ),
			'new_item'      => __( 'New Project', 'vanhaven-custom-blocks' ),
			'search_items'  => __( 'Search Projects', 'vanhaven-custom-blocks' ),
			'not_found'     => __( 'No projects yet', 'vanhaven-custom-blocks' ),
			'menu_name'     => __( 'Projects', 'vanhaven-custom-blocks' ),
		);

		register_post_type(
			self::POST_TYPE,
			array(
				'labels'        => $labels,
				'public'        => false,
				'show_ui'       => true,
				'show_in_menu'  => true,
				'show_in_rest'  => true,
				'menu_icon'     => 'dashicons-format-image',
				'menu_position' => 28,
				'supports'      => array( 'title', 'thumbnail', 'page-attributes' ),
				'has_archive'   => false,
				'rewrite'       => false,
			)
		);
	}

	/**
	 * Register caption meta for REST.
	 */
	public function register_meta() {
		register_post_meta(
			self::POST_TYPE,
			'_vhpg_caption',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}

	/**
	 * Admin columns.
	 *
	 * @param array $cols Columns.
	 * @return array
	 */
	public function columns( $cols ) {
		$new = array();
		$new['cb']         = isset( $cols['cb'] ) ? $cols['cb'] : '';
		$new['vh_thumb']   = __( 'Image', 'vanhaven-custom-blocks' );
		$new['title']      = __( 'Title', 'vanhaven-custom-blocks' );
		$new['menu_order'] = __( 'Order', 'vanhaven-custom-blocks' );
		$new['date']       = isset( $cols['date'] ) ? $cols['date'] : '';
		return $new;
	}

	/**
	 * Column content.
	 *
	 * @param string $column  Column.
	 * @param int    $post_id Post ID.
	 */
	public function column_content( $column, $post_id ) {
		if ( 'vh_thumb' === $column ) {
			echo has_post_thumbnail( $post_id ) ? get_the_post_thumbnail( $post_id, array( 80, 60 ) ) : '&mdash;';
		}
		if ( 'menu_order' === $column ) {
			$post = get_post( $post_id );
			echo esc_html( $post ? $post->menu_order : 0 );
		}
	}

	/**
	 * Caption meta box.
	 */
	public function add_box() {
		add_meta_box(
			'vhpg_caption_box',
			__( 'Project Details', 'vanhaven-custom-blocks' ),
			array( $this, 'render_box' ),
			self::POST_TYPE,
			'side',
			'default'
		);
	}

	/**
	 * Render the meta box.
	 *
	 * @param WP_Post $post Post.
	 */
	public function render_box( $post ) {
		wp_nonce_field( 'vhpg_save', 'vhpg_nonce' );
		$caption = get_post_meta( $post->ID, '_vhpg_caption', true );
		?>
		<p>
			<label for="vhpg_caption"><strong><?php esc_html_e( 'Caption (optional)', 'vanhaven-custom-blocks' ); ?></strong></label>
			<input type="text" id="vhpg_caption" name="vhpg_caption" value="<?php echo esc_attr( $caption ); ?>" style="width:100%;margin-top:6px;" placeholder="<?php esc_attr_e( 'Shown in the lightbox', 'vanhaven-custom-blocks' ); ?>" />
		</p>
		<p class="description"><?php esc_html_e( 'Set the project image via "Featured image". Use Order (Page Attributes) to control sequence.', 'vanhaven-custom-blocks' ); ?></p>
		<?php
	}

	/**
	 * Save handler.
	 *
	 * @param int $post_id Post ID.
	 */
	public function save( $post_id ) {
		if ( ! isset( $_POST['vhpg_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['vhpg_nonce'] ) ), 'vhpg_save' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( isset( $_POST['vhpg_caption'] ) ) {
			update_post_meta( $post_id, '_vhpg_caption', sanitize_text_field( wp_unslash( $_POST['vhpg_caption'] ) ) );
		}
	}
}
