"use strict"

kpb_current_widget      = {}
kpb_current_sidebar     = {}
kpb_media               = false
kpb_media_button_upload = {}
kpb_media_button_reset  = {}
$kpb_media_2nd          = false

jQuery(window).on 'load',() ->
	KPB.prepare()
	return

KPB =
	_get_builder_id: () ->
		return '#kpb-metabox'

	_get_btn_toggle_id: () ->
		return '#kpb-toggle-editor'
	
	_get_btn_save_id: () ->
		return '#kpb-button-save-layouts'

	get_elements: () ->
		return '#wp-content-editor-container, #post-status-info, #insert-media-button, .wp-editor-tabs'

	prepare: () ->
		$current_layout = jQuery( '#kpb-select-layout option:selected').val()

		if 'disable' != $current_layout
			KPB_Layout.load( $current_layout )			

		return

	toggle: ( event ) ->
		event.preventDefault()
		$button = jQuery( KPB._get_btn_toggle_id() )
		$buider = jQuery( KPB._get_builder_id() )

		if $buider.is(":visible")
			KPB.off( $button )
		else
			KPB.on( $button )

		return

	on: ( $button ) ->
		jQuery( KPB.get_elements() ).hide()
		jQuery( KPB._get_builder_id() ).show()
		$button.addClass( 'button-primary' )
		return

	off: ( $button ) ->
		jQuery( KPB.get_elements() ).show()
		jQuery( KPB._get_builder_id() ).hide()
		$button.removeClass( 'button-primary' )
		return

	force_save: () ->
		jQuery( KPB._get_btn_save_id() ).click()
		return

KPB_Main_Form = 
	_get_form_id: () ->
		return '#post'

	mark_it: ( $is_mark ) ->
		$form = jQuery KPB_Main_Form._get_form_id()

		if $form.length
			if $is_mark
				$form.addClass 'kpb-marked'
			else
				$form.removeClass 'kpb-marked'

		return

	do_submit: () ->
		return

KPB_Wrapper = 
	_get_id: () ->
		return '#kpb-wrapper'

KPB_Overlay = 
	_get_id: () ->
		return '#kpb-loading-overlay'

	show: ( $message )->		
		jQuery( KPB_Overlay._get_id() ).stop().animate
			display: 'block'
			bottom: '0px'
			,500		
		return

	hide: ( $message )->		
		jQuery( KPB_Overlay._get_id() ).animate
			display: 'none'
			bottom: '-100px'
			,500		
		return

KPB_Assets = 
	init: () ->
		KPB_UI.init()
		KPB_Assets.init_tooltip()
		KPB_Assets.init_tabs()
		KPB_Assets.init_sortable()
		return

	init_tooltip: () ->
		jQuery( '.kpb-tooltip' ).tooltip
			tooltipClass: 'kpb-ui-tooltip'
			position: { 
				my: 'center bottom'
				at: 'center top-6'
			}
			show: { 
				effect: "fade"
				duration: 300 
			}
			hide: { 
				effect: "fade"
				duration: 300 
			}
		return

	force_close_tooltip: () ->
		jQuery( '.kpb-tooltip' ).tooltip( 'close' )
		return

	init_tabs: () ->
		tabs = jQuery( '.kpb-tab-title > a')
		if tabs.length > 0
			tabs.each (index, element) ->
				tab = jQuery this
				tab.click (event) ->
					event.preventDefault()

					root = tab.parents( '.kpb-tabs')
					parent = tab.parent()
					
					if !parent.hasClass( 'kpb-tab-title-active')
						root.find( '.kpb-tab-title-active').removeClass( 'kpb-tab-title-active')
						root.find( '.kpb-tab-content').slideUp 500

						parent.addClass( 'kpb-tab-title-active')
						jQuery(tab.attr 'href').slideDown 500

					return
				return
		return

	init_sortable: () ->
		jQuery( KPB_Widget._get_placeholder_class()).sortable			
			forcePlaceholderSize: true
			connectWith: KPB_Widget._get_placeholder_class()
			placeholder: "kpb-widget-sortable-placeholder"
			start: (e, ui) ->
				ui.placeholder.height ui.helper.outerHeight() - 2
		.disableSelection()
		return

