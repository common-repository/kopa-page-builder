<?php

if ( ! class_exists( 'KPB_Field_Color' ) ) {

	class KPB_Field_Color extends KPB_Field {

		function display() {

			?>
		  <input name="<?php echo esc_attr( $this->params['name'] ); ?>" value="<?php echo esc_attr( $this->params['value'] ); ?>" type="text" class="kpb-ui-color" data-default-color="<?php echo isset( $this->params['default'] ) ? $this->params['default'] : ''; ?>" autocomplete="off">
	  	<?php

		}
	}

}
