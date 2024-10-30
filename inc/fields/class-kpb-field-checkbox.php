<?php

if ( ! class_exists( 'KPB_Field_Checkbox' ) ) {

	class KPB_Field_Checkbox extends KPB_Field {

		function display() {

			$this->params['value'] = isset( $this->params['value'] ) ? isset( $this->params['value'] ) : isset( $this->params['default'] ) ? $this->params['value'] : 'false';
			?>
		  <input value="true" type="checkbox" class="kpb-ui-checbox" autocomplete="off" <?php checked( $this->params['value'], 'true' ); ?> name="<?php echo esc_attr( $this->params['name'] ); ?>">
	  	<?php

		}
	}

}


