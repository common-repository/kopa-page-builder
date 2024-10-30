<?php

if ( ! class_exists( 'KPB_Autoloader' ) ) {

	class KPB_Autoloader {

		private $include_path = '';

		function __construct() {
			if ( function_exists( '__autoload' ) ) {
				spl_autoload_register( '__autoload' );
			}

			spl_autoload_register( array( $this, 'autoload' ) );

			$this->include_path = plugin_dir_path( __FILE__ );
		}

		function autoload( $class ) {
			$class = strtolower( $class );
			$file  = $this->__get_file_name_from_class( $class );
			$path  = $this->__filter( '', $class );

			if ( $path && ( ! $this->__load_file( $path . $file ) ) ) {
				$this->__load_file( $this->include_path . $file );
			}
		}

		private function __filter( $path, $class ) {

			if ( 0 === strpos( $class, 'kpb_' ) ) {
				if ( 0 === strpos( $class, 'kpb_field_' ) ) {
					return $this->include_path . 'fields/';
				} else if ( 0 === strpos( $class, 'kpb_minifier_' ) ) {
					return $this->include_path . 'minifier/';
				} else if ( 0 === strpos( $class, 'kpb_shortcode_' ) ) {
					return $this->include_path . 'shortcodes/';
				} else {
					return $this->include_path;
				}
			} else {
				return false;
			}

		}

		private function __get_file_name_from_class( $class ) {
			return 'class-' . str_replace( '_', '-', $class ) . '.php';
		}

		private function __load_file( $path ) {
			$is_ready = false;

			if ( $path && is_readable( $path ) ) {
				include_once $path;
				$is_ready = true;
			}

			return $is_ready;
		}
	}

	new KPB_Autoloader();

}
