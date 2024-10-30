<?php

if ( ! class_exists( 'KPB_Shortcode_Site_Url' ) ) {

	class KPB_Shortcode_Site_Url {

		protected static $instance = null;

		static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		function __construct() {
			add_shortcode( 'kpb_home_url', array( $this, 'get_html' ) );
		}

		function get_html( $atts, $content ) {
			return get_site_url();
		}
	}

}
