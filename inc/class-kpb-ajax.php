<?php

if ( ! class_exists( 'KPB_Ajax' ) ) {

	class KPB_Ajax {

		protected static $instance = null;

		static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		function __construct() {
			add_action( 'admin_footer', array( $this, 'print_nonce_fields' ) );
		}

		function print_nonce_fields() {

			wp_nonce_field( 'kpb_load_widget_placeholder', 'kpb_load_widget_placeholder_security', false );
			wp_nonce_field( 'kpb_load_widgets', 'kpb_load_widgets_security', false );
			wp_nonce_field( 'kpb_load_widget_form', 'kpb_load_widget_form_security', false );
			wp_nonce_field( 'kpb_delete_widget', 'kpb_delete_widget_security', false );
			wp_nonce_field( 'kpb_load_row_customize', 'kpb_load_row_customize_security', false );
			wp_nonce_field( 'kpb_load_col_customize', 'kpb_load_col_customize_security', false );
			wp_nonce_field( 'kpb_save_layout', 'kpb_save_layout_security', false );
			wp_nonce_field( 'kpb_load_layout', 'kpb_load_layout_security', false );
			wp_nonce_field( 'kpb_load_layout_customize', 'kpb_load_layout_customize_security', false );

			$this->print_loader();
		}

		function print_loader() {
			?>
			<div id="kpb-loading-overlay">
				<span><?php esc_html_e( 'Loading..', 'kopa-page-builder' ); ?></span>
			</div>          
			<?php
		}
	}


}
