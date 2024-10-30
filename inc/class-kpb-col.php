<?php

if ( ! class_exists( 'KPB_Col' ) ) {

	class KPB_Col {

		protected static $instance = null;

		private $cols, $fields, $key_customize;

		static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		function __construct() {
			$this->cols          = apply_filters( 'kopa_page_builder_get_areas', array() );
			$this->cols          = apply_filters( 'kopa_page_builder_get_cols', $this->cols );		
			$this->fields        = apply_filters( 'kopa_page_builder_get_col_fields', array() );
			$this->key_customize = apply_filters( 'kopa_page_builder_get_meta_key_col_customize', 'kopa_page_builder_col_customize' );

			add_action( 'wp_ajax_kpb_load_col_customize', array( $this, 'load_col_customize' ) );
			add_action( 'wp_ajax_kpb_save_col_customize', array( $this, 'save_col_customize' ) );
		}

		function get_key_customize() {
			return $this->key_customize;
		}

		function get_meta_key_customize( $layout_slug, $row_slug, $col_slug ) {
			return sprintf( '%s-%s-%s-%s', $this->key_customize, $layout_slug, $row_slug, $col_slug );
		}

		function get_fields() {
			return $this->fields;
		}

		function has_fields() {
			return ! empty( $this->fields );
		}

		function get_name( $col_slug = '' ) {
			if ( isset( $this->cols[ $col_slug ] ) && ! empty( $this->cols[ $col_slug ] ) ) {
				return esc_html( $this->cols[ $col_slug ] );
			}
		}

		function save_col_customize() {

			check_ajax_referer( 'kpb_save_col_customize', 'security' );

			$layout_slug = isset( $_POST['layout_slug'] ) ? esc_attr( $_POST['layout_slug'] ) : false;
			$row_slug    = isset( $_POST['row_slug'] ) ? esc_attr( $_POST['row_slug'] ) : false;
			$col_slug    = isset( $_POST['col_slug'] ) ? esc_attr( $_POST['col_slug'] ) : false;
			$post_id     = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : false;

			$fields     = $this->get_fields();
			$meta_key   = $this->get_meta_key_customize( $layout_slug, $row_slug, $col_slug );
			$meta_value = array();
			$data       = $_POST[ $meta_key ];

			foreach ( $fields as $tab_slug => $tab ) {

				foreach ( $tab['params'] as $param_key => $param_args ) {
					$value = isset( $data[ $tab_slug ][ $param_key ] ) ? $data[ $tab_slug ][ $param_key ] : (isset( $param_args['default'] ) ? $param_args['default'] : null );
					$meta_value[ $tab_slug ][ $param_key ] = KPB_Data::santinizing( $param_args, $value );
				}
			}

			update_post_meta( $post_id, $meta_key, $meta_value );

			do_action( 'kopa_page_builder_after_save_col_customize', $post_id, $layout_slug, $row_slug, $col_slug, $meta_value );

			exit();
		}

		function get_data( $post_id, $layout_slug, $row_slug, $col_slug ) {
			$meta_key = $this->get_meta_key_customize( $layout_slug, $row_slug, $col_slug );
			return get_post_meta( $post_id, $meta_key, true );
		}

		function load_col_customize() {

			check_ajax_referer( 'kpb_load_col_customize', 'security' );

			$layout_slug = isset( $_POST['layout_slug'] ) ? esc_attr( $_POST['layout_slug'] ) : false;
			$row_slug    = isset( $_POST['row_slug'] ) ? esc_attr( $_POST['row_slug'] ) : false;
			$col_slug    = isset( $_POST['col_slug'] ) ? esc_attr( $_POST['col_slug'] ) : false;
			$post_id     = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : false;

			$col_fields = $this->get_fields();
			$meta_key   = $this->get_meta_key_customize( $layout_slug, $row_slug, $col_slug );
			$data       = $this->get_data( $post_id, $layout_slug, $row_slug, $col_slug );

			ob_start();
			?>

			<div id="<?php echo "kpb-customize-lightbox-{$layout_slug}-{$row_slug}-{$col_slug}"; ?>" class="kpb-customize-lightbox" style="display: none;">             
        
        <section class="kpb-customize">

      		<form name="<?php echo 'kpb-form' ?>"  method="POST" autocomplete="off" onsubmit="KPB_Col.save_customize( event, jQuery(this) );" action="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-ajax.php' ), 'kpb_save_col_customize', 'security' ) ); ?>">
                
      			<input type="hidden" name="layout_slug" value="<?php echo $layout_slug; ?>" autocomplete="off">
      			<input type="hidden" name="row_slug" value="<?php echo $row_slug; ?>" autocomplete="off">
      			<input type="hidden" name="col_slug" value="<?php echo $col_slug; ?>" autocomplete="off">
                <input type="hidden" name="action" value="kpb_save_col_customize" autocomplete="off">

                <header class="kpb-customize-header kpb-clearfix">
							<label class="kpb-section-title kpb-pull-left"><?php esc_html_e( 'Edit Column', 'kopa-page-builder' ); ?></label>

							<a href="#" onclick="KPB_Col.close_customize(event);" class="button button-link button-delete kpb-pull-right"><?php esc_html_e( 'Close', 'kopa-page-builder' ); ?></a>
							<button type="submit" class="button button-primary kpb-pull-right"><?php esc_html_e( 'Save', 'kopa-page-builder' ); ?></button>                                                             
                        </header>

                        <div class="kpb-form-inner kpb-clearfix">
                            <div class="kpb-wrapper-configuration">                                 
                                <div class="kpb-wrapper-configuration-toggle kpb-tabs">
                                    <nav>
                                        <ul class="kpb-clearfix">
											<?php
											$_is_first_tab = true;
											foreach ( $col_fields  as $fields_slug => $fields ) :
												$classes = $_is_first_tab ? 'kpb-tab-title kpb-tab-title-first kpb-tab-title-active' : 'kpb-tab-title';
												$tab_id  = sprintf( 'layout-%s-row-%s-col-%s-fields-%s', $layout_slug, $row_slug, $col_slug, $fields_slug );
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
									foreach ( $col_fields  as $fields_slug => $fields ) :
										$display = $_is_first_tab ? 'block' : 'none';
										$tab_id  = sprintf( 'layout-%s-row-%s-col-%s-fields-%s', $layout_slug, $row_slug, $col_slug, $fields_slug );
										?>
										<div id="<?php echo $tab_id; ?>" class="kpb-tab-content" style="display:<?php echo $display;?>;">
											<?php
											foreach ( $fields['params'] as $param_key => $param_args ) {

												$param_args['name'] = sprintf( '%s[%s][%s]', $meta_key, $fields_slug, $param_key );
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
	}


}
