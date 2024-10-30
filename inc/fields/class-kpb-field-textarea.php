<?php

if ( ! class_exists( 'KPB_Field_Textarea' ) ) {

	class KPB_Field_Textarea extends KPB_Field {

		function display() {

			$class = isset( $this->params['class'] ) && ! empty( $this->params['class'] ) ? $this->params['class'] : '';
			$rows = isset( $this->params['rows'] ) && ! empty( $this->params['rows'] ) ? (int) $this->params['rows'] : 3;
			?>
		  <textarea name="<?php echo esc_attr( $this->params['name'] ); ?>" class="kpb-ui-textarea <?php echo $class;?>" rows="<?php echo $rows; ?>" autocomplete="off"><?php echo htmlspecialchars_decode( stripslashes( $this->params['value'] ) ); ?></textarea>
	  	<?php

		}
	}

}