KPB_Widgets = 
	_get_id: () ->
		return '#kpb-widgets-lightbox'

	load: () ->
		jQuery.ajax
			url: KPB_Config.ajax
			dataType: 'html'
			type: 'POST'
			async: true
			data:
				action: 'kpb_load_widgets'
				security: jQuery( '#kpb_load_widgets_security').val()
			beforeSend: ( jqXHR ) ->					
				KPB_Overlay.show()
				return				
			success: ( data, textStatus, jqXHR ) ->
				if data				
					jQuery( 'body' ).append( data )					  					
					return
			complete: ( jqXHR, textStatus ) ->
				KPB_Assets.init()			
				KPB_Widgets.show()
				KPB_Overlay.hide()
				return
		return

	show: () ->
		lightbox = KPB_Widgets._get_id()

		jQuery.magnificPopup.open
			callbacks:
				open: () ->
					jQuery( lightbox ).show()
					return
				close: () ->
					jQuery( lightbox ).hide()
					return
			modal: true
			preloader: true
			alignTop: true
			items:
		 		src: lightbox
		 		type: 'inline'

		return

	open: ( event, obj ) ->
		event.preventDefault()
		kpb_current_sidebar = obj.parents '.kpb-area'
		lightbox            = KPB_Widgets._get_id()

		if !jQuery( lightbox ).length
			KPB_Widgets.load()		
		else
			KPB_Widgets.show()

		return

	close: ( event ) ->
		event.preventDefault()
		jQuery.magnificPopup.close()
		return

