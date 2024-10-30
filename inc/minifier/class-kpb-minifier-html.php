<?php

if( !class_exists( 'KPB_Minifier_HTML' ) ) {

	class KPB_Minifier_HTML extends KPB_Minify {

		protected static $instance = null;

		static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		function minify( $input ) {			
			return $input;			
		}

	}

}