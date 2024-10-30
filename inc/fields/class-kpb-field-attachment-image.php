<?php

if ( ! class_exists( 'KPB_Field_Attachment_Image' ) ) {

	class KPB_Field_Attachment_Image extends KPB_Field {

		function display() {

			$thumb              = '';
			$thumb_class        = 'kpb-ui-attachment-image--thumb';
			$btn_add_display    = 'block';
			$btn_remove_display = 'none';

			if( $this->params['value'] ){
				$image = wp_get_attachment_image_src( $this->params['value'] );
				if( isset( $image[0] ) ) {
					$thumb              = $image[0];
					$btn_add_display    = 'none';
					$btn_remove_display = 'block';					
				}
			}
			
			if( !$thumb ){
				$thumb_class .= ' kpb--is_hidden';		
			}

			?>

			<div class="kpb-ui-attachment-image">
				<span onclick="KPB_UI_Attachment_Image.edit( event, jQuery(this) );" class="kpb-ui-attachment-image--action kpb-ui-attachment-image--edit kpbi-plus" style="display:<?php echo esc_html( $btn_add_display ); ?>;"></span>
				<span onclick="KPB_UI_Attachment_Image.remove( event, jQuery(this) );" class="kpb-ui-attachment-image--action kpb-ui-attachment-image--remove kpbi-circle-with-minus" style="display:<?php echo esc_html( $btn_remove_display ); ?>;"></span>
				<input class="kpb-ui-attachment-image--input" type="hidden" name="<?php echo esc_attr( $this->params['name'] ); ?>" value="<?php echo esc_attr( $this->params['value'] ); ?>"/>				
				<img class="<?php echo esc_attr( $thumb_class ); ?>" src="<?php echo esc_url( $thumb ); ?>"/>
			</div>
			
			<?php

		}

	}

}
