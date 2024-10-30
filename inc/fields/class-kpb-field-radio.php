<?php

if ( ! class_exists( 'KPB_Field_Radio' ) ) {

	class KPB_Field_Radio extends KPB_Field {

		function display() {

			foreach ( $this->params['options'] as $value => $title ) :
				$checked = ! empty( $this->params['value'] ) && ( $this->params['value'] == $value) ? 'checked="checked"' : '';
				$id = wp_generate_password( 4, false, false ) . '-' . $value;
				?>
				<label for="<?php echo esc_attr( $id );?>">
					<span><?php echo esc_attr( $title ); ?></span>
					<input id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $this->params['name'] ); ?>" value="<?php echo esc_attr( $value ); ?>" type="radio" class="kpb-ui-radio" <?php echo $checked; ?> autocomplete="off">     
                </label>            
				<?php
			endforeach;

		}
	}

}
