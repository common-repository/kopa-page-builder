<?php

if ( ! class_exists( 'KPB_Field_Alert' ) ) {

	class KPB_Field_Alert extends KPB_Field {

		function display() {

			$classes = array( 'kpb-ui-alert' );
			if ( isset( $this->params['class'] ) ) {
				$classes = array_merge( $classes, $this->params['class'] );
			}

			$skin = isset( $this->params['skin'] ) ? trim( $this->params['skin'] ) : 'info';
			array_push( $classes, sprintf( 'kpb-skin-%s', $skin ) );
			?>
		  <div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"><?php echo wp_kses_post( $this->params['message'] ); ?></div>
	  	<?php

		}
	}

}

