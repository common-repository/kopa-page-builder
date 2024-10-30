<?php

if ( ! class_exists( 'KPB_Field_Text' ) ) {

	class KPB_Field_Text extends KPB_Field {

		function display() {

			?>
		  <input name="<?php echo esc_attr( $this->params['name'] ); ?>" value="<?php echo esc_attr( $this->params['value'] ); ?>" type="text" class="kpb-ui-text" autocomplete="off">      
	  	<?php

		}
	}

}
