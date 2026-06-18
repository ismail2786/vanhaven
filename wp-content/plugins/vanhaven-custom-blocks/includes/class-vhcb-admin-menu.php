<?php
/**
 * Builds the single "VanHaven" sidebar menu and re-homes module CPTs under it.
 *
 * Driven entirely by VHCB_Registry, so new modules with a CPT automatically
 * appear as submenus with no extra code.
 *
 * @package VanHaven_Custom_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VHCB_Admin_Menu {

	const SLUG = 'vanhaven-custom-blocks';

	/**
	 * Hooks.
	 */
	public function register() {
		add_action( 'admin_menu', array( $this, 'build_menu' ), 11 );
		add_action( 'admin_menu', array( $this, 'hide_standalone_cpt_menus' ), 12 );
		add_filter( 'parent_file', array( $this, 'keep_menu_open' ) );
	}

	/**
	 * Build top-level VanHaven menu + submenus.
	 */
	public function build_menu() {
		add_menu_page(
			__( 'VanHaven', 'vanhaven-custom-blocks' ),
			__( 'VanHaven', 'vanhaven-custom-blocks' ),
			'edit_posts',
			self::SLUG,
			array( $this, 'render_dashboard' ),
			'dashicons-superhero',
			26
		);

		add_submenu_page(
			self::SLUG,
			__( 'Dashboard', 'vanhaven-custom-blocks' ),
			__( 'Dashboard', 'vanhaven-custom-blocks' ),
			'edit_posts',
			self::SLUG,
			array( $this, 'render_dashboard' )
		);

		// Each module CPT becomes a submenu automatically.
		foreach ( VHCB_Registry::instance()->get_cpts() as $cpt => $label ) {
			add_submenu_page(
				self::SLUG,
				$label,
				$label,
				'edit_posts',
				'edit.php?post_type=' . $cpt
			);
		}
	}

	/**
	 * Remove the auto-generated top-level CPT menus (we show them as submenus).
	 */
	public function hide_standalone_cpt_menus() {
		foreach ( array_keys( VHCB_Registry::instance()->get_cpts() ) as $cpt ) {
			remove_menu_page( 'edit.php?post_type=' . $cpt );
		}
	}

	/**
	 * Keep the VanHaven menu highlighted while editing a module CPT.
	 *
	 * @param string $parent_file Current parent file.
	 * @return string
	 */
	public function keep_menu_open( $parent_file ) {
		global $current_screen;
		if ( $current_screen && in_array( $current_screen->post_type, array_keys( VHCB_Registry::instance()->get_cpts() ), true ) ) {
			return self::SLUG;
		}
		return $parent_file;
	}

	/**
	 * Dashboard page — lists every module from the registry.
	 */
	public function render_dashboard() {
		$modules = VHCB_Registry::instance()->get_modules();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'VanHaven Custom Blocks', 'vanhaven-custom-blocks' ); ?></h1>
			<p><?php esc_html_e( 'All VanHaven blocks, managed from one place. In the page editor, find them under the "VanHaven" block category.', 'vanhaven-custom-blocks' ); ?></p>

			<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:16px;margin-top:20px;max-width:1000px;">
				<?php foreach ( $modules as $key => $module ) : ?>
					<?php
					$count_txt = '';
					$add_url   = '';
					$manage_url = '';
					if ( ! empty( $module['cpt'] ) ) {
						$counts    = wp_count_posts( $module['cpt'] );
						$published = isset( $counts->publish ) ? (int) $counts->publish : 0;
						/* translators: %d: number of published items. */
						$count_txt  = sprintf( esc_html__( '%d published', 'vanhaven-custom-blocks' ), $published );
						$add_url    = admin_url( 'post-new.php?post_type=' . $module['cpt'] );
						$manage_url = admin_url( 'edit.php?post_type=' . $module['cpt'] );
					}
					$wc_missing = ! empty( $module['needs_wc'] ) && ! class_exists( 'WooCommerce' );
					?>
					<div style="background:#fff;border:1px solid #e0e0e0;border-radius:8px;padding:20px;">
						<h2 style="margin-top:0;font-size:16px;"><?php echo esc_html( $module['label'] ); ?></h2>

						<?php if ( $count_txt ) : ?>
							<p style="color:#666;"><?php echo esc_html( $count_txt ); ?></p>
						<?php endif; ?>

						<?php if ( $wc_missing ) : ?>
							<p><em><?php esc_html_e( 'Requires WooCommerce.', 'vanhaven-custom-blocks' ); ?></em></p>
						<?php endif; ?>

						<p>
							<?php if ( $add_url ) : ?>
								<a class="button button-primary" href="<?php echo esc_url( $add_url ); ?>"><?php esc_html_e( 'Add New', 'vanhaven-custom-blocks' ); ?></a>
								<a class="button" href="<?php echo esc_url( $manage_url ); ?>"><?php esc_html_e( 'Manage', 'vanhaven-custom-blocks' ); ?></a>
							<?php elseif ( ! empty( $module['needs_wc'] ) && class_exists( 'WooCommerce' ) ) : ?>
								<a class="button" href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=product_cat&post_type=product' ) ); ?>"><?php esc_html_e( 'Product categories', 'vanhaven-custom-blocks' ); ?></a>
							<?php endif; ?>
						</p>
					</div>
				<?php endforeach; ?>
			</div>

			<p style="margin-top:24px;color:#666;max-width:1000px;">
				<?php esc_html_e( 'Developer note: to add another block, drop its files under includes/<slug>/ and blocks/<slug>/, then add one entry to the $modules array in class-vhcb-registry.php. It will appear here and in the sidebar automatically.', 'vanhaven-custom-blocks' ); ?>
			</p>
		</div>
		<?php
	}
}