KPB_Widget = 
	_get_id: () ->
		return '#kpb-widget-lightbox'

	_get_placeholder_class: () ->
		return '.kpb-area-placeholder'

	get_placeholder: () ->		
		if !jQuery( KPB_Widget._get_id() ).length			
			jQuery.ajax
				url: KPB_Config.ajax
				dataType: "html"
				type: 'POST'
				async: true
				data:
					action: 'kpb_load_widget_placeholder'
					security: jQuery( '#kpb_load_widget_placeholder_security').val()							
					post_id: jQuery( '#post_ID').val()		
				beforeSend: ( jqXHR ) ->					
					KPB_Overlay.show()
					return
				success: (data, textStatus, jqXHR) ->			
					jQuery( 'body').append(data)			
					return
				complete: ( jqXHR, textStatus ) ->
					KPB_Assets.init()
					KPB_Overlay.hide()
					return				
		return

	prepare_form: ( widget_id, widget_name, widget_title, widget_class, action ) ->			
		jQuery( '#kpb-widget input[name=kpb-widget-id]').val widget_id
		jQuery( '#kpb-widget input[name=kpb-widget-name]').val widget_name
		jQuery( '#kpb-widget-title').text widget_title									
		jQuery( '#kpb-widget input[name=kpb-widget-class-name]').val widget_class						
		jQuery( '#kpb-widget input[name=kpb-widget-action]').val action
		return

	load_form: ( class_name, widget_id ) ->		
		jQuery.ajax
			url: KPB_Config.ajax
			dataType: "html"
			type: 'POST'
			async: true
			data:
				action: 'kpb_load_widget_form'
				security: jQuery( '#kpb_load_widget_form_security').val()							
				widget_id: widget_id
				class_name: class_name
				post_id: jQuery( '#post_ID').val()
			success: (data, textStatus, jqXHR) ->
				jQuery( '#kpb-widget .kpb-form-inner').html data																
				return							
			beforeSend: ()->
				KPB_Overlay.show()
				return
			complete: ( jqXHR, textStatus ) ->
				KPB_Assets.init()
				KPB_Overlay.hide()
				return
		return

	add: ( event, obj, class_name, widget_name ) ->
		event.preventDefault()

		jQuery.magnificPopup.close()

		lightbox = KPB_Widget._get_id()

		jQuery.magnificPopup.open
			callbacks:
				open: () ->
					jQuery( lightbox ).show()
					widget_id = KPB_Util.get_random_id( 'widget-' )
					KPB_Widget.prepare_form( widget_id, widget_name, widget_name, class_name, 'add' )
					KPB_Widget.load_form( class_name, widget_id )

					return
				close: () ->
					KPB_Widget.close(event)
					return
			modal: true
			preloader: true
			alignTop: true
			items:
		 		src: lightbox
		 		type: 'inline'
		return

	edit: ( event, obj, widget_id ) ->
		event.preventDefault()

		kpb_current_widget = obj.parents '.kpb-widget'
		lightbox           = KPB_Widget._get_id()
		class_name         = kpb_current_widget.attr 'data-class'
		widget_name        = kpb_current_widget.attr 'data-name'
		widget_title       = kpb_current_widget.find( 'label').text()		

		jQuery.magnificPopup.open
			callbacks:
				open: () ->
					jQuery(lightbox).show()

					KPB_Widget.prepare_form( widget_id, widget_name, widget_title, class_name, 'edit' )
					KPB_Widget.load_form( class_name, widget_id )

					return

				close: () ->
					KPB_Widget.close(event)
					return
			modal: true
			preloader: true
			alignTop: true
			items:
		 		src: lightbox
		 		type: 'inline'
		 	fixedBgPos: true		 		

		return

	delete: ( event, obj, widget_id ) ->
		event.preventDefault()
		answer = confirm KPB_Config.i18n.are_you_sure_to_remove_this_widget
		if answer
			$wrap       = obj.closest( '.kpb-widget' )
			class_name  = $wrap.attr( 'data-class' )
			layout_slug = jQuery( '#kpb-select-layout option:selected').val()
			post_id     = jQuery( '#post_ID').val()
			
			jQuery.ajax
				url: KPB_Config.ajax
				dataType: "html"
				type: 'POST'
				async: true
				data:
					action: 'kpb_delete_widget'
					security: jQuery( '#kpb_delete_widget_security').val()							
					widget_id: widget_id
					post_id: post_id
					class_name: class_name
					layout_slug: layout_slug
				success: (data, textStatus, jqXHR) ->			
					$wrap.remove()
					KPB.force_save()
					return
				beforeSend: (jqXHR) ->
					KPB_Overlay.show()		
					return					
				complete: ( jqXHR, textStatus ) ->					
					KPB_Overlay.hide()							
					return	
		return

	save: ( event, obj ) ->
		event.preventDefault()
		
		obj.ajaxSubmit
			dataType: 'json'
			type: 'POST'
			async: true		
			beforeSubmit: (arr, $form, options) ->				
				KPB_Overlay.show()
				return

			success: ( response, statusText, xhr, $form ) ->
				
				jQuery( '#kpb-widget input[name=kpb-widget-action]').val 'edit'

				if 'edit' == response.action
					if !kpb_current_widget.length
						kpb_current_widget = jQuery( '#' + response.id )

					if kpb_current_widget.length
						kpb_current_widget.find( 'label' ).text  response.label.kpb_escape()

				else if 'add' == response.action
					
					if response.visual
						kpb_current_sidebar.find( KPB_Widget._get_placeholder_class() ).append response.visual
				
				jQuery( '#kpb-tab-widget-kpb' ).html response.form.kpb_escape()

				KPB.force_save()				

				return

			complete: ( jqXHR, textStatus ) ->					
				KPB_Assets.init()
				KPB_Overlay.hide()
				return					
		return

	close: ( event ) ->
		event.preventDefault()
		jQuery.magnificPopup.close()
		jQuery( '#kpb-widget .kpb-form-inner').html '<center class="kpb-loading">' + KPB_Config.i18n.loading + '</center>'
		KPB_Overlay.hide()

		jQuery( '#kpb-widget-title').text ''
		jQuery( '#kpb-widget input[name=kpb-widget-class-name]').val ''				
		jQuery( '#kpb-widget input[name=kpb-widget-action]').val 'add'
		jQuery( '#kpb-widget input[name=kpb-widget-id]').val ''					
		jQuery( '#kpb-widget input[name=kpb-widget-name]').val ''	
		return		

