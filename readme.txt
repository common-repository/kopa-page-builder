=== Kopa Page Builder ===
Contributors: kopatheme, tranthethang
Tags: page builder, grid, drag and drop, content composer, layout builder, bootstrap, website builder, widgets, kopa, kopasoft, kopatheme, trathethang
Requires at least: 4.4
Tested up to: 4.7
Stable tag: 2.0.8

== Description ==

Kopa Page Builder plugin helps you create static pages by manually adding, editing or moving the widgets to the expected sidebars.
Unlike the other Page Builder plugins which available on WordPress.org now, this plugin requires a deep understanding of technical knowledge and WordPress to use for your website.

== Installation ==

This section describes how to install the plugin and get it working. E.g:

1. Upload the plugin files to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress

== Screenshots ==
1. Edit a page with page builder.
2. Customize a section / row.
3. Customize a col.
4. Custom a widget / block.

== Frequently Asked Questions ==

== Changelog ==

= 2.0.8 =
* Inherit field "icon_picker" from plugin "Kopa Framework"
* Fixed Widget Text in WordPress 4.8

= 2.0.7 =
* Tested up to WordPress 4.7

= 2.0.6 =
* Fix bug: autoloader - file not found.
* Remove API "PhpWee"

= 2.0.5 =
* Remove slash symbol by function "stripslashes"

= 2.0.4 =
* Remove function boolval()

= 2.0.3 =
* Edit: Minify Php, Css, Js with API "PhpWee"
* Add: new filter "kopa_page_builder_is_allow_minify" - return false to turn off Minify.

= 2.0.2 =
* Update field icon.

= 2.0.1 =
* Update jquery plugin "Magnific Popup" to  v1.1.0 - 2016-02-20
* Add toggle button, to switch mode Editor and Page Builder.
* Add Php Docs for all variables, fuctions, classes.
* Optimize source code: Php, Css, Js.
* Separate functions to multi classes.
* Support "php-autoload" to include files.
* Use Ajax for all action: load form, load list of widget, load customize fields,..

= 1.4 =
* English wording.

= 1.3 =
* fix: some js, css
* check: compatiable with wordpress version 4.4.1
* replace: placehold image for image field type.
* edit: don't auto close lightbox "edit widget" after save data.
* add: new field type: alert
* add: new some action hooks:
	1. kopa_page_builder_after_save_widget
	1. kopa_page_builder_after_save_grid
	1. kopa_page_builder_after_save_section_customize
	1. kopa_page_builder_after_save_layout_customize

= 1.1.1 =
* edit: some css and js

= 1.1.0 =
* fix: The feature multi language not working
* edit: Display list of widget by tabs
* add: Group all widget of bbPress to group bbPress

= 1.0.9 =
* add: support private setting foreach widget by variable $kpb_is_private

= 1.0.8 =
* fix: remove event publish (update) button click

= 1.0.7 =
* fix: some css & js

= 1.0.6 =
* add: radio_image field, support html tag in title of radio