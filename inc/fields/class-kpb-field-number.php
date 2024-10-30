<?php

if ( ! class_exists( 'KPB_Field_Number' ) ) {

	class KPB_Field_Number extends KPB_Field {

		function display() {

			?>
		  <input name="<?php echo esc_attr( $this->params['name'] ); ?>" value="<?php echo esc_attr( $this->params['value'] ); ?>" type="text" class="kpb-ui-number" autocomplete="off">        
	  	<?php if ( $this->params['affix'] ) : ?>
	  		<i><?php echo $this->params['affix']; ?></i>
	  	<?php endif;?>
	  	<?php

		}
	}

}