KPB_Row = 
	_get_id: ( layout_slug, row_slug ) ->
		return '#kpb-customize-lightbox-' + layout_slug + '-' + row_slug

	load: ( obj, layout_slug, row_slug ) ->
		jQuery.ajax
			url: KPB_Config.ajax
			dataType: 'html'
			type: 'POST'
			async: true
			data:
				action: 'kpb_load_row_customize'
				security: jQuery( '#kpb_load_row_customize_security').val()
				post_id: jQuery( '#post_ID' ).val()
				layout_slug: layout_slug
				row_slug: row_slug
			beforeSend: ()->
				KPB_Overlay.show()
				return
			success: ( data, textStatus, jqXHR ) ->
				if data
					jQuery( 'body' ).append( data )					  					
					return
			complete: ( jqXHR, textStatus ) ->
				KPB_Overlay.hide()
				KPB_Assets.init()
				KPB_Row.show( obj, layout_slug, row_slug )
				return
		return

	show: ( obj, layout_slug, row_slug ) ->
		lightbox = KPB_Row._get_id( layout_slug, row_slug )

		jQuery.magnificPopup.open
			callbacks:
				open: () ->
					jQuery( lightbox ).show()
					return
				close: () ->
					jQuery( lightbox ).hide()
					return
			modal: true
			preloader: true
			alignTop: true
			items:
		 		src: lightbox
		 		type: 'inline'		
		return

	open: ( event, obj, layout_slug, row_slug ) ->
		event.preventDefault()
				
		lightbox = KPB_Row._get_id( layout_slug, row_slug )		

		if !jQuery( lightbox ).length
			KPB_Row.load( obj, layout_slug, row_slug )
		else
			KPB_Row.show( obj, layout_slug, row_slug )
					
		return	

	save: ( event, obj, post_id ) ->
		event.preventDefault()		

		obj.ajaxSubmit
			beforeSubmit: (arr, $form, options) ->				
				KPB_Overlay.show()
				return
			success: (responseText, statusText, xhr, $form) ->								
				KPB_Overlay.hide()				
				return		
			data:
				post_id: post_id

		return

	close: ( event ) ->
		event.preventDefault()
		KPB_Widget.close(event)
		return

