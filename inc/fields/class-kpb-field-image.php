<?php

if ( ! class_exists( 'KPB_Field_Image' ) ) {

	class KPB_Field_Image extends KPB_Field {

		function display() {

			$preview       = KPB_DIR . 'assets/images/placehold.png';
			$image         = ! empty( $this->params['value'] ) ? do_shortcode( $this->params['value'] ) : '';
			$image_reset   = (isset( $this->params['default'] ) && ! empty( $this->params['default'] )) ? do_shortcode( $this->params['default'] ) : '';
			$image_preview = $image ? $image : $preview;
			?>
		  
		  <div class="kpb-ui-image-outer">
        <div class="kpb-clearfix">
		    	<input name="<?php echo esc_attr( $this->params['name'] ); ?>" value="<?php echo esc_url( $image ); ?>" type="text" class="kpb-ui-image kpb-pull-left" autocomplete="off">
		    	<a href="#" class="kpb-ui-image-button-upload button button-secondary kpb-pull-left"><?php esc_html_e( 'Upload', 'kopa-page-builder' ); ?></a>
		    	<a href="#" class="kpb-ui-image-button-reset button button-link button-delete kpb-pull-left" data-preview="<?php echo esc_url( $preview ); ?>" data-reset="<?php echo esc_url( $image_reset ); ?>"><?php esc_html_e( 'Reset', 'kopa-page-builder' ); ?></a>
        </div>    
        <br/>
	    	<img src="<?php echo $image_preview;?>" class="kpb-ui-image-preview" data-preview="<?php echo esc_url( $preview ); ?>">      
      
      </div>

	  	<?php

		}
	}

}



