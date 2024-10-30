<?php

if ( ! class_exists( 'KPB_Field_Select' ) ) {

	class KPB_Field_Select extends KPB_Field {

		function display() {

			$selected = esc_html( trim( $this->params['value'] ) );

			?>
			
			<select name="<?php echo esc_attr( $this->params['name'] ); ?>" class="kpb-ui-select" autocomplete=off>
				<?php	foreach ( $this->params['options'] as $value => $title ) : ?>
					<option value="<?php echo $value ?>" <?php selected( $selected, $value, true ); ?>><?php echo esc_html( $title ); ?></option>
					<?php endforeach; ?>
			</select>

			<?php

		}
	}

}
