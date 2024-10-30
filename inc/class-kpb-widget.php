<?php

if ( ! class_exists( 'KPB_Widget' ) ) {


	class KPB_Widget {

		protected static $instance = null;

		private $fields, $key_customize;

		static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}		

		function __construct() {
			$this->key_customize = apply_filters( 'kopa_page_builder_get_meta_key_widget_customize','kopa_page_builder_widget_customize' );
			$this->fields        = apply_filters( 'kopa_page_builder_get_customize_fields', array() );
			$this->fields        = apply_filters( 'kopa_page_builder_get_widget_fields', $this->fields );

			add_action( 'wp_ajax_kpb_load_widgets', array( $this, 'load_widgets' ) );
			add_action( 'wp_ajax_kpb_load_widget_placeholder', array( $this, 'load_widget_placeholder' ) );
			add_action( 'wp_ajax_kpb_load_widget_form', array( $this, 'load_widget_form' ) );
			add_action( 'wp_ajax_kpb_delete_widget', array( $this, 'delete_widget' ) );
			add_action( 'wp_ajax_kpb_save_widget', array( $this, 'save_widget' ) );
		}		

		function get_fields() {
			return $this->fields;
		}
	
		function is_exist( $post_id = 0, $layout_slug = '', $widget_class_name = '' ) {
			$key_exist = $this->get_key_exist_total( $layout_slug, $widget_class_name );
			return metadata_exists( 'post', $post_id, $key_exist );
		}

		function get_key_customize() {
			return $this->key_customize;
		}

		function get_key_exist_total( $layout_slug = '', $widget_class_name = '' ) {						
			return sprintf( 'kopa_page_builder_widget_exists__%s__%s', $layout_slug, KPB_Utility::str_uglify( $widget_class_name ) );
		}

		function get_key_exist_single( $layout_slug = '', $widget_class_name = '', $widget_id = '' ) {
			return sprintf( '%s__%s', $this->get_key_exist_total( $layout_slug, $widget_class_name ), $widget_id );
		}

		function load_widgets() {

			check_ajax_referer( 'kpb_load_widgets', 'security' );
			ob_start();
			?>

        <div id="kpb-widgets-lightbox" style="display: none;">

					<?php
					$type   = apply_filters( 'kopa_page_builder_get_layout_list_of_widgets', 'default' );
					$blocks = $this->get_blocks();

					switch ( $type ) {

						case 'default':
							$this->get_list_of_widgets_default( $blocks );
							break;

						case 'side':
							$this->get_list_of_widgets_side( $blocks );
							break;

						case 'searchable':
							$this->get_list_of_widgets_searchable( $blocks );
							break;

						default:
							do_action( 'kopa_page_builder_get_layout_list_of_widgets_' . $type, $blocks );
							break;

					}

					?>

        </div>

			<?php
			$html          = ob_get_clean();
			$html_minifier = KPB_Minifier_HTML::get_instance();

			echo $html_minifier->minify( $html );

			exit();
		}

		function get_list_of_widgets_default( $blocks = array() ) {
			?>

        <section id="kpb-widgets" class="kpb-widgets--default"> 
            
            <header id="kpb-widgets-header" class="kpb-clearfix">
							<label class="kpb-pull-left"><?php esc_html_e( 'Avaiable Widgets', 'kopa-page-builder' ); ?></label>
							<a href="#" onclick="KPB_Widgets.close(event);" class="button button-link button-delete kpb-pull-right"><?php esc_html_e( 'Close', 'kopa-page-builder' ); ?></a>
            </header>

            <div class="kpb-widgets-inner">
            <div class="kpb-wrapper-configuration">
                <div class="kpb-wrapper-configuration-toggle  kpb-tabs">
                    
                    <nav id="kpb-nav-list-blocks" class="kpb-nav-list-blocks--default">
                      <ul class="kpb-clearfix">
												<?php
												ob_start();

												$_is_first_tab = true;

												foreach ( $blocks as $block_slug => $block_info ) :
													$classes = $_is_first_tab ? 'kpb-tab-title kpb-tab-title-first kpb-tab-title-active' : 'kpb-tab-title';
													$tab_id = KPB_Utility::str_uglify( "kpb-list-{$block_slug}-blocks" );
													?>
		                                        
													<li class="<?php echo $classes;?>">
														<a href="<?php echo "#{$tab_id}"; ?>"><?php echo esc_attr( $block_info['title'] ); ?></a>
		                     	</li>
		                                        
													<?php
													$_is_first_tab = false;
												endforeach;
												$nav = ob_get_clean();

												echo wp_kses_post( $nav );
												?>
                      </ul>
                  </nav>

	            	<?php
		            $index_block = 0;
		            $_is_first_tab = true;

		            foreach ( $blocks as $block_slug => $block_info ) :
									$blocks_classes = (0 == $index_block) ? 'kpb-tab-content kpb-list-blocks kpb-list-blocks-first kpb-clearfix' : 'kpb-tab-content kpb-list-blocks kpb-clearfix';
									$display        = $_is_first_tab ? 'block' : 'none';
									$tab_id         = KPB_Utility::str_uglify( "kpb-list-{$block_slug}-blocks" );
		            	?>

		            	<div id="<?php echo $tab_id; ?>" class="<?php echo $blocks_classes; ?>" style="display: <?php echo $display;?>">                                        
			            	<?php
		            		$index_global = 1;
	            			$index_single = 1;

			            	$widgets = $block_info['items'];
			            	unset($widgets['WP_Widget_Media_Video']);
			            	unset($widgets['WP_Widget_Media_Image']);
			            	unset($widgets['WP_Widget_Media_Audio']);
			            	ksort( $widgets );

				            foreach ( $widgets as $class_name => $widget_info ) :

				                if ( 1 == $index_single || ( $index_single % 5 == 0) ) {
				                	if ( 1 == $index_global ) {
				                		echo '<div class="kpb-row kpb-first">';
				                	} else {
				                		echo '<div class="kpb-row">';
				                	}
				                }

				                ?>

                                <aside class="kpb-widget kpb-col-3">
                                    <div class="kpb-widget-inner">
                                        <header class="kpb-clearfix">
					                		<label class="kpb-pull-left"><?php echo $widget_info->name; ?></label>
					                		<a href="#" onclick="KPB_Widget.add(event, jQuery(this), '<?php echo $class_name; ?>', '<?php echo $widget_info->name; ?>' );" class="kpb-button-use kpb-pull-right"><?php esc_html_e( 'Add', 'kopa-page-builder' ); ?></a>
                                        </header>   

                                                        <div class="kpb-widget-description">                                                     
									          	<span><?php echo $widget_info->widget_options['description']; ?></span>
                                                        </div>
                                                    </div>                    
                                </aside>

				                <?php

				                if ( ( $index_single % 4 == 0) || ( $index_global == count( $widgets )) ) {
				                    echo '</div>';
				                    $index_single = 1;
				                } else {
				                    $index_single++;
				                }

				                $index_global++;
				            endforeach;
				            ?>

                            </div>

		            	<?php
		            	$_is_first_tab = false;
		            	$index_block++;
		            endforeach;
		            ?>

                    </div>
                    </div>

              </div>

                </section>

			<?php
		}

		function get_list_of_widgets_side( $blocks = array() ) {

			?>
      <section id="kpb-widgets" class="kpb-widgets--side">    
        
        <header id="kpb-widgets-header" class="kpb-clearfix">
					<label class="kpb-pull-left"><?php esc_html_e( 'Avaiable Widgets', 'kopa-page-builder' ); ?></label>
					<a href="#" onclick="KPB_Widgets.close(event);" class="button button-link button-delete kpb-pull-right"><?php esc_html_e( 'Close', 'kopa-page-builder' ); ?></a>
        </header>

        <div class="kpb-widgets-inner">
            
          <div class="kpb-tabs">

            <div class="row kpb-clearfix">

                <div class="kpb-blocks--side kpb-col-8">

			          	<?php
			            $index_block = 0;
			            $_is_first_tab = true;

			            foreach ( $blocks as $block_slug => $block_info ) :
										$blocks_classes = (0 == $index_block) ? 'kpb-tab-content kpb-list-blocks kpb-list-blocks-first kpb-clearfix' : 'kpb-tab-content kpb-list-blocks kpb-clearfix';
										$display        = $_is_first_tab ? 'block' : 'none';
										$tab_id         = sprintf( 'kpb-list-%s-blocks', KPB_Utility::str_uglify( $block_slug ) );
			            	?>

			            	<div id="<?php echo $tab_id; ?>" class="<?php echo $blocks_classes; ?>" style="display: <?php echo $display;?>">

			            		<h2 class="kpb-block-title--side"><?php echo esc_html( $block_info['title'] ); ?></h2>

				            	<?php
				            	$widgets = $block_info['items'];
				            	unset($widgets['WP_Widget_Media_Video']);
			            		unset($widgets['WP_Widget_Media_Image']);
			            		unset($widgets['WP_Widget_Media_Audio']);
				            	ksort( $widgets );
					            foreach ( $widgets as $class_name => $widget_info ) :
					            ?>

	                      <aside class="kpb-widget kpb-widget--side">
	                					<a href="#" onclick="KPB_Widget.add(event, jQuery(this), '<?php echo $class_name; ?>', '<?php echo $widget_info->name; ?>' );" class="kpb-button-use kpb-button-use--side"><?php esc_html_e( 'Add', 'kopa-page-builder' ); ?></a>
	                          <div class="kpb-widget-inner">

	                              <header class="kpb-clearfix">
		                							<label class="kpb-pull-left"><?php echo $widget_info->name; ?></label>                                      
	                              </header>   

                                <div class="kpb-widget-description kpb-widget-description--side">                                                    
      														<?php echo $widget_info->widget_options['description']; ?>
                                </div>

                            </div>                    
	                      </aside>

					            <?php endforeach; ?>

	                	</div>

			            	<?php
			            	$_is_first_tab = false;
			            	$index_block++;
			            endforeach;
			            ?>
              
              </div>

              <div class="kpb-col-4">

          			<h2 class="kpb-block-title--side"><?php esc_html_e( 'Type of Widget', 'kopa-page-builder' ); ?></h2>

                <nav id="kpb-nav-list-blocks" class="kpb-nav-list-blocks--side">
                  <ul class="kpb-clearfix">
										<?php
										ob_start();

										$_is_first_tab = true;

										foreach ( $blocks as $block_slug => $block_info ) :
											$classes = $_is_first_tab ? 'kpb-tab-title kpb-tab-title-first kpb-tab-title-active' : 'kpb-tab-title';
											$tab_id  = sprintf( 'kpb-list-%s-blocks', KPB_Utility::str_uglify( $block_slug ) );
											?>
		                                    
											<li class="<?php echo $classes;?>">
												<a href="<?php echo "#{$tab_id}"; ?>"><?php echo esc_attr( $block_info['title'] ); ?></a>
		                  </li>
		                                    
											<?php
											$_is_first_tab = false;
										endforeach;
										$nav = ob_get_clean();
										echo wp_kses_post( $nav );
										?>
                  </ul>
                </nav>                          

            	</div>

                </div>

            </div>
                    
          </div>

      </section>

			<?php
		}

		function get_list_of_widgets_searchable( $blocks = array() ) {
		}

		function get_blocks() {
			global $wp_widget_factory;
			$widgets           = $wp_widget_factory->widgets;
			$widgets           = apply_filters( 'kpb_get_widgets_list', $widgets );
			$widgets_inprocess = array();
			$blocks            = array();

			foreach ( $widgets as $class_name => $widget_info ) {

				if ( isset( $widget_info->kpb_group ) && ! empty( $widget_info->kpb_group ) ) {
					$group_slug = $widget_info->kpb_group;
				} else {
					if ( strpos( strtolower( $widget_info->name ), 'bbpress' ) ) {
						$group_slug = 'bbpress';
					} else if ( strpos( strtolower( $widget_info->name ), 'commerce' ) ) {
						$group_slug = 'product';
					} else {
						$group_slug = 'widgets';
					}
				}

				if ( ! isset( $blocks[ $group_slug ] ) ) {
					$blocks[ $group_slug ]['title'] = KPB_Utility::str_beautify( $group_slug );
				}

				$blocks[ $group_slug ]['items'][ $class_name ] = $widget_info;
			}

			ksort( $blocks );

			return $blocks;
		}

		function load_widget_placeholder() {
			check_ajax_referer( 'kpb_load_widget_placeholder', 'security' );

			$post_id = ( isset( $_POST['post_id'] )  && ! empty( $_POST['post_id'] ) ) ? absint( $_POST['post_id'] ) : false;

			if ( $post_id ) {
				ob_start();
				?>
	        <div id="kpb-widget-lightbox" style="display: none;">
	                  
	          <section id="kpb-widget">             
	                
		          <form id="kpb-form-widget" name="kpb-form-widget"  method="POST" autocomplete="off" onsubmit="KPB_Widget.save(event, jQuery(this));" action="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-ajax.php?action=kpb_save_widget' ), 'kpb_save_widget', 'security' ) ); ?>">
		                    
		            <header id="kpb-widget-header" class="kpb-clearfix">
									<label id="kpb-widget-title" class="kpb-pull-left"><?php esc_html_e( 'Widget Name', 'kopa-page-builder' ); ?></label>    
									<a href="#" onclick="KPB_Widget.close(event);" class="button button-link button-delete kpb-pull-right"><?php esc_html_e( 'Close', 'kopa-page-builder' ); ?></a>										
		            </header>

		            <div class="kpb-form-inner">
		                <center class="kpb-loading">
		       						<?php esc_html_e( 'Loading...', 'kopa-page-builder' ); ?>                               
		                </center>
		            </div>                  

		            <input type="hidden" name="kpb-widget-class-name" value="" autocomplete=off>
		            <input type="hidden" name="kpb-widget-name" value="" autocomplete=off>                    
		            <input type="hidden" name="kpb-widget-id" value="" autocomplete=off>             
		            <input type="hidden" name="kpb-widget-action" value="add" autocomplete=off>                                         
		          	<input type="hidden" name="kpb-post-id" value="<?php echo (int) $post_id; ?>" autocomplete=off>                               

		          	<footer id="kpb-widget-footer">
		          		<div class="kpb-clearfix">
		          			<button type="submit" class="button button-primary kpb-pull-right"><?php esc_html_e( 'Save', 'kopa-page-builder' ); ?></button>
		          			</div>
		          	</footer>

		          </form>

	        	</section>

	      </div>

				<?php
				$html          = ob_get_clean();
				$html_minifier = KPB_Minifier_HTML::get_instance();

				echo $html_minifier->minify( $html );

			}

			exit();
		}

		function load_widget_form() {

			check_ajax_referer( 'kpb_load_widget_form', 'security' );

			$post_id    = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
			$class_name = isset( $_POST['class_name'] ) && ! empty( $_POST['class_name'] ) ? esc_html( $_POST['class_name'] ) : false;
			$widget_id  = isset( $_POST['widget_id'] ) && ! empty( $_POST['widget_id'] ) ?  esc_html( $_POST['widget_id'] ) : false;

			if ( $post_id && $class_name && class_exists( $class_name ) ) {

				$instance       = array();
				$customize_data = array();

				if ( $widget_id ) {
					$data           = get_post_meta( $post_id, $widget_id, true );
					if ( $data ) {
						$instance       = $data['widget'];
						$customize_data = isset( $data['customize'] ) ? $data['customize'] : array();
					}
				}

				$widget          = new $class_name;
				$widget->id_base = 'kpb';
				$widget->number  = 0;

				if ( isset( $widget->kpb_is_private ) ) {
					$widget->kpb_is_private = false;
				}

				$customize_key    = $this->get_key_customize();
				$customize_fields = $this->get_fields();

				ob_start();

				if ( ! empty( $customize_fields ) ) {
					?>
					<section class="kpb-widget-customize kpb-wrapper-configuration">
					  <div class="kpb-wrapper-configuration-toggle kpb-tabs">
              
              <nav>
                <ul class="kpb-clearfix">
                  <li class="kpb-tab-title kpb-tab-title-first kpb-tab-title-active">
											<a href="<?php echo "#kpb-tab-widget-{$widget->id_base}"; ?>"><?php esc_html_e( 'Widget', 'kopa-page-builder' ); ?></a>
                  </li>

									<?php foreach ( $customize_fields  as $tab_slug => $tab ) : $tab_id = $tab_slug . '-tab-' . $widget->id_base; ?>
	                  <li class="kpb-tab-title">
											<a href="<?php echo "#{$tab_id}"; ?>"><?php echo esc_attr( $tab['title'] ); ?></a>
	                  </li>
									<?php endforeach; ?>

							  </ul>
							</nav>
            
							<div id="<?php echo "kpb-tab-widget-{$widget->id_base}"; ?>" class="kpb-tab-content">
								<?php 
									if ( $class_name == 'WP_Widget_Text' ) { 
										$this->form_wg_text( $instance );
									} else {
										$widget->form( $instance );
									}
								?>
							</div>

							<?php
							foreach ( $customize_fields  as $tab_slug => $tab ) :
								$tab_id = $tab_slug . '-tab-' . $widget->id_base;
								?>
								<div id="<?php echo $tab_id; ?>" class="kpb-tab-content" style="display:none;">
								
									<?php
									foreach ( $tab['params'] as $param_key => $param_args ) {
										$param_args['name'] = sprintf( '%s[%s][%s]', $customize_key, $tab_slug, $param_key );
										$param_args['value'] = isset( $customize_data[ $tab_slug ][ $param_key ] ) ? $customize_data[ $tab_slug ][ $param_key ] : (isset( $param_args['default'] ) ? $param_args['default'] : null);
										KPB_UI::get_control( $param_args );
									} 
									?>									
								</div>
							<?php endforeach; ?>
							
						</div>
					</section>            
					<?php
				} else {
					$widget->form( $instance );
				}

				$html          = ob_get_clean();
				$html_minifier = KPB_Minifier_HTML::get_instance();

				echo $html_minifier->minify( $html );

			}

			exit();
		}

		function delete_widget() {

			check_ajax_referer( 'kpb_delete_widget', 'security' );
			
			$data    = $_POST;
			$post_id = isset( $data['post_id'] ) ? absint( $data['post_id'] ) : 0;

			if ( $post_id ) {
								
				$layout_slug      = isset( $data['layout_slug'] ) ? esc_attr( $data['layout_slug'] ) : '';
				$widget_id        = isset( $data['widget_id'] ) ? esc_attr( $data['widget_id'] ) : '';
				$class_name       = isset( $data['class_name'] ) ? esc_attr( $data['class_name'] ) : '';				
				
				$key_exist_total  = $this->get_key_exist_total( $layout_slug, $class_name );
				$key_exist_single = $this->get_key_exist_single( $layout_slug, $class_name, $widget_id );
				
				$total            = (int)get_post_meta( $post_id, $key_exist_total, true );
				$total            = $total - 1;

				if( $total ) {
					update_post_meta( $post_id, $key_exist_total, $total );					
				}else{
					delete_post_meta( $post_id, $key_exist_total );
				}

				delete_post_meta( $post_id, $widget_id );
				delete_post_meta( $post_id, $key_exist_single );

				do_action( 'kopa_page_builder_after_delete_widget', $post_id, $widget_id );

			}

			exit();
		}

		function save_widget() {

			check_ajax_referer( 'kpb_save_widget', 'security' );

			// Create instance of HTML-Minifier.
			$html_minifier = KPB_Minifier_HTML::get_instance();

			// Grab data from client.
			$customize_key        = $this->get_key_customize();
			$data                 = $_POST;
			$action               = isset( $data['kpb-widget-action'] ) ? esc_html( $data['kpb-widget-action'] ) : 'add';
			$post_id              = isset( $data['kpb-post-id'] ) ? absint( $data['kpb-post-id'] ) : 0;
			$widget_id            = isset( $data['kpb-widget-id'] ) ? esc_html( $data['kpb-widget-id'] ) : '';
			$widget['widget']     = isset( $data['widget-kpb'][0] ) ? $data['widget-kpb'][0] : array();
			$widget['name']       = isset( $data['kpb-widget-name'] ) ? esc_html( $data['kpb-widget-name'] ) : '';
			$widget['class_name'] = isset( $data['kpb-widget-class-name'] ) ? esc_html( $data['kpb-widget-class-name'] ) : '';
			$widget['customize']  = isset( $data[ $customize_key ] ) ? $data[ $customize_key ] : array();
			$widget_title         = $widget['name'];

			$layout_slug = Kopa_Page_Builder::get_current_layout( $post_id );
			
			// Create instance of current widget.
			$obj              = new $widget['class_name'];

			if ( $widget['class_name'] == 'WP_Widget_Text' ) {
				$this->update_wg_text( $widget['widget'], array() );
			} else {
				$widget['widget'] = $obj->update( $widget['widget'], array() );
			}
			
			// Create title for visual block.
			if (  isset( $widget['widget']['title'] ) && ! empty( $widget['widget']['title'] ) ) {
				$widget_title .= ' : ' . $widget['widget']['title'];
			}

			// Save data.
			update_post_meta( $post_id, $widget_id, $widget );

			// Update exist count.
			$key_exist_total  = $this->get_key_exist_total( $layout_slug, $widget['class_name'] );
			$key_exist_single = $this->get_key_exist_single( $layout_slug, $widget['class_name'], $widget_id );

			$total = (int)get_post_meta( $post_id, $key_exist_total, true );

			if( !metadata_exists( 'post', $post_id, $key_exist_single ) ) {
				update_post_meta( $post_id, $key_exist_total, $total + 1 );
				update_post_meta( $post_id, $key_exist_single, true );
			}

			// Create response to client.
			$response = array(
				'id'     => $widget_id,
				'action' => $action,
				'visual' => '',
				'label'  => '',
				'form'   => '',
			);

			if ( 'add' === $action ) :
				ob_start();
				?>
			  <aside id="<?php echo esc_attr( $widget_id ); ?>" class="kpb-widget" data-class="<?php echo esc_attr( $widget['class_name'] ); ?>" data-name="<?php echo esc_attr( $widget['name'] ); ?>">
            <div class="kpb-widget-inner kpb-clearfix">
        		<label><?php echo esc_html( $widget_title ); ?></label>
                <div class="kpb-widget-action kpb-clearfix">
		          		<a href="#" onclick="KPB_Widget.edit(event, jQuery(this), '<?php echo esc_attr( $widget_id ); ?>' );" class="kpb-button-edit kpb-pull-left"><?php esc_html_e( 'Edit', 'kopa-page-builder' ); ?></a>
		          		<a href="#" onclick="KPB_Widget.delete(event, jQuery(this), '<?php echo esc_attr( $widget_id ); ?>' );" class="kpb-button-delete kpb-pull-left"><?php esc_html_e( 'Delete', 'kopa-page-builder' ); ?></a>
                </div>
            </div>
        </aside>
        <?php

				$response['visual'] = $html_minifier->minify( stripslashes( ob_get_clean() ) );

		  else :
				$response['label']  = wp_kses_post( stripslashes( $widget_title ) );
		  endif;

			// Load new form.
			$obj->id_base = 'kpb';
			$obj->number  = 0;

			ob_start();
			if ( $widget['class_name'] == 'WP_Widget_Text' ) {
				$this->form_wg_text( $widget['widget'] );
			} else {
				$obj->form( $widget['widget'] );
			}


			$response['form'] = $html_minifier->minify( stripslashes( ob_get_clean() ) );

			do_action( 'kopa_page_builder_after_save_widget', $post_id, $layout_slug, $widget_id, $widget );

			echo json_encode( $response );

			exit();
		}

		public function form_wg_text( $instance ) {
			$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
			$filter = isset( $instance['filter'] ) ? $instance['filter'] : 0;
			$title = sanitize_text_field( $instance['title'] );
			?>
			<p><label for="widget-kpb-0-title"><?php _e('Title:'); ?></label>
			<input class="widefat" id="widget-kpb-0-title" name="widget-kpb[0][title]" type="text" value="<?php echo esc_attr($title); ?>" /></p>

			<p><label for="widget-kpb-0-text"><?php _e( 'Content:' ); ?></label>
			<textarea class="widefat" rows="16" cols="20" id="widget-kpb-0-text" name="widget-kpb[0][text]"><?php echo esc_textarea( $instance['text'] ); ?></textarea></p>

			<p><input id="widget-kpb-0-filter" name="widget-kpb[0][filter]" type="checkbox"<?php if ( 'on' === $filter ) echo 'checked="checked"'; ?> />&nbsp;<label for="widget-kpb-0-filter"><?php _e('Automatically add paragraphs'); ?></label></p>
			<?php
		}

		public function update_wg_text( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['title'] = sanitize_text_field( $new_instance['title'] );
			if ( current_user_can( 'unfiltered_html' ) ) {
				$instance['text'] = $new_instance['text'];
			} else {
				$instance['text'] = wp_kses_post( $new_instance['text'] );
			}
			$instance['filter'] = ! empty( $new_instance['filter'] );
			return $instance;
		}
	}


}
