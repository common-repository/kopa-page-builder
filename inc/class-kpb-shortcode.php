<?php

if ( ! class_exists( 'KPB_Shortcode' ) ) {


	class KPB_Shortcode {

		protected static $instance = null;

		private $fields;

		static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		function __construct() {
			KPB_Shortcode_Site_Url::get_instance();
		}
	}

}