KPB_Col = 
	_get_id: ( layout_slug, row_slug, col_slug ) ->
		return '#kpb-customize-lightbox-' + layout_slug + '-' + row_slug + '-' + col_slug

	edit: ( event, obj ) ->
		event.preventDefault()

		col_slug    = obj.closest( '.kpb-area' ).attr( 'data-area' )
		row_slug    = obj.closest( '.kpb-section' ).attr( 'data-section' )
		layout_slug = obj.closest( '.kpb-layout ' ).attr( 'data-layout' )

		lightbox = KPB_Col._get_id( layout_slug, row_slug, col_slug )
		
		if jQuery( lightbox ).length
			KPB_Col.show( layout_slug, row_slug, col_slug  )
		else
			KPB_Col.load( layout_slug, row_slug, col_slug  )

		return

	load: ( layout_slug, row_slug, col_slug ) ->
		jQuery.ajax
			url: KPB_Config.ajax
			dataType: 'html'
			type: 'POST'
			async: true
			data:
				action: 'kpb_load_col_customize'
				security: jQuery( '#kpb_load_col_customize_security').val()
				post_id: jQuery( '#post_ID' ).val()
				layout_slug: layout_slug
				row_slug: row_slug
				col_slug: col_slug
			beforeSend: ()->
				KPB_Overlay.show()
				return
			success: ( data, textStatus, jqXHR ) ->
				if data
					jQuery( 'body' ).append( data )					  					
					return
			complete: ( jqXHR, textStatus ) ->
				KPB_Overlay.hide()
				KPB_Assets.init()
				KPB_Col.show( layout_slug, row_slug, col_slug )
				return
		return

	show: ( layout_slug, row_slug, col_slug ) ->
		lightbox = KPB_Col._get_id( layout_slug, row_slug, col_slug )

		jQuery.magnificPopup.open
			callbacks:
				open: () ->
					jQuery( lightbox ).show()
					return
				close: () ->
					jQuery( lightbox ).hide()
					return
			modal: true
			preloader: true
			alignTop: true
			items:
		 		src: lightbox
		 		type: 'inline'		
		return

	save_customize: ( event, obj ) ->
		event.preventDefault()

		obj.ajaxSubmit
			beforeSubmit: (arr, $form, options) ->				
				KPB_Overlay.show()
				return
			success: ( responseText, statusText, xhr, $form ) ->								
				KPB_Overlay.hide()				
				return		
			data:
				post_id: jQuery( '#post_ID' ).val()
		return

	close_customize: ( event ) ->
		event.preventDefault()
		KPB_Lightbox.close()
		return

KPB_Layouts =
	change: ( event, obj ) ->
		event.preventDefault()
		
		layout_slug = obj.find( 'option:selected').val()
		
		if !jQuery( KPB_Layout._get_id( layout_slug ) ).length
			KPB_Layout.load( layout_slug )			
		else
			KPB_Layout.change( layout_slug )

		return

KPB_Layout = 
	_get_id: ( layout_slug ) ->
		return '#kpb-layout-' + layout_slug

	get_btn_customize_id: () ->
		return '#kpb-button-customize-layout'

	get_btn_preview_id: () ->
		return '#kpb-button-hide-preview'

	load: ( layout_slug ) ->
		jQuery.ajax
			url: KPB_Config.ajax
			dataType: 'html'
			type: 'POST'
			async: true
			data:
				action: 'kpb_load_layout'
				security: jQuery( '#kpb_load_layout_security').val()
				post_id: jQuery( '#post_ID' ).val()
				layout_slug: layout_slug
			beforeSend: ()->
				KPB_Overlay.show()
				return
			success: ( data, textStatus, jqXHR ) ->
				if data					
					jQuery( KPB_Wrapper._get_id() ).append( data )					
					KPB_Widget.get_placeholder()
					return
			complete: ( jqXHR, textStatus ) ->
				KPB_Overlay.hide()
				KPB_Assets.init()		
				KPB_Layout.change( layout_slug )
				return
		return

	change: ( layout_slug ) ->
		layout_id = KPB_Layout._get_id( layout_slug )

		if !jQuery( layout_id ).hasClass( 'kpb-active')
			jQuery( '.kpb-layout.kpb-active').removeClass( 'kpb-active').addClass( 'kpb-hidden')
			jQuery( layout_id ).removeClass( 'kpb-hidden').addClass( 'kpb-active')

			KPB_Layout.toggle_customize( layout_id )
			KPB_Layout.toggle_preview( layout_id )

		is_mark = ( 'disable' != layout_slug )
		KPB_Main_Form.mark_it( is_mark )		

		return

	toggle_customize: ( layout_id ) ->
		has_customize = parseInt( jQuery( layout_id ).attr( 'data-has-customize' ) )
		if has_customize
			jQuery( KPB_Layout.get_btn_customize_id() ).show()
		else
			jQuery( KPB_Layout.get_btn_customize_id() ).hide()	
		return

	toggle_preview: ( layout_id ) ->
		has_customize = parseInt( jQuery( layout_id ).attr( 'data-has-preview' ) )
		if has_customize
			jQuery( KPB_Layout.get_btn_preview_id() ).show()
		else
			jQuery( KPB_Layout.get_btn_preview_id() ).hide()	
		return		

	save_layout: ( event, $btn_save ) ->
		event.preventDefault()

		data = KPB_Layout.grab_layout_data( $btn_save )

		jQuery.ajax
			url: KPB_Config.ajax
			dataType: "html"
			type: 'POST'
			async: true
			data:
				action: 'kpb_save_layout'
				security: jQuery( '#kpb_save_layout_security').val()
				data: data
				post_id: jQuery( '#post_ID').val()		
			error: (jqXHR, textStatus, errorThrown) ->				
				return
			beforeSend: (jqXHR) ->
				KPB_Overlay.show()
				$btn_save.text KPB_Config.i18n.saving
				return 
			success: (data, textStatus, jqXHR) ->
				$btn_save.text KPB_Config.i18n.save				
				return
			complete: () ->
				KPB_Overlay.hide()
				return
		return

	grab_layout_data: ( $btn_save ) ->
		layout_slug = jQuery( '#kpb-select-layout option:selected').val()

		$layout = jQuery( KPB_Layout._get_id( layout_slug ) )
		data =
			layout_slug: layout_slug
			rows: []

		#SECTION
		rows = $layout.find '.kpb-section'

		if rows.length > 0
			rows.each (s_index, s_element) ->
				current_section = jQuery s_element
				row_data =
					name: current_section.attr 'data-section'
					cols: []

				#AREA
				cols = current_section.find '.kpb-area'

				if cols.length > 0
					cols.each (a_index, a_element) ->
						current_area = jQuery a_element
						col_data =
							name: current_area.attr 'data-area'
							widgets: []

						#WIDGET
						widgets = current_area.find '.kpb-widget'

						if widgets.length > 0
							widgets.each (w_index, w_element) ->
								current_widget = jQuery w_element										
								widget_data =
									id: current_widget.attr 'id'											
									name: current_widget.attr 'data-name'
									class_name: current_widget.attr 'data-class'

								col_data.widgets.push widget_data
								return

							row_data.cols.push col_data

						return

					if row_data.cols.length	
						data.rows.push row_data
											
				return				
		
		return data

