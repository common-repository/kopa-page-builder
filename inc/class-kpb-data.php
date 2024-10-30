<?php

if ( ! class_exists( 'KPB_Data' ) ) {

	class KPB_Data {

		public static function santinizing( $param_args, $value ) {
			$output = $value;

			switch ( $param_args['type'] ) {

				case 'color':
					$output = esc_attr( $value );
					break;

				case 'image':
					if ( ! empty( $value ) ) {
						$output = str_replace( home_url(), '[kpb_home_url]', $value );
					}
					break;

				case 'select':
					$output = esc_attr( $value );
					break;

				case 'text':
					$output = esc_attr( $value );
					break;

				case 'number':
					if ( trim( $value ) != '' ) {
						$output = floatval( $value );
					}
					break;

				case 'textarea':					
					$output = wp_kses_post( $value );
					break;

				case 'numeric_slider':
				
					$prefix = isset( $param_args['prefix'] ) ? esc_attr( $param_args['prefix'] ) : '';					
					if( $prefix ) {
						$output = str_replace( $prefix, '', $value );					
					}

					$affix  = isset( $param_args['affix'] ) ? esc_attr( $param_args['affix'] ) : '';
					if( $affix ) {
						$output = str_replace( $affix, '', $output );					
					}

					$output = floatval( $output );

					break;
			}

			$output = apply_filters( 'kopa_page_builder_sanitize_control_' . $param_args['type'], $output, $param_args, $value );

			return $output;
		}

		public static function clean_layout_data( $post_id, $layout_slug ) {
			global $wpdb;

			$obj_layout = KPB_Layout::get_instance();
			$obj_row    = KPB_Row::get_instance();
			$obj_col    = KPB_Col::get_instance();

			$keys[] = sprintf( '%s-%s', $obj_row->get_key_customize(), $layout_slug );
			$keys[] = sprintf( '%s-%s', $obj_layout->get_key_customize(), $layout_slug );
			$keys[] = sprintf( '%s-%s', $obj_col->get_key_customize(), $layout_slug );

			foreach ( $keys as $key ) {
				$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE post_id = %d AND meta_key LIKE %s", $post_id, "{$key}%" ) );
			}

			wp_reset_query();
		}
	}

}
