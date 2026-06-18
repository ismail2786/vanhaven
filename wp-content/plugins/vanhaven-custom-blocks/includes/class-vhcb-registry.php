<?php
/**
 * VanHaven block module registry.
 *
 * This is the single place that knows about every block "module" in the plugin.
 * To add a new block later you:
 *   1. Drop its files in  includes/<slug>/  and  blocks/<slug>/
 *   2. Add one entry to the $modules array in get_modules() below.
 * Everything else (loading classes, registering, building the admin menu) is
 * handled automatically from that array.
 *
 * @package VanHaven_Custom_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VHCB_Registry {

	/**
	 * Singleton.
	 *
	 * @var VHCB_Registry|null
	 */
	private static $instance = null;

	/**
	 * Get instance.
	 *
	 * @return VHCB_Registry
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * The module manifest. Each module describes one block + its admin presence.
	 *
	 * Keys:
	 *  - label       : human name shown on the dashboard.
	 *  - files       : class files to require (relative to includes/<dir>/).
	 *  - dir         : sub-directory under includes/ and blocks/.
	 *  - boot        : array of [ ClassName => methodToCallOnInstance ] to run on plugins_loaded.
	 *  - cpt         : optional custom post type slug this module manages (adds a submenu).
	 *  - cpt_label   : optional menu label for the CPT submenu.
	 *  - needs_wc    : whether the block depends on WooCommerce.
	 *
	 * @return array
	 */
	public function get_modules() {
		return array(

			'showcase' => array(
				'label'    => __( 'VH Product Showcase', 'vanhaven-custom-blocks' ),
				'dir'      => 'showcase',
				'files'    => array(
					'class-vhsc-query.php',
					'class-vhsc-rest.php',
					'class-vhsc-block.php',
				),
				'boot'     => array(
					'VHSC_Block' => 'register',
					'VHSC_REST'  => 'register',
				),
				'cpt'      => '',
				'needs_wc' => true,
			),

			'handovers' => array(
				'label'    => __( 'VH Van Handovers', 'vanhaven-custom-blocks' ),
				'dir'      => 'handovers',
				'files'    => array(
					'class-vhhg-cpt.php',
					'class-vhhg-meta.php',
					'class-vhhg-rest.php',
					'class-vhhg-block.php',
				),
				'boot'     => array(
					'VHHG_CPT'   => 'register',
					'VHHG_Meta'  => 'register',
					'VHHG_REST'  => 'register',
					'VHHG_Block' => 'register',
				),
				'cpt'       => 'vh_handover',
				'cpt_label' => __( 'Handovers', 'vanhaven-custom-blocks' ),
				'needs_wc'  => false,
			),

			'solutions' => array(
				'label'    => __( 'VH Solutions Slider', 'vanhaven-custom-blocks' ),
				'dir'      => 'solutions',
				'files'    => array(
					'class-vhs-cpt.php',
					'class-vhs-meta.php',
					'class-vhs-rest.php',
					'class-vhs-block.php',
				),
				'boot'     => array(
					'VHS_CPT'   => 'register',
					'VHS_Meta'  => 'register',
					'VHS_REST'  => 'register',
					'VHS_Block' => 'register',
				),
				'cpt'       => 'vh_solution',
				'cpt_label' => __( 'Solutions', 'vanhaven-custom-blocks' ),
				'needs_wc'  => false,
			),

			'feature-grid' => array(
				'label'    => __( 'VH Feature Grid', 'vanhaven-custom-blocks' ),
				'dir'      => 'feature-grid',
				'files'    => array(
					'class-vhfg-block.php',
				),
				'boot'     => array(
					'VHFG_Block' => 'register',
				),
				'cpt'      => '',
				'needs_wc' => false,
			),

			'process-steps' => array(
				'label'    => __( 'VH Process Steps', 'vanhaven-custom-blocks' ),
				'dir'      => 'process-steps',
				'files'    => array(
					'class-vhps-block.php',
				),
				'boot'     => array(
					'VHPS_Block' => 'register',
				),
				'cpt'      => '',
				'needs_wc' => false,
			),

			'projects' => array(
				'label'    => __( 'VH Project Gallery', 'vanhaven-custom-blocks' ),
				'dir'      => 'projects',
				'files'    => array(
					'class-vhpg-cpt.php',
					'class-vhpg-rest.php',
					'class-vhpg-block.php',
				),
				'boot'     => array(
					'VHPG_CPT'   => 'register',
					'VHPG_REST'  => 'register',
					'VHPG_Block' => 'register',
				),
				'cpt'       => 'vh_project',
				'cpt_label' => __( 'Projects', 'vanhaven-custom-blocks' ),
				'needs_wc'  => false,
			),

			'van-gallery' => array(
				'label'    => __( 'VH Gallery with Filters', 'vanhaven-custom-blocks' ),
				'dir'      => 'van-gallery',
				'files'    => array(
					'class-vhvg-cpt.php',
					'class-vhvg-rest.php',
					'class-vhvg-block.php',
				),
				'boot'     => array(
					'VHVG_CPT'   => 'register',
					'VHVG_REST'  => 'register',
					'VHVG_Block' => 'register',
				),
				'cpt'       => 'vh_media',
				'cpt_label' => __( 'Van Gallery', 'vanhaven-custom-blocks' ),
				'needs_wc'  => false,
			),

			/*
			 * ---- ADD NEW BLOCKS HERE ----
			 * 'pricing' => array(
			 *     'label' => __( 'VH Pricing Table', 'vanhaven-custom-blocks' ),
			 *     'dir'   => 'pricing',
			 *     'files' => array( 'class-vhp-cpt.php', 'class-vhp-block.php' ),
			 *     'boot'  => array( 'VHP_CPT' => 'register', 'VHP_Block' => 'register' ),
			 *     'cpt'       => 'vh_pricing',
			 *     'cpt_label' => __( 'Pricing', 'vanhaven-custom-blocks' ),
			 *     'needs_wc'  => false,
			 * ),
			 */
		);
	}

	/**
	 * Require all module class files.
	 */
	public function load_files() {
		foreach ( $this->get_modules() as $module ) {
			foreach ( $module['files'] as $file ) {
				$path = VHCB_PATH . 'includes/' . $module['dir'] . '/' . $file;
				if ( file_exists( $path ) ) {
					require_once $path;
				}
			}
		}
	}

	/**
	 * Instantiate + boot each module's classes.
	 */
	public function boot_modules() {
		foreach ( $this->get_modules() as $module ) {
			foreach ( $module['boot'] as $class => $method ) {
				if ( class_exists( $class ) ) {
					$instance = new $class();
					if ( method_exists( $instance, $method ) ) {
						$instance->{$method}();
					}
				}
			}
		}
	}

	/**
	 * Get the CPT slugs registered by modules (used for menu re-homing).
	 *
	 * @return array
	 */
	public function get_cpts() {
		$cpts = array();
		foreach ( $this->get_modules() as $module ) {
			if ( ! empty( $module['cpt'] ) ) {
				$cpts[ $module['cpt'] ] = isset( $module['cpt_label'] ) ? $module['cpt_label'] : $module['cpt'];
			}
		}
		return $cpts;
	}
}
