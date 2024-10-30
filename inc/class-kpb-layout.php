<?php

if ( ! class_exists( 'KPB_Layout' ) ) {


	class KPB_Layout {

		protected static $instance = null;

		private $layouts = array(), $key_grid = '', $key_customize = '', $key_current_layout = '';

		static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		function __construct() {
			$this->init_layouts();
			
			$this->layouts            = apply_filters( 'kopa_page_builder_get_layouts', $this->layouts );
			$this->key_grid           = apply_filters( 'kopa_page_builder_get_meta_key_grid','kopa_page_builder_data' );
			$this->key_customize      = apply_filters( 'kopa_page_builder_get_meta_key_layout_customize','kopa_page_builder_layout_customize' );
			$this->key_current_layout = apply_filters( 'kopa_page_builder_get_meta_key_current_layout','kopa_page_builder_current_layout' );

			add_action( 'wp_ajax_kpb_load_layout', array( $this, 'load_layout' ) );
			add_action( 'wp_ajax_kpb_save_layout', array( $this, 'save_layout' ) );
			add_action( 'wp_ajax_kpb_load_layout_customize', array( $this, 'load_layout_customize' ) );
			add_action( 'wp_ajax_kpb_save_layout_customize', array( $this, 'save_layout_customize' ) );
		}

		function init_layouts() {
			$this->layouts['disable'] = array(
	       'title' => esc_html__( '-- Disable --', 'kopa-page-builder' )
	    );
		}

		function get_layouts() {
			return $this->layouts;
		}

		function get_layout( $layout_slug ) {
			if ( isset( $this->layouts[ $layout_slug ] ) && ! empty( $this->layouts[ $layout_slug ] ) ) {
				return $this->layouts[ $layout_slug ];
			}
		}

		function get_key_customize() {
			return $this->key_customize;
		}

		function get_meta_key_customize( $layout_slug ) {
			return sprintf( '%s-%s', $this->get_key_customize(), $layout_slug );
		}

		function get_key_grid() {
			return $this->key_grid;
		}

		function get_meta_key_grid( $layout_slug ) {
			return sprintf( '%s-%s', $this->get_key_grid(), $layout_slug );
		}

		function get_key_current_layout() {
			return $this->key_current_layout;
		}

		function load_layout() {

			check_ajax_referer( 'kpb_load_layout', 'security' );

			$layout_slug = isset( $_POST['layout_slug'] ) ? esc_html( $_POST['layout_slug'] ) : false;
			$post_id     = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : false;

			$obj_col = KPB_Col::get_instance();
			$obj_row = KPB_Row::get_instance();

			$layout         = $this->get_layout( $layout_slug );
			$row_fields     = $obj_row->get_fields();
			$col_has_fields = $obj_col->has_fields();

			$layout_saved_data = $this->get_data( $post_id, $layout_slug );
			$has_customize     = ( isset( $layout['customize'] ) && ! empty( $layout['customize'] ) ) ? 1 : 0;
			$has_preview       = ( isset( $layout['preview'] ) && ! empty( $layout['preview'] ) ) ? 1 : 0;

			ob_start();
			?>
            
				<div id="<?php echo "kpb-layout-{$layout_slug}"; ?>" class="kpb-layout kpb-hidden" data-has-preview='<?php echo esc_attr( $has_preview ); ?>' data-has-customize='<?php echo esc_attr( $has_customize ); ?>' data-layout="<?php echo esc_attr( $layout_slug ); ?>">
         	<div class="kpb-row">
						<?php if ( isset( $layout['section'] ) && ! empty( $layout['section'] ) ) :  ?>
            	<div class="kpb-col-left kpb-col-12">

						<?php if ( $sections = isset( $layout['section'] ) && ! empty( $layout['section'] ) ? $layout['section'] : false ) :  ?>

							<?php
							$_is_first_section = true;
							foreach ( $sections as $section_slug => $section ) :
								$_section_classes = ( $_is_first_section) ? 'kpb-section kpb-first' : 'kpb-section';
								?>

									<aside id="<?php echo "kpb-section-{$section_slug}-for-layout{$layout_slug}"; ?>" data-section="<?php echo $section_slug; ?>" class="<?php echo $_section_classes; ?>">
                                    
		                <header class="kpb-section-header kpb-clearfix">
											
											<label class="kpb-pull-left"><?php echo esc_attr( $section['title'] ); ?></label>
									                          
											<?php if ( ! empty( $row_fields ) ) :  ?>
									      <span class="kpb-button-customize kpb-button-customize--row kpb-clearfix">
													<span class="kpb-tooltip" title="<?php esc_attr_e( 'Edit this row', 'kopa-page-builder' ) ?>"	onclick="KPB_Row.open( event, jQuery( this ), '<?php echo $layout_slug;?>', '<?php echo $section_slug; ?>' );"><i class="kpbi-pencil"></i></span>
									      </span>
											<?php endif; ?>

		                </header>

	                  <div class="kpb-section-placeholder">
	                      
	                      <div class="kpb-row">
                                            
												<?php
												if ( $areas = isset( $section['area'] ) && ! empty( $section['area'] ) ? $section['area'] : false ) :
													$_section_grid = $section['grid'];
													?>                                                  

													<?php foreach ( $areas as $area_index => $area ) : ?>
                                                                                                                
															<?php if ( is_array( $area ) ) : ?>
                                                                
																<div class="<?php printf( 'kpb-col-%d', (int) $_section_grid[ $area_index ] ); ?>">

																	<?php
																	$sub_grids = $area['grid'];
																	$sub_areas = $area['area'];

																	foreach ( $sub_areas as $sub_area_index => $sub_area ) {
																		$sub_area_class = (0 == $sub_area_index) ? 'kpb-row-sub-area-first' : '';
																		?>
																		<div class="kpb-row kpb-clearfix kpb-row-sub-area <?php echo $sub_area_class;?>">
																			<?php
																			foreach ( $sub_area as $child_area_area => $child_area ) :
																				$_area_name    = $obj_col->get_name( $child_area );
																				$_area_classes = array( 'kpb-area' );
																				$_col_classes  = sprintf( 'kpb-col-%d', (int) $sub_grids[ $sub_area_index ][ $child_area_area ] );
																				?>
																				<div class="<?php echo esc_attr( $_col_classes ); ?>">
																					<div id="<?php echo "kpb-area-{$child_area}-for-section{$section_slug}"; ?>" data-area="<?php echo $child_area; ?>" class="<?php echo implode( ' ', $_area_classes ); ?>">
                                                                                        
                                                                                        <header class="kpb-area-header kpb-clearfix">
																							<label class="kpb-area-name"><?php echo esc_attr( $_area_name ); ?></label>                                                                                          
                                                                                            <span class="kpb-button-customize kpb-button-customize--col kpb-clearfix">                                                                                              
                                                                                                
																								<?php if ( $col_has_fields ) :  ?>
																									<span onclick="KPB_Widgets.open(event, jQuery(this));" class="kpb-tooltip kpb-button-add-widget kpb_has_separate" title="<?php esc_html_e( 'Add new widget', 'kopa-page-builder' ); ?>"><i class="kpbi-plus"></i></span>
																									<span onclick="KPB_Col.edit( event, jQuery(this) );" class="kpb-tooltip" title="<?php esc_attr_e( 'Edit this column', 'kopa-page-builder' ); ?>"><i class="kpbi-pencil"></i></span>
																								<?php else : ?>
																									<span href="#"	onclick="KPB_Widgets.open(event, jQuery(this));" class="kpb-tooltip kpb-button-add-widget" title="<?php esc_html_e( 'Add new widget', 'kopa-page-builder' ); ?>"><i class="kpbi-plus"></i></span>
																								<?php endif; ?>

                                                                                            </span>                                                                                         
                                                                                        </header>

                                                                                        <div class="kpb-area-placeholder">
																							<?php
																							if ( isset( $layout_saved_data[ $section_slug ][ $child_area ] ) && ! empty( $layout_saved_data[ $section_slug ][ $child_area ] ) ) :

																								$widgets = $layout_saved_data[ $section_slug ][ $child_area ];

																								foreach ( $widgets as $widget_id => $widget ) :
																									$widget_data = get_post_meta( $post_id, $widget_id,true );

																									$widget_title = isset( $widget_data['widget']['title'] ) && ! empty( $widget_data['widget']['title'] ) ? $widget['name'] . ' : ' . $widget_data['widget']['title'] : $widget['name'];

																									?>
																									<aside id="<?php echo esc_attr( $widget_id ); ?>" class="kpb-widget" data-class="<?php echo esc_attr( $widget['class_name'] ); ?>" data-name="<?php echo esc_attr( $widget['name'] ); ?>">
                                                                                            <div class="kpb-widget-inner kpb-clearfix">                                         
																		                		<label class=""><?php echo esc_attr( $widget_title ); ?></label> 
                                                                                                <br/>   
                                                                                                <div class="kpb-widget-action kpb-clearfix">                                        
																		                			<a href="#" onclick="KPB_Widget.edit(event, jQuery(this), '<?php echo esc_attr( $widget_id ); ?>' );" class="kpb-button-edit kpb-pull-left"><?php esc_html_e( 'Edit', 'kopa-page-builder' ); ?></a>                                                                                  
																			                		<a href="#" onclick="KPB_Widget.delete(event, jQuery(this), '<?php echo esc_attr( $widget_id ); ?>' );" class="kpb-button-delete kpb-pull-left"><?php esc_html_e( 'Delete', 'kopa-page-builder' ); ?></a>                                                                                    
                                                                                                </div>
																										</div>                    
                                                                                                </aside>
																                		<?php
															                		endforeach;
																                endif;
																                ?>
                                                                                        </div>

                                                                                    </div>
                                                                                </div>
																				<?php
																			endforeach;
																			?>
                                                                        </div>                                                                                                                          
																		<?php
																	}
																	?>

                                                                </div>

															<?php else : ?>

																<?php
																$_area_name    = $obj_col->get_name( $area );
																$_area_classes = array( 'kpb-area' );
																$_col_classes = sprintf( 'kpb-col-%d', (int) $_section_grid[ $area_index ] );
																?>

																<div class="<?php echo esc_attr( $_col_classes ); ?>">
																	<div id="<?php echo "kpb-area-{$area}-for-section{$section_slug}"; ?>" data-area="<?php echo $area; ?>" class="<?php echo implode( ' ', $_area_classes ); ?>">
                                                                        
                                    <header class="kpb-area-header kpb-clearfix">
																			<label class="kpb-area-name"><?php echo esc_attr( $_area_name ); ?></label>                                                                          
                                      
                                      <span class="kpb-button-customize kpb-button-customize--col kpb-clearfix">                                                                                               
																				<?php if ( $col_has_fields ) :  ?>
																					<span	onclick="KPB_Widgets.open(event, jQuery(this));" class="kpb-tooltip kpb-button-add-widget kpb_has_separate" title="<?php esc_html_e( 'Add new widget', 'kopa-page-builder' ); ?>"><i class="kpbi-plus"></i></span>
																					<span	onclick="KPB_Col.edit( event, jQuery(this) );" class="kpb-tooltip" title="<?php esc_attr_e( 'Edit this column', 'kopa-page-builder' ); ?>"><i class="kpbi-pencil"></i></span>
																				<?php else : ?>
																					<span	onclick="KPB_Widgets.open(event, jQuery(this));" class="kpb-tooltip kpb-button-add-widget" title="<?php esc_html_e( 'Add new widget', 'kopa-page-builder' ); ?>"><i class="kpbi-plus"></i></span>
																				<?php endif; ?>
                                      </span>

                                    </header>

                                    <div class="kpb-area-placeholder">
																			<?php
																			if ( isset( $layout_saved_data[ $section_slug ][ $area ] ) && ! empty( $layout_saved_data[ $section_slug ][ $area ] ) ) :

																				$widgets = $layout_saved_data[ $section_slug ][ $area ];

																				foreach ( $widgets as $widget_id => $widget ) :
																					$widget_data = get_post_meta( $post_id, $widget_id,true );
																					$widget_title = isset( $widget_data['widget']['title'] ) && ! empty( $widget_data['widget']['title'] ) ? $widget['name'] . ' : ' . $widget_data['widget']['title'] : $widget['name'];
																					?>
																					<aside id="<?php echo esc_attr( $widget_id ); ?>" class="kpb-widget" data-class="<?php echo esc_attr( $widget['class_name'] ); ?>" data-name="<?php echo esc_attr( $widget['name'] ); ?>">
                                      			<div class="kpb-widget-inner kpb-clearfix">                                         
														                		<label class=""><?php echo esc_attr( $widget_title ); ?></label> 
                                            <br/>   
                                            <div class="kpb-widget-action kpb-clearfix">                                        
												                			<a href="#" onclick="KPB_Widget.edit(event, jQuery(this), '<?php echo esc_attr( $widget_id ); ?>' );" class="kpb-button-edit kpb-pull-left"><?php esc_html_e( 'Edit', 'kopa-page-builder' ); ?></a>                                                                                  
													                		<a href="#" onclick="KPB_Widget.delete(event, jQuery(this), '<?php echo esc_attr( $widget_id ); ?>' );" class="kpb-button-delete kpb-pull-left"><?php esc_html_e( 'Delete', 'kopa-page-builder' ); ?></a>                                                                                    
                                            </div>
																					</div>                    
                                    	</aside>
								                		<?php
							                		endforeach;
								                endif;
								                ?>
                              </div>
                            </div>
                          </div>

												<?php endif; ?>

											<?php endforeach; ?>                                        

										<?php endif;?>

                    </div>

                  </div>

	              </aside>

								<?php
								$_is_first_section = false;
							endforeach;
							?>

						<?php endif; ?>

         		</div>
					<?php endif;?>

					<?php if ( isset( $layout['preview'] ) && ! empty( $layout['preview'] ) ) :  ?>
            <div class="kpb-col-right kpb-col-4" style="display: none;">
							<span class="kpb-preview-images"><img src="<?php echo $layout['preview']; ?>"></span>                   
            </div>
					<?php endif; ?>
          </div>
      </div>

			<?php
			$html          = ob_get_clean();
			$html_minifier = KPB_Minifier_HTML::get_instance();

			echo $html_minifier->minify( $html );

			exit();
		}

		function save_layout() {

			check_ajax_referer( 'kpb_save_layout', 'security' );

			$post_id     = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : false;
			$data        = isset( $_POST['data'] ) ? $_POST['data'] : false;
			$layout_slug = esc_html( $data['layout_slug'] );

			$layout_data = array();

			if ( $data ) {

				$rows = isset( $data['rows'] ) && ! empty( $data['rows'] ) ? $data['rows'] : array();

				if ( $rows ) {

					foreach ( $rows as $row ) {

						$cols = isset( $row['cols'] ) && ! empty( $row['cols'] ) ? $row['cols'] : array();

						if ( $cols ) {

							$cols_data = array();

							foreach ( $cols as $col ) {

								$widgets = isset( $col['widgets'] ) && ! empty( $col['widgets'] ) ? $col['widgets'] : array();

								if ( $widgets ) {

									$_widgets = array();

									foreach ( $widgets as $widget_index => $widget ) {
										$_widgets[ $widget['id'] ] = array( 'name' => $widget['name'], 'class_name' => $widget['class_name'] );
									}

									$cols_data[ $col['name'] ] = $_widgets;

								}
							}

							$layout_data[ $row['name'] ] = $cols_data;
						}
					}
				}

				if ( $layout_data ) {
					update_post_meta( $post_id, $this->get_meta_key_grid( $layout_slug ), $layout_data );
				} else {
					delete_post_meta( $post_id, $this->get_meta_key_grid( $layout_slug ) );
					KPB_Data::clean_layout_data( $post_id, $layout_slug );
				}
				update_post_meta( $post_id, $this->get_key_current_layout(), $layout_slug );

			}

			do_action( 'kopa_page_builder_after_save_layout', $post_id, $layout_slug, $layout_data );
			do_action( 'kopa_page_builder_after_save_grid', $post_id, $layout_slug, $layout_data );

			exit();
		}

		function load_layout_customize() {

			check_ajax_referer( 'kpb_load_layout_customize', 'security' );

			$layout_slug = isset( $_POST['layout_slug'] ) ? esc_html( $_POST['layout_slug'] ) : false;
			$post_id     = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : false;

			$layout = $this->get_layout( $layout_slug );

			if ( isset( $layout['customize'] ) && ! empty( $layout['customize'] ) ) :

				ob_start();

				$data = Kopa_Page_Builder::get_layout_customize_data( $post_id, $layout_slug );

				?>

				<div id="<?php echo "kpb-layout-customize-lightbox-{$layout_slug}"; ?>" class="kpb-customize-lightbox" style="display: none;">
            
            <section class="kpb-customize">
            
        		<form name="<?php echo "kpb-form-customize-layout-{$layout_slug}" ?>"  method="POST" autocomplete="off" onsubmit="KPB_Layout_Customize.save( event, jQuery( this ), <?php echo (int) $post_id;?> );" action="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-ajax.php' ), 'kpb_save_layout_customize', 'security' ) ); ?>">
            
        			<input type="hidden" name="layout_slug" value="<?php echo $layout_slug; ?>" autocomplete="off">
                    <input type="hidden" name="action" value="kpb_save_layout_customize" autocomplete="off">
                
                    <header class="kpb-customize-header kpb-clearfix">
								<label class="kpb-section-title kpb-pull-left"><?php echo $layout['title']; ?></label>
								<a href="#" onclick="KPB_Layout_Customize.close(event);" class="button button-link button-delete kpb-pull-right"><?php esc_html_e( 'Close', 'kopa-page-builder' ); ?></a>
								<button type="submit" class="button button-primary kpb-pull-right"><?php esc_html_e( 'Save', 'kopa-page-builder' ); ?></button>                                                             
                            </header>

                            <div class="kpb-form-inner kpb-clearfix">
                                <div class="kpb-wrapper-configuration">                                 
                                    <div class="kpb-wrapper-configuration-toggle kpb-tabs">
                                        
                                        <nav>
                                            <ul class="kpb-clearfix">
												<?php
												$_is_first_tab = true;
												foreach ( $layout['customize']  as $tab_slug => $tab ) :
													$classes = $_is_first_tab ? 'kpb-tab-title kpb-tab-title-first kpb-tab-title-active' : 'kpb-tab-title';
													$tab_id = 'kpb-layout-customize-' . $layout_slug . '-tab-' . $tab_slug;
													?>
													<li class="<?php echo $classes;?>">
														<a href="<?php echo "#{$tab_id}"; ?>"><?php echo esc_attr( $tab['title'] ); ?></a>
                                                    </li>
													<?php
													$_is_first_tab = false;
												endforeach;
												?>
                                            </ul>
                                        </nav>

										<?php
										$_is_first_tab = true;
										foreach ( $layout['customize']  as $tab_slug => $tab ) :
											$display = $_is_first_tab ? 'block' : 'none';
											$tab_id = 'kpb-layout-customize-' . $layout_slug . '-tab-' . $tab_slug;
											?>
											<div id="<?php echo $tab_id; ?>" class="kpb-tab-content" style="display:<?php echo $display;?>;">
												<?php
												foreach ( $tab['params'] as $param_key => $param_args ) :

													$param_args['name']  = sprintf( '%s[%s][%s]', $this->get_meta_key_customize( $layout_slug ), $tab_slug, $param_key );
													$param_args['value'] = isset( $data[ $tab_slug ][ $param_key ] ) ? $data[ $tab_slug ][ $param_key ] : (isset( $param_args['default'] ) ? $param_args['default'] : null);

													KPB_UI::get_control( $param_args );

												endforeach;
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

			endif;

			exit();
		}

		function save_layout_customize() {
			check_ajax_referer( 'kpb_save_layout_customize', 'security' );

			$post_id     = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : false;
			$layout_slug = isset( $_POST['layout_slug'] ) ? esc_attr( $_POST['layout_slug'] ) : false;
			$meta_key    = $this->get_meta_key_customize( $layout_slug );
			$meta_value  = array();
			$data        = isset( $_POST[ $meta_key ] ) ? $_POST[ $meta_key ] : array();
			$layout      = $this->get_layout( $layout_slug );

			foreach ( $layout['customize'] as $tab_slug => $tab ) {

				foreach ( $tab['params'] as $param_key => $param_args ) {

					$value = isset( $data[ $tab_slug ][ $param_key ] ) ? $data[ $tab_slug ][ $param_key ] : ( isset( $param_args['default'] ) ? $param_args['default'] : null );
					$meta_value[ $tab_slug ][ $param_key ] = KPB_Data::santinizing( $param_args, $value );

				}
			}

			do_action( 'kopa_page_builder_before_save_layout_customize', $post_id, $layout_slug, $meta_value );

			update_post_meta( $post_id, $meta_key, $meta_value );

			do_action( 'kopa_page_builder_after_save_layout_customize', $post_id, $layout_slug, $meta_value );

			exit();
		}

		function get_data_customize( $post_id, $layout_slug ) {
			$meta_key = $this->get_meta_key_customize( $layout_slug );
			return get_post_meta( $post_id, $meta_key, true );
		}

		function get_current_layout( $post_id ) {
			$meta_key = $this->get_key_current_layout();
			return get_post_meta( $post_id, $meta_key, true );
		}

		function get_data( $post_id, $layout_slug = null ) {
			$layout_slug = $layout_slug ? $layout_slug : $this->get_current_layout( $post_id );
			$meta_key = $this->get_meta_key_grid( $layout_slug );
			return get_post_meta( $post_id, $meta_key, true );
		}
	}

}
