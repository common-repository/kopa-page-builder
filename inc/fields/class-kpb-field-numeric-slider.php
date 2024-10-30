<?php

if ( ! class_exists( 'KPB_Field_Numeric_Slider' ) ) {

	class KPB_Field_Numeric_Slider extends KPB_Field {

		function display() {
			
			$start   = isset( $this->params['start'] ) ? esc_attr( $this->params['start'] ) : '';
			$prefix  = isset( $this->params['prefix'] ) ? esc_attr( $this->params['prefix'] ) : '';
			$affix   = isset( $this->params['affix'] ) ? esc_attr( $this->params['affix'] ) : '';
			$preview = isset( $this->params['preview'] ) ? esc_attr( $this->params['preview'] ) : '';
			$min     = isset( $this->params['min'] ) ? esc_attr( $this->params['min'] ) : 0;
			$max     = isset( $this->params['max'] ) ? esc_attr( $this->params['max'] ) : 12;
			$step    = isset( $this->params['step'] ) ? esc_attr( $this->params['step'] ) : 1;
			
			$value   = floatval( $this->params['value'] );
			$percent = round( ( $value / $max ) * 100, 2 ) . '%';

			if( 'percent' === $preview ) {
				$preview_value = $value ? $percent : '';
			}else{
				$preview_value = $prefix . $value . $affix;
			}

			?>
			<div class="kpb-ui-numeric-slider--outer kpb-clearfix">
				<div class="kpb-ui-numeric-slider--control">
					<div class="kpb-ui-numeric-slider--filler" style="width:<?php echo esc_attr( $percent ); ?>"></div>
	  			<span class="kpb-ui-numeric-slider--preview"><?php echo esc_attr( $preview_value ); ?></span>
	  			<input name="<?php echo esc_attr( $this->params['name'] ); ?>" 
		  			value="<?php echo esc_attr( $value ); ?>" 	  			
		  			type="hidden" 
		  			class="kpb-ui-numeric-slider"		  			
		  			autocomplete="off"
		  			data-start="<?php echo esc_attr( $start ); ?>" 		  			
		  			data-prefix="<?php echo esc_attr( $prefix ); ?>" 		  			
		  			data-affix="<?php echo esc_attr( $affix ); ?>" 		  			
		  			data-preview="<?php echo esc_attr( $preview ); ?>" 		  			
		  			data-min="<?php echo esc_attr( $min ); ?>" 
		  			data-max="<?php echo esc_attr( $max ); ?>" 
		  			data-step="<?php echo esc_attr( $step ); ?>"/>						  			
	  		</div>
		  </div>
	  	<?php

		}
	}

}
