<?php

if ( ! class_exists( 'KPB_Field' ) ) {

	class KPB_Field {

		protected $params;

		function __construct( $params ) {
			$this->params = $params;
		}

		function display() {
			esc_html_e( "I'm abstract field. Override me, please!", 'kopa-page-builder' );
		}
	}

}
