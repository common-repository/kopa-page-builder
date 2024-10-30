<?php

if ( ! class_exists( 'KPB_Admin_Assets' ) ) {


	class KPB_Admin_Assets {

		protected static $instance = null;

		static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		}

		function admin_enqueue_scripts( $hook ) {

			if ( KPB_Utility::is_page() ) {
				$prefix = 'kpb_';
				$affix  = KPB_IS_DEV ? '' : '.min';
				
				wp_enqueue_media();

				$this->load_css( $prefix, $affix );
				$this->load_js( $prefix, $affix );
				$this->load_localize( $prefix, $affix );

			}

		}

		function load_css( $prefix, $affix ) {
			$dir = KPB_DIR . 'assets/css/';

			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'jquery-magnific-popup', $dir . "magnific-popup{$affix}.css", null, null );
			wp_enqueue_style( 'font-kpb', $dir . "font-kpb{$affix}.css", null, null );
			wp_enqueue_style( $prefix . 'style',  $dir . "style{$affix}.css", null, null );

			$dynamic_css = $this->get_dynamic_css();
			if ( $dynamic_css ) {
				wp_add_inline_style( $prefix . 'style', $dynamic_css );
			}
		}

		function load_js( $prefix, $affix ) {
			$dir = KPB_DIR . 'assets/js/';

			wp_enqueue_script( 'json2' );
			wp_enqueue_script( 'jquery-form' );
			wp_enqueue_script( 'jquery-ui-slider' );
			wp_enqueue_script( 'jquery-ui-tooltip' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'jquery-magnific-popup',  $dir . "jquery.magnific-popup{$affix}.js", array( 'jquery' ), null, true );
			wp_enqueue_script( $prefix . 'script',  $dir . "script{$affix}.js", array( 'jquery' ), null, true );
		}

		function load_localize( $prefix, $affix ) {
			wp_localize_script( $prefix . 'script', 'KPB_Config', array(
				'ajax' => admin_url( 'admin-ajax.php' ),
				'i18n' => array(
				'media_center'                       => esc_html__( 'Media center', 'kopa-page-builder' ),
				'choose_image'                       => esc_html__( 'Choose image', 'kopa-page-builder' ),
				'loading'                            => esc_html__( 'Loading...', 'kopa-page-builder' ),
				'save'                               => esc_html__( 'Save', 'kopa-page-builder' ),
				'saving'                             => esc_html__( 'Saving...', 'kopa-page-builder' ),
				'hide_preview'                       => esc_html__( 'Hide visual layout', 'kopa-page-builder' ),
				'show_preview'                       => esc_html__( 'Show visual layout', 'kopa-page-builder' ),
				'are_you_sure_to_remove_this_widget' => esc_html__( 'Are you sure to remove this widget ?', 'kopa-page-builder' ),
				),
			));
		}

		function get_dynamic_css() {
			global $post;
			$css = false;

			$current_layout = Kopa_Page_Builder::get_current_layout( $post->ID );

			if ( $current_layout ) {

				if ( 'disable' === $current_layout ) {
					$css = '#wp-content-editor-container, #post-status-info, #wp-content-editor-tools .wp-editor-tabs { display: block; } #insert-media-button{ display: inline-block; } #kpb-metabox { display: none; }';
				} else {
					$css = '#wp-content-editor-container, #post-status-info, #wp-content-editor-tools .wp-editor-tabs { display: none; } #insert-media-button{ display: none; } #kpb-metabox { display: block; }';
				}
			}

			return $css;
		}
	}


}
