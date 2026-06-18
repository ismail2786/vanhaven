<?php
/**
 * VH Gallery — "Media" CPT + "Gallery Category" taxonomy (with term thumbnails)
 * + per-item meta (type photo/video, video URL, featured flag).
 *
 * @package VanHaven_Custom_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VHVG_CPT {

	const POST_TYPE = 'vh_media';
	const TAXONOMY  = 'vh_gallery_cat';

	const META_TYPE     = '_vhvg_type';      // 'photo' | 'video'
	const META_VIDEO    = '_vhvg_video_url'; // video URL (YouTube/Vimeo/MP4)
	const META_FEATURED = '_vhvg_featured';  // '1' | ''

	/**
	 * Hooks.
	 */
	public function register() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_taxonomy' ) );
		add_action( 'init', array( $this, 'register_meta' ) );

		// Media item meta box.
		add_action( 'add_meta_boxes', array( $this, 'add_box' ) );
		add_action( 'save_post_' . self::POST_TYPE, array( $this, 'save' ), 10, 1 );

		// Term thumbnail fields.
		add_action( self::TAXONOMY . '_add_form_fields', array( $this, 'term_add_field' ) );
		add_action( self::TAXONOMY . '_edit_form_fields', array( $this, 'term_edit_field' ), 10, 1 );
		add_action( 'created_' . self::TAXONOMY, array( $this, 'term_save' ) );
		add_action( 'edited_' . self::TAXONOMY, array( $this, 'term_save' ) );

		// Media uploader on term screens + meta box.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );

		// Admin columns.
		add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', array( $this, 'columns' ) );
		add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( $this, 'column_content' ), 10, 2 );
	}

	/**
	 * Register CPT.
	 */
	public function register_post_type() {
		$labels = array(
			'name'          => __( 'Van Gallery', 'vanhaven-custom-blocks' ),
			'singular_name' => __( 'Media Item', 'vanhaven-custom-blocks' ),
			'add_new'       => __( 'Add Media', 'vanhaven-custom-blocks' ),
			'add_new_item'  => __( 'Add New Media', 'vanhaven-custom-blocks' ),
			'edit_item'     => __( 'Edit Media', 'vanhaven-custom-blocks' ),
			'new_item'      => __( 'New Media', 'vanhaven-custom-blocks' ),
			'search_items'  => __( 'Search Media', 'vanhaven-custom-blocks' ),
			'not_found'     => __( 'No media yet', 'vanhaven-custom-blocks' ),
			'menu_name'     => __( 'Van Gallery', 'vanhaven-custom-blocks' ),
		);

		register_post_type(
			self::POST_TYPE,
			array(
				'labels'        => $labels,
				'public'        => false,
				'show_ui'       => true,
				'show_in_menu'  => true,
				'show_in_rest'  => true,
				'menu_icon'     => 'dashicons-images-alt2',
				'menu_position' => 29,
				'supports'      => array( 'title', 'thumbnail', 'page-attributes' ),
				'has_archive'   => false,
				'rewrite'       => false,
				'taxonomies'    => array( self::TAXONOMY ),
			)
		);
	}

	/**
	 * Register taxonomy (the Filter-1 categories).
	 */
	public function register_taxonomy() {
		$labels = array(
			'name'          => __( 'Gallery Categories', 'vanhaven-custom-blocks' ),
			'singular_name' => __( 'Gallery Category', 'vanhaven-custom-blocks' ),
			'add_new_item'  => __( 'Add Gallery Category', 'vanhaven-custom-blocks' ),
			'edit_item'     => __( 'Edit Gallery Category', 'vanhaven-custom-blocks' ),
			'menu_name'     => __( 'Categories', 'vanhaven-custom-blocks' ),
		);

		register_taxonomy(
			self::TAXONOMY,
			self::POST_TYPE,
			array(
				'labels'            => $labels,
				'public'            => false,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'hierarchical'      => true,
				'rewrite'           => false,
			)
		);
	}

	/**
	 * Register meta for REST.
	 */
	public function register_meta() {
		$auth = function () {
			return current_user_can( 'edit_posts' );
		};
		register_post_meta( self::POST_TYPE, self::META_TYPE, array(
			'type' => 'string', 'single' => true, 'show_in_rest' => true,
			'sanitize_callback' => 'sanitize_text_field', 'auth_callback' => $auth,
		) );
		register_post_meta( self::POST_TYPE, self::META_VIDEO, array(
			'type' => 'string', 'single' => true, 'show_in_rest' => true,
			'sanitize_callback' => 'esc_url_raw', 'auth_callback' => $auth,
		) );
		register_post_meta( self::POST_TYPE, self::META_FEATURED, array(
			'type' => 'string', 'single' => true, 'show_in_rest' => true,
			'sanitize_callback' => 'sanitize_text_field', 'auth_callback' => $auth,
		) );
	}

	/**
	 * Enqueue WP media uploader on relevant admin screens.
	 *
	 * @param string $hook Admin hook.
	 */
	public function admin_assets( $hook ) {
		$screen = get_current_screen();
		if ( ! $screen ) {
			return;
		}
		$is_media_edit = ( self::POST_TYPE === $screen->post_type && in_array( $hook, array( 'post.php', 'post-new.php' ), true ) );
		$is_term_edit  = ( self::TAXONOMY === $screen->taxonomy );

		if ( $is_media_edit || $is_term_edit ) {
			wp_enqueue_media();
			wp_enqueue_script(
				'vhvg-admin',
				VHCB_URL . 'includes/van-gallery/admin.js',
				array( 'jquery' ),
				VHCB_VERSION,
				true
			);
		}
	}

	/* -------------------- Media item meta box -------------------- */

	/**
	 * Add meta box.
	 */
	public function add_box() {
		add_meta_box(
			'vhvg_media_box',
			__( 'Media Settings', 'vanhaven-custom-blocks' ),
			array( $this, 'render_box' ),
			self::POST_TYPE,
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
		wp_nonce_field( 'vhvg_save', 'vhvg_nonce' );
		$type     = get_post_meta( $post->ID, self::META_TYPE, true );
		$type     = $type ? $type : 'photo';
		$video    = get_post_meta( $post->ID, self::META_VIDEO, true );
		$featured = get_post_meta( $post->ID, self::META_FEATURED, true );
		?>
		<p>
			<strong><?php esc_html_e( 'Type', 'vanhaven-custom-blocks' ); ?></strong><br/>
			<label><input type="radio" name="vhvg_type" value="photo" <?php checked( $type, 'photo' ); ?> /> <?php esc_html_e( 'Photo', 'vanhaven-custom-blocks' ); ?></label>&nbsp;&nbsp;
			<label><input type="radio" name="vhvg_type" value="video" <?php checked( $type, 'video' ); ?> /> <?php esc_html_e( 'Video', 'vanhaven-custom-blocks' ); ?></label>
		</p>
		<p class="vhvg-video-row" style="<?php echo 'video' === $type ? '' : 'display:none;'; ?>">
			<label for="vhvg_video_url"><strong><?php esc_html_e( 'Video URL', 'vanhaven-custom-blocks' ); ?></strong></label>
			<input type="url" id="vhvg_video_url" name="vhvg_video_url" value="<?php echo esc_attr( $video ); ?>" style="width:100%;margin-top:4px;" placeholder="https://youtube.com/watch?v=..." />
			<span class="description"><?php esc_html_e( 'YouTube, Vimeo, or direct MP4 URL. Use the Featured Image as the video poster.', 'vanhaven-custom-blocks' ); ?></span>
		</p>
		<p>
			<label><input type="checkbox" name="vhvg_featured" value="1" <?php checked( $featured, '1' ); ?> /> <?php esc_html_e( 'Mark as Featured', 'vanhaven-custom-blocks' ); ?></label>
		</p>
		<p class="description"><?php esc_html_e( 'Set the image (or video poster) via "Featured image". Assign categories in the Gallery Categories box.', 'vanhaven-custom-blocks' ); ?></p>
		<?php
	}

	/**
	 * Save media item meta.
	 *
	 * @param int $post_id Post ID.
	 */
	public function save( $post_id ) {
		if ( ! isset( $_POST['vhvg_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['vhvg_nonce'] ) ), 'vhvg_save' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$type = isset( $_POST['vhvg_type'] ) && 'video' === $_POST['vhvg_type'] ? 'video' : 'photo';
		update_post_meta( $post_id, self::META_TYPE, $type );

		if ( isset( $_POST['vhvg_video_url'] ) ) {
			update_post_meta( $post_id, self::META_VIDEO, esc_url_raw( wp_unslash( $_POST['vhvg_video_url'] ) ) );
		}

		update_post_meta( $post_id, self::META_FEATURED, isset( $_POST['vhvg_featured'] ) ? '1' : '' );
	}

	/* -------------------- Term thumbnail -------------------- */

	/**
	 * Add-term thumbnail field.
	 */
	public function term_add_field() {
		?>
		<div class="form-field term-thumb-wrap">
			<label><?php esc_html_e( 'Thumbnail', 'vanhaven-custom-blocks' ); ?></label>
			<input type="hidden" name="vhvg_term_thumb" id="vhvg_term_thumb" value="" />
			<div id="vhvg_term_thumb_preview" style="margin:6px 0;"></div>
			<button type="button" class="button vhvg-upload-thumb"><?php esc_html_e( 'Select image', 'vanhaven-custom-blocks' ); ?></button>
			<button type="button" class="button vhvg-remove-thumb"><?php esc_html_e( 'Remove', 'vanhaven-custom-blocks' ); ?></button>
			<p class="description"><?php esc_html_e( 'Shown behind the filter tab (like the screenshot).', 'vanhaven-custom-blocks' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Edit-term thumbnail field.
	 *
	 * @param WP_Term $term Term.
	 */
	public function term_edit_field( $term ) {
		$thumb_id = get_term_meta( $term->term_id, 'vhvg_thumb', true );
		$url      = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'thumbnail' ) : '';
		?>
		<tr class="form-field term-thumb-wrap">
			<th scope="row"><label><?php esc_html_e( 'Thumbnail', 'vanhaven-custom-blocks' ); ?></label></th>
			<td>
				<input type="hidden" name="vhvg_term_thumb" id="vhvg_term_thumb" value="<?php echo esc_attr( $thumb_id ); ?>" />
				<div id="vhvg_term_thumb_preview" style="margin:6px 0;">
					<?php if ( $url ) : ?>
						<img src="<?php echo esc_url( $url ); ?>" style="max-width:120px;height:auto;border-radius:6px;" />
					<?php endif; ?>
				</div>
				<button type="button" class="button vhvg-upload-thumb"><?php esc_html_e( 'Select image', 'vanhaven-custom-blocks' ); ?></button>
				<button type="button" class="button vhvg-remove-thumb"><?php esc_html_e( 'Remove', 'vanhaven-custom-blocks' ); ?></button>
			</td>
		</tr>
		<?php
	}

	/**
	 * Save term thumbnail.
	 *
	 * @param int $term_id Term ID.
	 */
	public function term_save( $term_id ) {
		if ( isset( $_POST['vhvg_term_thumb'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$thumb = absint( wp_unslash( $_POST['vhvg_term_thumb'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
			if ( $thumb ) {
				update_term_meta( $term_id, 'vhvg_thumb', $thumb );
			} else {
				delete_term_meta( $term_id, 'vhvg_thumb' );
			}
		}
	}

	/* -------------------- Admin columns -------------------- */

	/**
	 * Columns.
	 *
	 * @param array $cols Columns.
	 * @return array
	 */
	public function columns( $cols ) {
		$new = array();
		$new['cb']        = isset( $cols['cb'] ) ? $cols['cb'] : '';
		$new['vh_thumb']  = __( 'Media', 'vanhaven-custom-blocks' );
		$new['title']     = __( 'Title', 'vanhaven-custom-blocks' );
		$new['vh_type']   = __( 'Type', 'vanhaven-custom-blocks' );
		$new['vh_feat']   = __( 'Featured', 'vanhaven-custom-blocks' );
		if ( isset( $cols['taxonomy-' . self::TAXONOMY] ) ) {
			$new[ 'taxonomy-' . self::TAXONOMY ] = $cols[ 'taxonomy-' . self::TAXONOMY ];
		}
		$new['menu_order'] = __( 'Order', 'vanhaven-custom-blocks' );
		return $new;
	}

	/**
	 * Column content.
	 *
	 * @param string $column  Column.
	 * @param int    $post_id Post ID.
	 */
	public function column_content( $column, $post_id ) {
		switch ( $column ) {
			case 'vh_thumb':
				echo has_post_thumbnail( $post_id ) ? get_the_post_thumbnail( $post_id, array( 80, 60 ) ) : '&mdash;';
				break;
			case 'vh_type':
				$t = get_post_meta( $post_id, self::META_TYPE, true );
				echo esc_html( 'video' === $t ? __( 'Video', 'vanhaven-custom-blocks' ) : __( 'Photo', 'vanhaven-custom-blocks' ) );
				break;
			case 'vh_feat':
				echo get_post_meta( $post_id, self::META_FEATURED, true ) ? '★' : '&mdash;';
				break;
			case 'menu_order':
				$post = get_post( $post_id );
				echo esc_html( $post ? $post->menu_order : 0 );
				break;
		}
	}
}
