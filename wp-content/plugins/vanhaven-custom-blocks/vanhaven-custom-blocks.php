<?php
/**
 * Plugin Name:       VanHaven Custom Blocks
 * Plugin URI:        https://example.com/vanhaven-custom-blocks
 * Description:       A growing suite of premium VanHaven blocks for Gutenberg & Kadence (VH Product Showcase, VH Van Handovers, VH Solutions Slider, and more). All managed from one "VanHaven" admin menu and driven by a simple module registry so new blocks are easy to add.
 * Version:           1.4.1
 * Requires at least: 6.2
 * Requires PHP:      7.4
 * Author:            Rajendhar
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       vanhaven-custom-blocks
 *
 * WC requires at least: 7.0
 * WC tested up to:      9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'VHCB_VERSION', '1.4.1' );
define( 'VHCB_FILE', __FILE__ );
define( 'VHCB_PATH', plugin_dir_path( __FILE__ ) );
define( 'VHCB_URL', plugin_dir_url( __FILE__ ) );

/*
 * Module classes were originally separate plugins, each with its own constants.
 * Map every legacy constant to this unified plugin so module code runs unchanged.
 * All *_PATH point at the plugin root (where /blocks lives); *_URL likewise.
 */
foreach ( array( 'VHSC', 'VHHG', 'VHS' ) as $prefix ) {
	if ( ! defined( $prefix . '_VERSION' ) ) {
		define( $prefix . '_VERSION', VHCB_VERSION );
	}
	if ( ! defined( $prefix . '_FILE' ) ) {
		define( $prefix . '_FILE', VHCB_FILE );
	}
	if ( ! defined( $prefix . '_PATH' ) ) {
		define( $prefix . '_PATH', VHCB_PATH );
	}
	if ( ! defined( $prefix . '_URL' ) ) {
		define( $prefix . '_URL', VHCB_URL );
	}
}

// Core infrastructure.
require_once VHCB_PATH . 'includes/class-vhcb-registry.php';
require_once VHCB_PATH . 'includes/class-vhcb-admin-menu.php';

// Load every module's class files via the registry.
VHCB_Registry::instance()->load_files();

/**
 * Declare WooCommerce HPOS compatibility (for the showcase module).
 */
add_action( 'before_woocommerce_init', function () {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
			'custom_order_tables',
			VHCB_FILE,
			true
		);
	}
} );

/**
 * Boot everything.
 */
add_action( 'plugins_loaded', function () {
	load_plugin_textdomain( 'vanhaven-custom-blocks', false, dirname( plugin_basename( VHCB_FILE ) ) . '/languages' );

	// Register + boot all module classes (blocks, REST, CPTs, meta).
	VHCB_Registry::instance()->boot_modules();

	// Build the single VanHaven admin menu.
	( new VHCB_Admin_Menu() )->register();

	// One shared "VanHaven" block category for all blocks.
	add_filter( 'block_categories_all', function ( $cats ) {
		foreach ( $cats as $c ) {
			if ( isset( $c['slug'] ) && 'vanhaven' === $c['slug'] ) {
				return $cats;
			}
		}
		array_unshift( $cats, array(
			'slug'  => 'vanhaven',
			'title' => __( 'VanHaven', 'vanhaven-custom-blocks' ),
		) );
		return $cats;
	} );
} );

/**
 * Activation: register module CPTs, then flush rewrite rules.
 */
register_activation_hook( __FILE__, function () {
	require_once VHCB_PATH . 'includes/class-vhcb-registry.php';
	VHCB_Registry::instance()->load_files();

	// Register any CPTs so their rewrite rules exist before the flush.
	foreach ( VHCB_Registry::instance()->get_modules() as $module ) {
		foreach ( $module['boot'] as $class => $method ) {
			if ( class_exists( $class ) && method_exists( $class, 'register_post_type' ) ) {
				( new $class() )->register_post_type();
			}
		}
	}
	flush_rewrite_rules();
} );

register_deactivation_hook( __FILE__, function () {
	flush_rewrite_rules();
} );
