<?php
/**
 * Kopa Page Builder plugin helps you create static pages by manually adding, editing or moving the widgets to the expected sidebars. Unlike the other Page Builder plugins which available on WordPress.org now, this plugin requires a deep understanding of technical knowledge and WordPress to use for your website.
 *
 * @package Kopa_Page_Builder
 * @author kopatheme
 *
 * Plugin Name: Kopa Page Builder
 * Description: Kopa Page Builder plugin helps you create static pages by manually adding, editing or moving the widgets to the expected sidebars. Unlike the other Page Builder plugins which available on WordPress.org now, this plugin requires a deep understanding of technical knowledge and WordPress to use for your website.
 * Version: 2.0.8
 * Author: Kopa Theme
 * Author URI: http://kopatheme.com/
 * License: GNU General Public License v3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Kopa Page Builder plugin, Copyright 2016 Kopatheme.com
 * Kopa Page Builder is distributed under the terms of the GNU GPL
 *
 * Requires at least: 4.4
 * Tested up to: 4.7
 * Text Domain: kopa-page-builder
 * Domain Path: /languages/
 */


if ( ! class_exists( 'Kopa_Page_Builder' ) ) {

	define( 'KPB_IS_DEV', false );
	define( 'KPB_DIR', plugin_dir_url( __FILE__ ) );
	define( 'KPB_PATH', plugin_dir_path( __FILE__ ) );

	add_action( 'plugins_loaded', array( 'Kopa_Page_Builder', 'plugins_loaded' ) );
	add_action( 'after_setup_theme', array( 'Kopa_Page_Builder', 'get_instance' ), 20 );

	include_once KPB_PATH . 'inc/class-kpb-autoloader.php';

	/**
	 * The main class for plugin Kopa Page Builder.
	 */
	class Kopa_Page_Builder {

		/**
		 * The instance of class Kopa_Page_Builder.
		 *
		 * @var object $instance
		 */
		protected static $instance = null;

		/**
		 * The function for singleton pattern.
		 *
		 * @return object
		 */
		static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		/**
		 * The construct function of object Kopa_Page_Builder.
		 */
		function __construct() {
			add_action( 'admin_menu', array( $this, 'admin_menu' ), 99 );
			add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );

			if ( is_admin() ) {
				KPB_Ajax::get_instance();
				KPB_Layout::get_instance();
				KPB_Row::get_instance();
				KPB_Col::get_instance();
				KPB_Widget::get_instance();
				KPB_Admin_Assets::get_instance();
				KPB_Editor::get_instance();
			}

			KPB_Shortcode::get_instance();
		}

		/**
		 * Force remove metabox "postcustom".
		 *
		 * @return void
		 */
		function admin_menu() {
			remove_meta_box( 'postcustom', 'page', 'normal' );
		}

		/**
		 * Register page-builder metabox.
		 *
		 * @return void
		 */
		function register_meta_boxes() {
			add_meta_box( 'kpb-metabox', esc_html__( 'Page Builder', 'kopa-page-builder' ), array( $this, 'get_meta_boxes' ), 'page', 'advanced', 'high' );
		}

		/**
		 * Print page-builder metabox.
		 *
		 * @return void
		 */
		function get_meta_boxes() {
			global $post;

			wp_nonce_field( 'kpb-metabox', 'kpb-metabox_security' );

			$obj_layout     = KPB_Layout::get_instance();
			$layouts        = $obj_layout->get_layouts();
			$current_layout = self::get_current_layout( $post->ID );
			$cbo_layouts    = $obj_layout->get_key_current_layout();
			?>          

      <section id="kpb-wrapper">
          
          <header id="kpb-wrapper-header" class="kpb-clearfix">
              
						<select id="kpb-select-layout" name="<?php echo esc_attr( $cbo_layouts ); ?>" class="kpb-pull-left" onchange="KPB_Layouts.change(event, jQuery(this));" autocomplete="off">
							<?php	foreach ( $layouts as $layout_slug => $layout ) : ?>
								<option value="<?php echo esc_attr( $layout_slug ); ?>" <?php selected( $current_layout, $layout_slug, true ) ?>><?php echo esc_html( $layout['title'] ); ?></option>						
							<?php endforeach; ?>
						</select>                                           

						<a id="kpb-button-save-layouts" href="#" onclick="KPB_Layout.save_layout( event, jQuery( this ) );" class="button button-primary button-large kpb-pull-right" data-status='1'><?php esc_html_e( 'Save', 'kopa-page-builder' ); ?></a>       
						<a id="kpb-button-hide-preview" href="#" onclick="KPB_Tips.hide_screenshot( event, jQuery( this ) );" class="button button-secondary button-large kpb-pull-right" data-status='0' style="display: none;"><?php esc_html_e( 'Show visual layout', 'kopa-page-builder' ); ?></a>
						<a id="kpb-button-customize-layout" href="#" onclick="KPB_Layout_Customize.open( event, jQuery( this ) );" class="button button-secondary button-large kpb-pull-right" style="display: none;"><?php esc_html_e( 'Customize', 'kopa-page-builder' ); ?></a>                  
	                    
          </header>
                          
      </section>
			<?php
		}

		/**
		 * Register plugin text-domain.
		 *
		 * @return void
		 */
		static function plugins_loaded() {
			load_plugin_textdomain( 'kopa-page-builder', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Turn on/off featured "minify"
		 *
		 * @return bool $is_allow_minify
		 */
		static function is_allow_minify() {
			return apply_filters( 'kopa_page_builder_is_allow_minify', false );
		}

		/**
		 * Get list of registed layouts.
		 *
		 * @return array
		 */
		static function get_registed_layouts() {
			$obj_layout = KPB_Layout::get_instance();
			return $obj_layout->get_layouts();
		}

		/**
		 * Get layout information by layout slug.
		 * 
		 * @param  string $layout_slug Slug or Name of a layout.
		 * @return array
		 */
		static function get_layout( $layout_slug = '' ) {
			$obj_layout = KPB_Layout::get_instance();
			return $obj_layout->get_layout( $layout_slug );
		}

		/**
		 * Get slug / name of current / activated layout.
		 *
		 * @param integer $post_id ID of a post.
		 */
		static function get_current_layout( $post_id = 0 ) {
			$obj_layout = KPB_Layout::get_instance();
			return $obj_layout->get_current_layout( $post_id );
		}

		/**
		 * Get grid data for page.
		 *
		 * @param  integer $post_id ID of a post.
		 * @param  string  $layout_slug Slug or Name of a layout.
		 * @return array
		 */
		static function get_layout_data( $post_id = 0, $layout_slug = '' ) {
			$obj_layout = KPB_Layout::get_instance();
			return $obj_layout->get_data( $post_id, $layout_slug );
		}

		/**
		 * Get customize data for a layout.
		 *
		 * @param  integer $post_id ID of a post.
		 * @param  string  $layout_slug Slug or Name of a layout.
		 * @return array
		 */
		static function get_layout_customize_data( $post_id = 0, $layout_slug = '' ) {
			$obj_layout = KPB_Layout::get_instance();
			return $obj_layout->get_data_customize( $post_id, $layout_slug );
		}

		/**
		 * Get customize data for a row.
		 *
		 * @param  integer $post_id ID of a post.
		 * @param  string  $layout_slug Slug or Name of a layout.
		 * @param  string  $row_slug Slug or Name of a row.
		 * @return array
		 */
		static function get_row_customize_data( $post_id = 0, $layout_slug = '', $row_slug = '' ) {
			$obj_row = KPB_Row::get_instance();
			return $obj_row->get_data( $post_id, $layout_slug, $row_slug );
		}

		/**
		 * Get customize data for a column.
		 *
		 * @param  integer $post_id ID of a post.
		 * @param  string  $layout_slug Slug or Name of a layout.
		 * @param  string  $row_slug Slug or Name of a row.
		 * @param  string  $col_slug Slug or Name of a column.
		 * @return array
		 */
		static function get_col_customize_data( $post_id = 0, $layout_slug = '', $row_slug = '', $col_slug = '' ) {
			$obj_col = KPB_Col::get_instance();
			return $obj_col->get_data( $post_id, $layout_slug, $row_slug, $col_slug );
		}

		/**
		 * Check widget exist?
		 * @param  integer $post_id           ID of a post.
		 * @param  string  $widget_class_name class of widget
		 * @return boolean $is_exist
		 */
		static function is_exist_widget( $post_id = 0, $widget_class_name = '' ) {
			$layout_slug = self::get_current_layout( $post_id );
			$obj_widget  = KPB_Widget::get_instance();
			
			return $obj_widget->is_exist( $post_id, $layout_slug, $widget_class_name );
		}

		/**
		 * Get customize data for a row.
		 * Has been replaced by get_row_customize_data( $post_id, $layout_slug, $row_slug ).
		 *
		 * @deprecated since 2.0.0
		 * @param  integer $post_id ID of a post.
		 * @param  string  $layout_slug Slug or Name of a layout.
		 * @param  string  $row_slug Slug or Name of a row.
		 * @return array
		 */
		static function get_current_wrapper_data( $post_id = 0, $layout_slug = '', $row_slug = '' ) {
			return self::get_row_customize_data( $post_id, $layout_slug, $row_slug );
		}

		/**
		 * Get grid data for page.
		 * Has been replaced by get_layout_data( $post_id, $layout_slug, $row_slug ).
		 *
		 * @deprecated since 2.0.0
		 * @param  integer $post_id ID of a post.
		 * @param  string  $layout_slug Slug or Name of a layout.
		 * @return array
		 */
		static function get_current_layout_data( $post_id = 0, $layout_slug = '' ) {
			return self::get_layout_data( $post_id, $layout_slug );
		}		
	}

}
