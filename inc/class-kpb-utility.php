<?php

if ( ! class_exists( 'KPB_Utility' ) ) {

	class KPB_Utility {

		public static function is_page() {

			global $pagenow, $post;
			$is_page = false;

			if ( in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
				if ( ! empty( $post ) && in_array( $post->post_type, array( 'page' ) ) ) {
					$is_page = true;
				}
			}

			return $is_page;
		}

		public static function str_beautify( $string ) {
			$string = str_replace( '-', ' ', $string );
			$string = str_replace( '_', ' ', $string );
			return ucwords( $string );
		}

		public static function str_uglify( $string ) {
			$string = str_replace( ' ', '_', $string );
			$string = str_replace( '-', '_', $string );
			return strtolower( $string );
		}
	}


}
