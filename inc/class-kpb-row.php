<?php

if ( ! class_exists( 'KPB_Row' ) ) {


	class KPB_Row {

		protected static $instance = null;

		private $fields, $key_customize;

		static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		function __construct() {
			$this->key_customize = apply_filters( 'kopa_page_builder_get_meta_key_wrapper','kopa_page_builder_wrapper' );
			$this->fields        = apply_filters( 'kopa_page_builder_get_section_fields', array() );
			$this->fields        = apply_filters( 'kopa_page_builder_get_row_fields', $this->fields );
			
			add_action( 'wp_ajax_kpb_load_row_customize', array( $this, 'load_customize_form' ) );
			add_action( 'wp_ajax_kpb_save_row_customize', array( $this, 'save_customize' ) );
		}

		function get_fields() {
			return $this->fields;
		}

		function get_key_customize() {
			return $this->key_customize;
		}

		function get_meta_key_customize( $layout_slug, $row_slug ) {
			return sprintf( '%s-%s-%s', $this->get_key_customize(), $layout_slug, $row_slug );
		}

		function load_customize_form() {

			check_ajax_referer( 'kpb_load_row_customize', 'security' );

			$layout_slug    = isset( $_POST['layout_slug'] ) ? esc_attr( $_POST['layout_slug'] ) : false;
			$row_slug       = isset( $_POST['row_slug'] ) ? esc_attr( $_POST['row_slug'] ) : false;
			$post_id        = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : false;
			$section_fields = $this->get_fields();
			$data           = Kopa_Page_Builder::get_current_wrapper_data( $post_id, $layout_slug, $row_slug );

			ob_start();
			?>

			<div id="<?php echo "kpb-customize-lightbox-{$layout_slug}-{$row_slug}"; ?>" class="kpb-customize-lightbox" style="display: none;">             
        
        <section class="kpb-customize">

      		<form name="<?php echo "kpb-form-customize-layout-{$layout_slug}-section-{$row_slug}" ?>"  method="POST" autocomplete="off" onsubmit="KPB_Row.save(event, jQuery(this), <?php echo (int) $post_id;?>);" action="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-ajax.php' ), 'kpb_save_row_customize', 'security' ) ); ?>">
                
      			<input type="hidden" name="layout_slug" value="<?php echo $layout_slug; ?>" autocomplete="off">
      			<input type="hidden" name="row_slug" value="<?php echo $row_slug; ?>" autocomplete="off">
                <input type="hidden" name="action" value="kpb_save_row_customize" autocomplete="off">

                <header class="kpb-customize-header kpb-clearfix">
							<label class="kpb-section-title kpb-pull-left"><?php esc_html_e( 'Edit Row', 'kopa-page-builder' ); ?></label>
							<a href="#" onclick="KPB_Row.close( event );" class="button button-link button-delete kpb-pull-right"><?php esc_html_e( 'Close', 'kopa-page-builder' ); ?></a>
							<button type="submit" class="button button-primary kpb-pull-right"><?php esc_html_e( 'Save', 'kopa-page-builder' ); ?></button>                                                             
                        </header>

                        <div class="kpb-form-inner kpb-clearfix">
                            <div class="kpb-wrapper-configuration">                                 
                                <div class="kpb-wrapper-configuration-toggle kpb-tabs">
                                    <nav>
                                        <ul class="kpb-clearfix">
											<?php
											$_is_first_tab = true;
											foreach ( $section_fields  as $fields_slug => $fields ) :
												$classes = $_is_first_tab ? 'kpb-tab-title kpb-tab-title-first kpb-tab-title-active' : 'kpb-tab-title';
												$tab_id = $row_slug . '-field-' . $fields_slug;
												?>
												<li class="<?php echo $classes;?>">
													<a href="<?php echo "#{$tab_id}"; ?>"><?php echo esc_attr( $fields['title'] ); ?></a>
                                                </li>
												<?php
												$_is_first_tab = false;
											endforeach;
											?>
                                        </ul>
                                    </nav>

									<?php
									$_is_first_tab = true;
									foreach ( $section_fields  as $fields_slug => $fields ) :
										$display = $_is_first_tab ? 'block' : 'none';
										$tab_id = $row_slug . '-field-' . $fields_slug;
										?>
										<div id="<?php echo $tab_id; ?>" class="kpb-tab-content" style="display:<?php echo $display;?>;">
											<?php
											foreach ( $fields['params'] as $param_key => $param_args ) {

												$param_args['name'] = sprintf( '%s[%s][%s]', $this->get_meta_key_customize( $layout_slug, $row_slug ), $fields_slug, $param_key );
												$param_args['value'] = isset( $data[ $fields_slug ][ $param_key ] ) ? $data[ $fields_slug ][ $param_key ] : (isset( $param_args['default'] ) ? $param_args['default'] : null);

												KPB_UI::get_control( $param_args );

											}
											?>
                                        </div>
										<?php
										$_is_first_tab = false;
									endforeach;
									?>      
                                </div>
                            </div>                                  
                        </div>

            </form>

        </section>

      </div>

			<?php
			$html          = ob_get_clean();
			$html_minifier = KPB_Minifier_HTML::get_instance();

			echo $html_minifier->minify( $html );

			exit();
		}

		function get_data( $post_id, $layout_slug, $row_slug ) {
			$meta_key = $this->get_meta_key_customize( $layout_slug, $row_slug );
			return get_post_meta( $post_id, $meta_key, true );
		}

		function save_customize() {

			check_ajax_referer( 'kpb_save_row_customize', 'security' );

			$layout_slug = isset( $_POST['layout_slug'] ) ? esc_attr( $_POST['layout_slug'] ) : false;
			$row_slug    = isset( $_POST['row_slug'] ) ? esc_attr( $_POST['row_slug'] ) : false;
			$post_id     = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : false;

			$meta_key   = $this->get_meta_key_customize( $layout_slug, $row_slug );
			$data       = isset( $_POST[ $meta_key ] ) ? $_POST[ $meta_key ] : array();
			$meta_value = array();

			$obj_row = KPB_Row::get_instance();
			$fields  = $obj_row->get_fields();

			foreach ( $fields as $tab_slug => $tab ) {

				foreach ( $tab['params'] as $param_key => $param_args ) {
					$_value = isset( $data[ $tab_slug ][ $param_key ] ) ? $data[ $tab_slug ][ $param_key ] : (isset( $param_args['default'] ) ? $param_args['default'] : null);
					$meta_value[ $tab_slug ][ $param_key ] = KPB_Data::santinizing( $param_args, $_value );
				}
			}

			update_post_meta( $post_id, $meta_key, $meta_value );

			do_action( 'kopa_page_builder_after_save_row_customize', $post_id, $layout_slug, $meta_value, $row_slug );

			/**
			 * Do action after save data.
			 * @deprecated since 2.0.0
			 */
			do_action( 'kopa_page_builder_after_save_section_customize', $post_id, $layout_slug, $meta_value, $row_slug );

			exit();

		}
	}


}
