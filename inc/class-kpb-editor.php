<?php

if ( ! class_exists( 'KPB_Editor' ) ) {

	class KPB_Editor {

		protected static $instance = null;

		static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		function __construct() {
			add_action( 'media_buttons', array( $this, 'add_toggle_editor' ) );
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_filter( 'tiny_mce_before_init', array( $this, 'unset_autoresize_on' ) );
		}

		function admin_init() {
			wp_deregister_script( 'editor-expand' );
		}

		function unset_autoresize_on( $init ) {
			unset( $init['wp_autoresize_on'] );
			return $init;
		}

		function add_toggle_editor() {

			if ( KPB_Utility::is_page() ) {
				global $post;
				$current_layout = Kopa_Page_Builder::get_current_layout( $post->ID );
				$button_classes = ( 'disable' === $current_layout ) ? 'button' : 'button button-primary';

					echo '<a href="#" id="kpb-toggle-editor" onclick="KPB.toggle(event);" class="' . esc_attr( $button_classes ) . '"><span class="wp-media-buttons-icon dashicons dashicons-schedule"></span>' . esc_html__( 'Page Builder', 'kopa-page-builder' ) . '</a>';
			}

		}
	}


}