KPB_Layout_Customize =
	_get_id: ( layout_slug ) ->
		return '#kpb-layout-customize-lightbox-' + layout_slug

	load: ( layout_slug ) ->
		jQuery.ajax
			url: KPB_Config.ajax
			dataType: 'html'
			type: 'POST'
			async: true
			data:
				action: 'kpb_load_layout_customize'
				security: jQuery( '#kpb_load_layout_customize_security').val()
				layout_slug: layout_slug
				post_id: jQuery( '#post_ID' ).val()
			beforeSend: ( jqXHR ) ->					
				KPB_Overlay.show()
				return				
			success: ( data, textStatus, jqXHR ) ->
				if data				
					jQuery( 'body' ).append( data )					  					
					return
			complete: ( jqXHR, textStatus ) ->
				KPB_Assets.init()
				KPB_Layout_Customize.show( layout_slug )
				KPB_Overlay.hide()
				return		
		return

	show: ( layout_slug ) ->
		lightbox = KPB_Layout_Customize._get_id( layout_slug )
		
		if jQuery( lightbox ).length
			jQuery.magnificPopup.open
				callbacks:
					open: () ->
						jQuery( lightbox ).show()
						return
					close: () ->
						jQuery( lightbox ).hide()
						return
				modal: true
				preloader: true
				alignTop: true
				items:
			 		src: lightbox
			 		type: 'inline'	
		return
	
	open: ( event, obj ) ->
		event.preventDefault()

		layout_slug = jQuery( '#kpb-select-layout option:selected').val()	
		lightbox = KPB_Layout_Customize._get_id( layout_slug )

		if !jQuery( lightbox ).length
			KPB_Layout_Customize.load( layout_slug )
		else
			KPB_Layout_Customize.show( layout_slug )

		return	

	save: ( event, obj, post_id ) ->
		event.preventDefault()		

		obj.ajaxSubmit
			beforeSubmit: (arr, $form, options) ->				
				KPB_Overlay.show()
				return
			success: (responseText, statusText, xhr, $form) ->								
				KPB_Overlay.hide()
				return		
			data:
				post_id: post_id	
		return

	close: ( event ) ->
		event.preventDefault()
		KPB_Widget.close(event)
		return

