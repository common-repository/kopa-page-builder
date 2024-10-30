<?php

if ( ! class_exists( 'KPB_UI' ) ) {

	class KPB_UI {

		static function get_control( $param_args ) {
			$is_hide_title     = ( isset( $param_args['is_hide_title'] ) && ( true === $param_args['is_hide_title'] ) ) ? true : false;
			$right_col_classes = $is_hide_title ? 'kpb-col-12' : 'kpb-col-9';
			?>

      <div class="kpb-control kpb-clearfix">
        <div class="kpb-row">

					<?php
					if ( ! $is_hide_title  ) {
						echo '<div class="kpb-col-3">';
						echo esc_attr( $param_args['title'] );
						echo '</div>';
					}
					?>

					<div class="<?php echo esc_attr( $right_col_classes ); ?>">
						
						<?php
						$obj = new KPB_Field( $param_args );

						switch ( $param_args['type'] ) {
							case 'alert':
								$obj = new KPB_Field_Alert( $param_args );
								break;
							case 'color':
								$obj = new KPB_Field_Color( $param_args );
								break;
							case 'image':
								$obj = new KPB_Field_Image( $param_args );
								break;
							case 'select':
								$obj = new KPB_Field_Select( $param_args );
								break;
							case 'text':
								$obj = new KPB_Field_Text( $param_args );
								break;
							case 'number':
								$obj = new KPB_Field_Number( $param_args );
								break;
							case 'numeric_slider':
								$obj = new KPB_Field_Numeric_Slider( $param_args );
								break;
							case 'checkbox':
								$obj = new KPB_Field_Checkbox( $param_args );
								break;
							case 'radio':
								$obj = new KPB_Field_Radio( $param_args );
								break;
							case 'radio_image':
								$obj = new KPB_Field_Radio_Image( $param_args );
	              break;
							case 'textarea':
								$obj = new KPB_Field_Textarea( $param_args );
								break;
							case 'icon':
								$obj = new KPB_Field_Icon( $param_args );
								break;
							case 'attachment_image':
								$obj = new KPB_Field_Attachment_Image( $param_args );
								break;
							default:
								$obj = apply_filters( 'kopa_page_builder_get_control_' . $param_args['type'], $param_args );
								break;
						}

						if ( $obj instanceof KPB_Field ) {
							$obj->display();
						}

						if ( isset( $param_args['help'] ) && ! empty( $param_args['help'] ) ) {
							echo '<div class="kpb-ui-help-text">';
								echo wp_kses_post( $param_args['help'] );
							echo '</div>';
						}
						?> 

          </div>

        </div>
      </div>

			<?php

		}
	}

}