KPB_Util = 
	get_random_id: (prefix) ->
    prefix + Date.now().toString(36).substr(2, 5)

KPB_UI = 
	init: ()->
		KPB_UI_Image.init()
		KPB_UI_Color.init()
		KPB_UI_Numeric_Slider.init()		
		return

KPB_UI_Image = 
	init: () ->	
		jQuery( '.kpb-ui-image-outer').on 'click', '.kpb-ui-image-button-upload', (event)->
			event.preventDefault()

			kpb_media_button_upload = jQuery this
					
			if (kpb_media)
				kpb_media.open()
				return

			kpb_media = wp.media.frames.kpb_media = wp.media
				title: KPB_Config.i18n.media_center
				button:
					text: KPB_Config.i18n.choose_image         
				library:
					type: 'image'
				multiple: false		            

			kpb_media.on 'select', () ->
				attachment = kpb_media.state().get( 'selection').first().toJSON()
				kpb_media_button_upload.parents( '.kpb-ui-image-outer').find( '.kpb-ui-image').val attachment.url
				kpb_media_button_upload.parents( '.kpb-ui-image-outer').find( '.kpb-ui-image-preview').attr 'src', attachment.url
				return				

			kpb_media.open()

			return	

		jQuery( '.kpb-ui-image-outer').on 'click', '.kpb-ui-image-button-reset', (event)->
			event.preventDefault()
			kpb_media_button_reset = jQuery this

			if kpb_media_button_reset.attr( 'data-reset')
				kpb_media_button_reset.parents( '.kpb-ui-image-outer').find( '.kpb-ui-image').val kpb_media_button_reset.attr 'data-reset'
				kpb_media_button_reset.parents( '.kpb-ui-image-outer').find( '.kpb-ui-image-preview').attr 'src', kpb_media_button_reset.attr 'data-reset'
			else
				kpb_media_button_reset.parents( '.kpb-ui-image-outer').find( '.kpb-ui-image').val kpb_media_button_reset.attr ''
				kpb_media_button_reset.parents( '.kpb-ui-image-outer').find( '.kpb-ui-image-preview').attr 'src', kpb_media_button_reset.attr 'data-preview'				

			return

		return

KPB_UI_Color = 
	init: () ->
		jQuery( '.kpb-ui-color' ).wpColorPicker()
		return

KPB_UI_Numeric_Slider = 
	_get_class: ()->
		return '.kpb-ui-numeric-slider'

	init: () ->
		$sliders = jQuery( KPB_UI_Numeric_Slider._get_class() )
		if $sliders.length

			jQuery.each $sliders, ()->

				$input     = jQuery(this)
				$outer     = $input.closest( '.kpb-ui-numeric-slider--outer' )	
				$slider    = $outer.find( '.kpb-ui-numeric-slider--control' )
				$filler    = $outer.find( '.kpb-ui-numeric-slider--filler' )				
				$previewer = $outer.find( '.kpb-ui-numeric-slider--preview' )			
				
				start      = $input.attr( 'data-start' )
				prefix     = $input.attr( 'data-prefix' )
				affix      = $input.attr( 'data-affix' )
				preview    = $input.attr( 'data-preview' )
				min        = parseFloat( $input.attr( 'data-min' ) )
				max        = parseFloat( $input.attr( 'data-max' ) )
				step       = parseFloat( $input.attr( 'data-step' ) )
				
				value      = $input.val()


				$slider.slider
		      value: value
		      min: min
		      max: max
		      step: step
		      slide: ( event, ui )->
		      	value = parseFloat( ui.value )
		      	width = (value / max) * 100
		      	$input.val( ui.value )

		      	if value > 0
		        	if 'percent' == preview
		        		$previewer.text( width.toString().substring( 0, 5 )  + '%' )
		        	else
		        		$previewer.text( prefix + ui.value + affix )

		        	$filler.css 'width', width + '%'
		       	else
		       		$previewer.text prefix + start + affix
		       		$filler.css 'width', '0%'
		      	return

				return

		return

KPB_UI_Attachment_Image =
	edit: ( event, $button_edit ) ->
		event.preventDefault();			

		$control       = $button_edit.parent()
		$input         = $control.find( '.kpb-ui-attachment-image--input' )
		$thumb         = $control.find( '.kpb-ui-attachment-image--thumb' ).first()
		$button_remove = $control.find( '.kpb-ui-attachment-image--remove' ).first()

		if $kpb_media_2nd			
			$kpb_media_2nd.open()			
		else			
			$kpb_media_2nd = wp.media.frames.$kpb_media_2nd = wp.media
				title: KPB_Config.i18n.media_center
				button:
					text: KPB_Config.i18n.choose_image         
				library:
					type: 'image'
				multiple: false

			$kpb_media_2nd.on 'open', () ->
				$thumb_id = parseInt( $input.val(), 10 )
				if $thumb_id
					$selection  = $kpb_media_2nd.state().get( 'selection' )
					$attachment = wp.media.attachment( $thumb_id )
					$attachment.fetch()
					$selection.add( $attachment )
				return

			$kpb_media_2nd.on 'select', () ->
				$attachment = $kpb_media_2nd.state().get( 'selection' ).first().toJSON()
				$input.val $attachment.id
				$thumb.attr 'src', $attachment.sizes.thumbnail.url
				$thumb.removeClass 'kpb--is_hidden'
				$button_edit.hide()
				$button_remove.show()

				$kpb_media_2nd = false
				return

			$kpb_media_2nd.open()
		
		return

	remove: ( event, $button_remove ) ->
		event.preventDefault();			

		$control     = $button_remove.parent()
		$input       = $control.find( '.kpb-ui-attachment-image--input' )
		$thumb       = $control.find( '.kpb-ui-attachment-image--thumb' ).first()
		$button_edit = $control.find( '.kpb-ui-attachment-image--edit' ).first()	
		$input.val( 0 );
		$thumb.attr 'src', ''
		$thumb.addClass 'kpb--is_hidden'
		$button_edit.show()
		$button_remove.hide()

		return

KPB_Lightbox =
	close: () ->
		jQuery.magnificPopup.close()
		return

KPB_Tips = 
	hide_screenshot: ( event, obj )->
		event.preventDefault()
		
		if obj.attr( 'data-status') is '0'
			jQuery( '.kpb-layout > .kpb-row > .kpb-col-left').removeClass( 'kpb-col-12').addClass( 'kpb-col-8')
			jQuery( '.kpb-layout > .kpb-row > .kpb-col-right').show()
			obj.attr 'data-status', '1'
			obj.text KPB_Config.i18n.hide_preview
		else			
			jQuery( '.kpb-layout > .kpb-row > .kpb-col-left').removeClass( 'kpb-col-8').addClass( 'kpb-col-12')
			jQuery( '.kpb-layout > .kpb-row > .kpb-col-right').hide()
			obj.attr 'data-status', '0'
			obj.text KPB_Config.i18n.show_preview
		return


String::kpb_escape = ->  
  @replace(/\\n/g, '\\n').replace(/\\'/g, '\\\'').replace(/\\"/g, '\"').replace(/\\&/g, '\\&').replace(/\\r/g, '\\r').replace(/\\t/g, '\\t').replace(/\\b/g, '\\b').replace /\\f/g, '\\f'
