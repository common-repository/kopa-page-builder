"use strict";
var $kpb_media_2nd, KPB, KPB_Assets, KPB_Col, KPB_Layout, KPB_Layout_Customize, KPB_Layouts, KPB_Lightbox, KPB_Main_Form, KPB_Overlay, KPB_Row, KPB_Tips, KPB_UI, KPB_UI_Attachment_Image, KPB_UI_Color, KPB_UI_Image, KPB_UI_Numeric_Slider, KPB_Util, KPB_Widget, KPB_Widgets, KPB_Wrapper, kpb_current_sidebar, kpb_current_widget, kpb_media, kpb_media_button_reset, kpb_media_button_upload;

kpb_current_widget = {};

kpb_current_sidebar = {};

kpb_media = false;

kpb_media_button_upload = {};

kpb_media_button_reset = {};

$kpb_media_2nd = false;

jQuery(window).on('load', function() {
  KPB.prepare();
});

KPB = {
  _get_builder_id: function() {
    return '#kpb-metabox';
  },
  _get_btn_toggle_id: function() {
    return '#kpb-toggle-editor';
  },
  _get_btn_save_id: function() {
    return '#kpb-button-save-layouts';
  },
  get_elements: function() {
    return '#wp-content-editor-container, #post-status-info, #insert-media-button, .wp-editor-tabs';
  },
  prepare: function() {
    var $current_layout;
    $current_layout = jQuery('#kpb-select-layout option:selected').val();
    if ('disable' !== $current_layout) {
      KPB_Layout.load($current_layout);
    }
  },
  toggle: function(event) {
    var $buider, $button;
    event.preventDefault();
    $button = jQuery(KPB._get_btn_toggle_id());
    $buider = jQuery(KPB._get_builder_id());
    if ($buider.is(":visible")) {
      KPB.off($button);
    } else {
      KPB.on($button);
    }
  },
  on: function($button) {
    jQuery(KPB.get_elements()).hide();
    jQuery(KPB._get_builder_id()).show();
    $button.addClass('button-primary');
  },
  off: function($button) {
    jQuery(KPB.get_elements()).show();
    jQuery(KPB._get_builder_id()).hide();
    $button.removeClass('button-primary');
  },
  force_save: function() {
    jQuery(KPB._get_btn_save_id()).click();
  }
};

KPB_Main_Form = {
  _get_form_id: function() {
    return '#post';
  },
  mark_it: function($is_mark) {
    var $form;
    $form = jQuery(KPB_Main_Form._get_form_id());
    if ($form.length) {
      if ($is_mark) {
        $form.addClass('kpb-marked');
      } else {
        $form.removeClass('kpb-marked');
      }
    }
  },
  do_submit: function() {}
};

KPB_Wrapper = {
  _get_id: function() {
    return '#kpb-wrapper';
  }
};

KPB_Overlay = {
  _get_id: function() {
    return '#kpb-loading-overlay';
  },
  show: function($message) {
    jQuery(KPB_Overlay._get_id()).stop().animate({
      display: 'block',
      bottom: '0px'
    }, 500);
  },
  hide: function($message) {
    jQuery(KPB_Overlay._get_id()).animate({
      display: 'none',
      bottom: '-100px'
    }, 500);
  }
};

KPB_Assets = {
  init: function() {
    KPB_UI.init();
    KPB_Assets.init_tooltip();
    KPB_Assets.init_tabs();
    KPB_Assets.init_sortable();
  },
  init_tooltip: function() {
    jQuery('.kpb-tooltip').tooltip({
      tooltipClass: 'kpb-ui-tooltip',
      position: {
        my: 'center bottom',
        at: 'center top-6'
      },
      show: {
        effect: "fade",
        duration: 300
      },
      hide: {
        effect: "fade",
        duration: 300
      }
    });
  },
  force_close_tooltip: function() {
    jQuery('.kpb-tooltip').tooltip('close');
  },
  init_tabs: function() {
    var tabs;
    tabs = jQuery('.kpb-tab-title > a');
    if (tabs.length > 0) {
      tabs.each(function(index, element) {
        var tab;
        tab = jQuery(this);
        tab.click(function(event) {
          var parent, root;
          event.preventDefault();
          root = tab.parents('.kpb-tabs');
          parent = tab.parent();
          if (!parent.hasClass('kpb-tab-title-active')) {
            root.find('.kpb-tab-title-active').removeClass('kpb-tab-title-active');
            root.find('.kpb-tab-content').slideUp(500);
            parent.addClass('kpb-tab-title-active');
            jQuery(tab.attr('href')).slideDown(500);
          }
        });
      });
    }
  },
  init_sortable: function() {
    jQuery(KPB_Widget._get_placeholder_class()).sortable({
      forcePlaceholderSize: true,
      connectWith: KPB_Widget._get_placeholder_class(),
      placeholder: "kpb-widget-sortable-placeholder",
      start: function(e, ui) {
        return ui.placeholder.height(ui.helper.outerHeight() - 2);
      }
    }).disableSelection();
  }
};

KPB_Widgets = {
  _get_id: function() {
    return '#kpb-widgets-lightbox';
  },
  load: function() {
    jQuery.ajax({
      url: KPB_Config.ajax,
      dataType: 'html',
      type: 'POST',
      async: true,
      data: {
        action: 'kpb_load_widgets',
        security: jQuery('#kpb_load_widgets_security').val()
      },
      beforeSend: function(jqXHR) {
        KPB_Overlay.show();
      },
      success: function(data, textStatus, jqXHR) {
        if (data) {
          jQuery('body').append(data);
        }
      },
      complete: function(jqXHR, textStatus) {
        KPB_Assets.init();
        KPB_Widgets.show();
        KPB_Overlay.hide();
      }
    });
  },
  show: function() {
    var lightbox;
    lightbox = KPB_Widgets._get_id();
    jQuery.magnificPopup.open({
      callbacks: {
        open: function() {
          jQuery(lightbox).show();
        },
        close: function() {
          jQuery(lightbox).hide();
        }
      },
      modal: true,
      preloader: true,
      alignTop: true,
      items: {
        src: lightbox,
        type: 'inline'
      }
    });
  },
  open: function(event, obj) {
    var lightbox;
    event.preventDefault();
    kpb_current_sidebar = obj.parents('.kpb-area');
    lightbox = KPB_Widgets._get_id();
    if (!jQuery(lightbox).length) {
      KPB_Widgets.load();
    } else {
      KPB_Widgets.show();
    }
  },
  close: function(event) {
    event.preventDefault();
    jQuery.magnificPopup.close();
  }
};

KPB_Widget = {
  _get_id: function() {
    return '#kpb-widget-lightbox';
  },
  _get_placeholder_class: function() {
    return '.kpb-area-placeholder';
  },
  get_placeholder: function() {
    if (!jQuery(KPB_Widget._get_id()).length) {
      jQuery.ajax({
        url: KPB_Config.ajax,
        dataType: "html",
        type: 'POST',
        async: true,
        data: {
          action: 'kpb_load_widget_placeholder',
          security: jQuery('#kpb_load_widget_placeholder_security').val(),
          post_id: jQuery('#post_ID').val()
        },
        beforeSend: function(jqXHR) {
          KPB_Overlay.show();
        },
        success: function(data, textStatus, jqXHR) {
          jQuery('body').append(data);
        },
        complete: function(jqXHR, textStatus) {
          KPB_Assets.init();
          KPB_Overlay.hide();
        }
      });
    }
  },
  prepare_form: function(widget_id, widget_name, widget_title, widget_class, action) {
    jQuery('#kpb-widget input[name=kpb-widget-id]').val(widget_id);
    jQuery('#kpb-widget input[name=kpb-widget-name]').val(widget_name);
    jQuery('#kpb-widget-title').text(widget_title);
    jQuery('#kpb-widget input[name=kpb-widget-class-name]').val(widget_class);
    jQuery('#kpb-widget input[name=kpb-widget-action]').val(action);
  },
  load_form: function(class_name, widget_id) {
    jQuery.ajax({
      url: KPB_Config.ajax,
      dataType: "html",
      type: 'POST',
      async: true,
      data: {
        action: 'kpb_load_widget_form',
        security: jQuery('#kpb_load_widget_form_security').val(),
        widget_id: widget_id,
        class_name: class_name,
        post_id: jQuery('#post_ID').val()
      },
      success: function(data, textStatus, jqXHR) {
        jQuery('#kpb-widget .kpb-form-inner').html(data);
      },
      beforeSend: function() {
        KPB_Overlay.show();
      },
      complete: function(jqXHR, textStatus) {
        KPB_Assets.init();
        KPB_Overlay.hide();
      }
    });
  },
  add: function(event, obj, class_name, widget_name) {
    var lightbox;
    event.preventDefault();
    jQuery.magnificPopup.close();
    lightbox = KPB_Widget._get_id();
    jQuery.magnificPopup.open({
      callbacks: {
        open: function() {
          var widget_id;
          jQuery(lightbox).show();
          widget_id = KPB_Util.get_random_id('widget-');
          KPB_Widget.prepare_form(widget_id, widget_name, widget_name, class_name, 'add');
          KPB_Widget.load_form(class_name, widget_id);
        },
        close: function() {
          KPB_Widget.close(event);
        }
      },
      modal: true,
      preloader: true,
      alignTop: true,
      items: {
        src: lightbox,
        type: 'inline'
      }
    });
  },
  edit: function(event, obj, widget_id) {
    var class_name, lightbox, widget_name, widget_title;
    event.preventDefault();
    kpb_current_widget = obj.parents('.kpb-widget');
    lightbox = KPB_Widget._get_id();
    class_name = kpb_current_widget.attr('data-class');
    widget_name = kpb_current_widget.attr('data-name');
    widget_title = kpb_current_widget.find('label').text();
    jQuery.magnificPopup.open({
      callbacks: {
        open: function() {
          jQuery(lightbox).show();
          KPB_Widget.prepare_form(widget_id, widget_name, widget_title, class_name, 'edit');
          KPB_Widget.load_form(class_name, widget_id);
        },
        close: function() {
          KPB_Widget.close(event);
        }
      },
      modal: true,
      preloader: true,
      alignTop: true,
      items: {
        src: lightbox,
        type: 'inline'
      },
      fixedBgPos: true
    });
  },
  "delete": function(event, obj, widget_id) {
    var $wrap, answer, class_name, layout_slug, post_id;
    event.preventDefault();
    answer = confirm(KPB_Config.i18n.are_you_sure_to_remove_this_widget);
    if (answer) {
      $wrap = obj.closest('.kpb-widget');
      class_name = $wrap.attr('data-class');
      layout_slug = jQuery('#kpb-select-layout option:selected').val();
      post_id = jQuery('#post_ID').val();
      jQuery.ajax({
        url: KPB_Config.ajax,
        dataType: "html",
        type: 'POST',
        async: true,
        data: {
          action: 'kpb_delete_widget',
          security: jQuery('#kpb_delete_widget_security').val(),
          widget_id: widget_id,
          post_id: post_id,
          class_name: class_name,
          layout_slug: layout_slug
        },
        success: function(data, textStatus, jqXHR) {
          $wrap.remove();
          KPB.force_save();
        },
        beforeSend: function(jqXHR) {
          KPB_Overlay.show();
        },
        complete: function(jqXHR, textStatus) {
          KPB_Overlay.hide();
        }
      });
    }
  },
  save: function(event, obj) {
    event.preventDefault();
    obj.ajaxSubmit({
      dataType: 'json',
      type: 'POST',
      async: true,
      beforeSubmit: function(arr, $form, options) {
        KPB_Overlay.show();
      },
      success: function(response, statusText, xhr, $form) {
        jQuery('#kpb-widget input[name=kpb-widget-action]').val('edit');
        if ('edit' === response.action) {
          if (!kpb_current_widget.length) {
            kpb_current_widget = jQuery('#' + response.id);
          }
          if (kpb_current_widget.length) {
            kpb_current_widget.find('label').text(response.label.kpb_escape());
          }
        } else if ('add' === response.action) {
          if (response.visual) {
            kpb_current_sidebar.find(KPB_Widget._get_placeholder_class()).append(response.visual);
          }
        }
        jQuery('#kpb-tab-widget-kpb').html(response.form.kpb_escape());
        KPB.force_save();
      },
      complete: function(jqXHR, textStatus) {
        KPB_Assets.init();
        KPB_Overlay.hide();
      }
    });
  },
  close: function(event) {
    event.preventDefault();
    jQuery.magnificPopup.close();
    jQuery('#kpb-widget .kpb-form-inner').html('<center class="kpb-loading">' + KPB_Config.i18n.loading + '</center>');
    KPB_Overlay.hide();
    jQuery('#kpb-widget-title').text('');
    jQuery('#kpb-widget input[name=kpb-widget-class-name]').val('');
    jQuery('#kpb-widget input[name=kpb-widget-action]').val('add');
    jQuery('#kpb-widget input[name=kpb-widget-id]').val('');
    jQuery('#kpb-widget input[name=kpb-widget-name]').val('');
  }
};

KPB_Row = {
  _get_id: function(layout_slug, row_slug) {
    return '#kpb-customize-lightbox-' + layout_slug + '-' + row_slug;
  },
  load: function(obj, layout_slug, row_slug) {
    jQuery.ajax({
      url: KPB_Config.ajax,
      dataType: 'html',
      type: 'POST',
      async: true,
      data: {
        action: 'kpb_load_row_customize',
        security: jQuery('#kpb_load_row_customize_security').val(),
        post_id: jQuery('#post_ID').val(),
        layout_slug: layout_slug,
        row_slug: row_slug
      },
      beforeSend: function() {
        KPB_Overlay.show();
      },
      success: function(data, textStatus, jqXHR) {
        if (data) {
          jQuery('body').append(data);
        }
      },
      complete: function(jqXHR, textStatus) {
        KPB_Overlay.hide();
        KPB_Assets.init();
        KPB_Row.show(obj, layout_slug, row_slug);
      }
    });
  },
  show: function(obj, layout_slug, row_slug) {
    var lightbox;
    lightbox = KPB_Row._get_id(layout_slug, row_slug);
    jQuery.magnificPopup.open({
      callbacks: {
        open: function() {
          jQuery(lightbox).show();
        },
        close: function() {
          jQuery(lightbox).hide();
        }
      },
      modal: true,
      preloader: true,
      alignTop: true,
      items: {
        src: lightbox,
        type: 'inline'
      }
    });
  },
  open: function(event, obj, layout_slug, row_slug) {
    var lightbox;
    event.preventDefault();
    lightbox = KPB_Row._get_id(layout_slug, row_slug);
    if (!jQuery(lightbox).length) {
      KPB_Row.load(obj, layout_slug, row_slug);
    } else {
      KPB_Row.show(obj, layout_slug, row_slug);
    }
  },
  save: function(event, obj, post_id) {
    event.preventDefault();
    obj.ajaxSubmit({
      beforeSubmit: function(arr, $form, options) {
        KPB_Overlay.show();
      },
      success: function(responseText, statusText, xhr, $form) {
        KPB_Overlay.hide();
      },
      data: {
        post_id: post_id
      }
    });
  },
  close: function(event) {
    event.preventDefault();
    KPB_Widget.close(event);
  }
};

KPB_Col = {
  _get_id: function(layout_slug, row_slug, col_slug) {
    return '#kpb-customize-lightbox-' + layout_slug + '-' + row_slug + '-' + col_slug;
  },
  edit: function(event, obj) {
    var col_slug, layout_slug, lightbox, row_slug;
    event.preventDefault();
    col_slug = obj.closest('.kpb-area').attr('data-area');
    row_slug = obj.closest('.kpb-section').attr('data-section');
    layout_slug = obj.closest('.kpb-layout ').attr('data-layout');
    lightbox = KPB_Col._get_id(layout_slug, row_slug, col_slug);
    if (jQuery(lightbox).length) {
      KPB_Col.show(layout_slug, row_slug, col_slug);
    } else {
      KPB_Col.load(layout_slug, row_slug, col_slug);
    }
  },
  load: function(layout_slug, row_slug, col_slug) {
    jQuery.ajax({
      url: KPB_Config.ajax,
      dataType: 'html',
      type: 'POST',
      async: true,
      data: {
        action: 'kpb_load_col_customize',
        security: jQuery('#kpb_load_col_customize_security').val(),
        post_id: jQuery('#post_ID').val(),
        layout_slug: layout_slug,
        row_slug: row_slug,
        col_slug: col_slug
      },
      beforeSend: function() {
        KPB_Overlay.show();
      },
      success: function(data, textStatus, jqXHR) {
        if (data) {
          jQuery('body').append(data);
        }
      },
      complete: function(jqXHR, textStatus) {
        KPB_Overlay.hide();
        KPB_Assets.init();
        KPB_Col.show(layout_slug, row_slug, col_slug);
      }
    });
  },
  show: function(layout_slug, row_slug, col_slug) {
    var lightbox;
    lightbox = KPB_Col._get_id(layout_slug, row_slug, col_slug);
    jQuery.magnificPopup.open({
      callbacks: {
        open: function() {
          jQuery(lightbox).show();
        },
        close: function() {
          jQuery(lightbox).hide();
        }
      },
      modal: true,
      preloader: true,
      alignTop: true,
      items: {
        src: lightbox,
        type: 'inline'
      }
    });
  },
  save_customize: function(event, obj) {
    event.preventDefault();
    obj.ajaxSubmit({
      beforeSubmit: function(arr, $form, options) {
        KPB_Overlay.show();
      },
      success: function(responseText, statusText, xhr, $form) {
        KPB_Overlay.hide();
      },
      data: {
        post_id: jQuery('#post_ID').val()
      }
    });
  },
  close_customize: function(event) {
    event.preventDefault();
    KPB_Lightbox.close();
  }
};

KPB_Layouts = {
  change: function(event, obj) {
    var layout_slug;
    event.preventDefault();
    layout_slug = obj.find('option:selected').val();
    if (!jQuery(KPB_Layout._get_id(layout_slug)).length) {
      KPB_Layout.load(layout_slug);
    } else {
      KPB_Layout.change(layout_slug);
    }
  }
};

KPB_Layout = {
  _get_id: function(layout_slug) {
    return '#kpb-layout-' + layout_slug;
  },
  get_btn_customize_id: function() {
    return '#kpb-button-customize-layout';
  },
  get_btn_preview_id: function() {
    return '#kpb-button-hide-preview';
  },
  load: function(layout_slug) {
    jQuery.ajax({
      url: KPB_Config.ajax,
      dataType: 'html',
      type: 'POST',
      async: true,
      data: {
        action: 'kpb_load_layout',
        security: jQuery('#kpb_load_layout_security').val(),
        post_id: jQuery('#post_ID').val(),
        layout_slug: layout_slug
      },
      beforeSend: function() {
        KPB_Overlay.show();
      },
      success: function(data, textStatus, jqXHR) {
        if (data) {
          jQuery(KPB_Wrapper._get_id()).append(data);
          KPB_Widget.get_placeholder();
        }
      },
      complete: function(jqXHR, textStatus) {
        KPB_Overlay.hide();
        KPB_Assets.init();
        KPB_Layout.change(layout_slug);
      }
    });
  },
  change: function(layout_slug) {
    var is_mark, layout_id;
    layout_id = KPB_Layout._get_id(layout_slug);
    if (!jQuery(layout_id).hasClass('kpb-active')) {
      jQuery('.kpb-layout.kpb-active').removeClass('kpb-active').addClass('kpb-hidden');
      jQuery(layout_id).removeClass('kpb-hidden').addClass('kpb-active');
      KPB_Layout.toggle_customize(layout_id);
      KPB_Layout.toggle_preview(layout_id);
    }
    is_mark = 'disable' !== layout_slug;
    KPB_Main_Form.mark_it(is_mark);
  },
  toggle_customize: function(layout_id) {
    var has_customize;
    has_customize = parseInt(jQuery(layout_id).attr('data-has-customize'));
    if (has_customize) {
      jQuery(KPB_Layout.get_btn_customize_id()).show();
    } else {
      jQuery(KPB_Layout.get_btn_customize_id()).hide();
    }
  },
  toggle_preview: function(layout_id) {
    var has_customize;
    has_customize = parseInt(jQuery(layout_id).attr('data-has-preview'));
    if (has_customize) {
      jQuery(KPB_Layout.get_btn_preview_id()).show();
    } else {
      jQuery(KPB_Layout.get_btn_preview_id()).hide();
    }
  },
  save_layout: function(event, $btn_save) {
    var data;
    event.preventDefault();
    data = KPB_Layout.grab_layout_data($btn_save);
    jQuery.ajax({
      url: KPB_Config.ajax,
      dataType: "html",
      type: 'POST',
      async: true,
      data: {
        action: 'kpb_save_layout',
        security: jQuery('#kpb_save_layout_security').val(),
        data: data,
        post_id: jQuery('#post_ID').val()
      },
      error: function(jqXHR, textStatus, errorThrown) {},
      beforeSend: function(jqXHR) {
        KPB_Overlay.show();
        $btn_save.text(KPB_Config.i18n.saving);
      },
      success: function(data, textStatus, jqXHR) {
        $btn_save.text(KPB_Config.i18n.save);
      },
      complete: function() {
        KPB_Overlay.hide();
      }
    });
  },
  grab_layout_data: function($btn_save) {
    var $layout, data, layout_slug, rows;
    layout_slug = jQuery('#kpb-select-layout option:selected').val();
    $layout = jQuery(KPB_Layout._get_id(layout_slug));
    data = {
      layout_slug: layout_slug,
      rows: []
    };
    rows = $layout.find('.kpb-section');
    if (rows.length > 0) {
      rows.each(function(s_index, s_element) {
        var cols, current_section, row_data;
        current_section = jQuery(s_element);
        row_data = {
          name: current_section.attr('data-section'),
          cols: []
        };
        cols = current_section.find('.kpb-area');
        if (cols.length > 0) {
          cols.each(function(a_index, a_element) {
            var col_data, current_area, widgets;
            current_area = jQuery(a_element);
            col_data = {
              name: current_area.attr('data-area'),
              widgets: []
            };
            widgets = current_area.find('.kpb-widget');
            if (widgets.length > 0) {
              widgets.each(function(w_index, w_element) {
                var current_widget, widget_data;
                current_widget = jQuery(w_element);
                widget_data = {
                  id: current_widget.attr('id'),
                  name: current_widget.attr('data-name'),
                  class_name: current_widget.attr('data-class')
                };
                col_data.widgets.push(widget_data);
              });
              row_data.cols.push(col_data);
            }
          });
          if (row_data.cols.length) {
            data.rows.push(row_data);
          }
        }
      });
    }
    return data;
  }
};

KPB_Layout_Customize = {
  _get_id: function(layout_slug) {
    return '#kpb-layout-customize-lightbox-' + layout_slug;
  },
  load: function(layout_slug) {
    jQuery.ajax({
      url: KPB_Config.ajax,
      dataType: 'html',
      type: 'POST',
      async: true,
      data: {
        action: 'kpb_load_layout_customize',
        security: jQuery('#kpb_load_layout_customize_security').val(),
        layout_slug: layout_slug,
        post_id: jQuery('#post_ID').val()
      },
      beforeSend: function(jqXHR) {
        KPB_Overlay.show();
      },
      success: function(data, textStatus, jqXHR) {
        if (data) {
          jQuery('body').append(data);
        }
      },
      complete: function(jqXHR, textStatus) {
        KPB_Assets.init();
        KPB_Layout_Customize.show(layout_slug);
        KPB_Overlay.hide();
      }
    });
  },
  show: function(layout_slug) {
    var lightbox;
    lightbox = KPB_Layout_Customize._get_id(layout_slug);
    if (jQuery(lightbox).length) {
      jQuery.magnificPopup.open({
        callbacks: {
          open: function() {
            jQuery(lightbox).show();
          },
          close: function() {
            jQuery(lightbox).hide();
          }
        },
        modal: true,
        preloader: true,
        alignTop: true,
        items: {
          src: lightbox,
          type: 'inline'
        }
      });
    }
  },
  open: function(event, obj) {
    var layout_slug, lightbox;
    event.preventDefault();
    layout_slug = jQuery('#kpb-select-layout option:selected').val();
    lightbox = KPB_Layout_Customize._get_id(layout_slug);
    if (!jQuery(lightbox).length) {
      KPB_Layout_Customize.load(layout_slug);
    } else {
      KPB_Layout_Customize.show(layout_slug);
    }
  },
  save: function(event, obj, post_id) {
    event.preventDefault();
    obj.ajaxSubmit({
      beforeSubmit: function(arr, $form, options) {
        KPB_Overlay.show();
      },
      success: function(responseText, statusText, xhr, $form) {
        KPB_Overlay.hide();
      },
      data: {
        post_id: post_id
      }
    });
  },
  close: function(event) {
    event.preventDefault();
    KPB_Widget.close(event);
  }
};

KPB_Util = {
  get_random_id: function(prefix) {
    return prefix + Date.now().toString(36).substr(2, 5);
  }
};

KPB_UI = {
  init: function() {
    KPB_UI_Image.init();
    KPB_UI_Color.init();
    KPB_UI_Numeric_Slider.init();
  }
};

KPB_UI_Image = {
  init: function() {
    jQuery('.kpb-ui-image-outer').on('click', '.kpb-ui-image-button-upload', function(event) {
      event.preventDefault();
      kpb_media_button_upload = jQuery(this);
      if (kpb_media) {
        kpb_media.open();
        return;
      }
      kpb_media = wp.media.frames.kpb_media = wp.media({
        title: KPB_Config.i18n.media_center,
        button: {
          text: KPB_Config.i18n.choose_image
        },
        library: {
          type: 'image'
        },
        multiple: false
      });
      kpb_media.on('select', function() {
        var attachment;
        attachment = kpb_media.state().get('selection').first().toJSON();
        kpb_media_button_upload.parents('.kpb-ui-image-outer').find('.kpb-ui-image').val(attachment.url);
        kpb_media_button_upload.parents('.kpb-ui-image-outer').find('.kpb-ui-image-preview').attr('src', attachment.url);
      });
      kpb_media.open();
    });
    jQuery('.kpb-ui-image-outer').on('click', '.kpb-ui-image-button-reset', function(event) {
      event.preventDefault();
      kpb_media_button_reset = jQuery(this);
      if (kpb_media_button_reset.attr('data-reset')) {
        kpb_media_button_reset.parents('.kpb-ui-image-outer').find('.kpb-ui-image').val(kpb_media_button_reset.attr('data-reset'));
        kpb_media_button_reset.parents('.kpb-ui-image-outer').find('.kpb-ui-image-preview').attr('src', kpb_media_button_reset.attr('data-reset'));
      } else {
        kpb_media_button_reset.parents('.kpb-ui-image-outer').find('.kpb-ui-image').val(kpb_media_button_reset.attr(''));
        kpb_media_button_reset.parents('.kpb-ui-image-outer').find('.kpb-ui-image-preview').attr('src', kpb_media_button_reset.attr('data-preview'));
      }
    });
  }
};

KPB_UI_Color = {
  init: function() {
    jQuery('.kpb-ui-color').wpColorPicker();
  }
};

KPB_UI_Numeric_Slider = {
  _get_class: function() {
    return '.kpb-ui-numeric-slider';
  },
  init: function() {
    var $sliders;
    $sliders = jQuery(KPB_UI_Numeric_Slider._get_class());
    if ($sliders.length) {
      jQuery.each($sliders, function() {
        var $filler, $input, $outer, $previewer, $slider, affix, max, min, prefix, preview, start, step, value;
        $input = jQuery(this);
        $outer = $input.closest('.kpb-ui-numeric-slider--outer');
        $slider = $outer.find('.kpb-ui-numeric-slider--control');
        $filler = $outer.find('.kpb-ui-numeric-slider--filler');
        $previewer = $outer.find('.kpb-ui-numeric-slider--preview');
        start = $input.attr('data-start');
        prefix = $input.attr('data-prefix');
        affix = $input.attr('data-affix');
        preview = $input.attr('data-preview');
        min = parseFloat($input.attr('data-min'));
        max = parseFloat($input.attr('data-max'));
        step = parseFloat($input.attr('data-step'));
        value = $input.val();
        $slider.slider({
          value: value,
          min: min,
          max: max,
          step: step,
          slide: function(event, ui) {
            var width;
            value = parseFloat(ui.value);
            width = (value / max) * 100;
            $input.val(ui.value);
            if (value > 0) {
              if ('percent' === preview) {
                $previewer.text(width.toString().substring(0, 5) + '%');
              } else {
                $previewer.text(prefix + ui.value + affix);
              }
              $filler.css('width', width + '%');
            } else {
              $previewer.text(prefix + start + affix);
              $filler.css('width', '0%');
            }
          }
        });
      });
    }
  }
};

KPB_UI_Attachment_Image = {
  edit: function(event, $button_edit) {
    var $button_remove, $control, $input, $thumb;
    event.preventDefault();
    $control = $button_edit.parent();
    $input = $control.find('.kpb-ui-attachment-image--input');
    $thumb = $control.find('.kpb-ui-attachment-image--thumb').first();
    $button_remove = $control.find('.kpb-ui-attachment-image--remove').first();
    if ($kpb_media_2nd) {
      $kpb_media_2nd.open();
    } else {
      $kpb_media_2nd = wp.media.frames.$kpb_media_2nd = wp.media({
        title: KPB_Config.i18n.media_center,
        button: {
          text: KPB_Config.i18n.choose_image
        },
        library: {
          type: 'image'
        },
        multiple: false
      });
      $kpb_media_2nd.on('open', function() {
        var $attachment, $selection, $thumb_id;
        $thumb_id = parseInt($input.val(), 10);
        if ($thumb_id) {
          $selection = $kpb_media_2nd.state().get('selection');
          $attachment = wp.media.attachment($thumb_id);
          $attachment.fetch();
          $selection.add($attachment);
        }
      });
      $kpb_media_2nd.on('select', function() {
        var $attachment;
        $attachment = $kpb_media_2nd.state().get('selection').first().toJSON();
        $input.val($attachment.id);
        $thumb.attr('src', $attachment.sizes.thumbnail.url);
        $thumb.removeClass('kpb--is_hidden');
        $button_edit.hide();
        $button_remove.show();
        $kpb_media_2nd = false;
      });
      $kpb_media_2nd.open();
    }
  },
  remove: function(event, $button_remove) {
    var $button_edit, $control, $input, $thumb;
    event.preventDefault();
    $control = $button_remove.parent();
    $input = $control.find('.kpb-ui-attachment-image--input');
    $thumb = $control.find('.kpb-ui-attachment-image--thumb').first();
    $button_edit = $control.find('.kpb-ui-attachment-image--edit').first();
    $input.val(0);
    $thumb.attr('src', '');
    $thumb.addClass('kpb--is_hidden');
    $button_edit.show();
    $button_remove.hide();
  }
};

KPB_Lightbox = {
  close: function() {
    jQuery.magnificPopup.close();
  }
};

KPB_Tips = {
  hide_screenshot: function(event, obj) {
    event.preventDefault();
    if (obj.attr('data-status') === '0') {
      jQuery('.kpb-layout > .kpb-row > .kpb-col-left').removeClass('kpb-col-12').addClass('kpb-col-8');
      jQuery('.kpb-layout > .kpb-row > .kpb-col-right').show();
      obj.attr('data-status', '1');
      obj.text(KPB_Config.i18n.hide_preview);
    } else {
      jQuery('.kpb-layout > .kpb-row > .kpb-col-left').removeClass('kpb-col-8').addClass('kpb-col-12');
      jQuery('.kpb-layout > .kpb-row > .kpb-col-right').hide();
      obj.attr('data-status', '0');
      obj.text(KPB_Config.i18n.show_preview);
    }
  }
};

String.prototype.kpb_escape = function() {
  return this.replace(/\\n/g, '\\n').replace(/\\'/g, '\\\'').replace(/\\"/g, '\"').replace(/\\&/g, '\\&').replace(/\\r/g, '\\r').replace(/\\t/g, '\\t').replace(/\\b/g, '\\b').replace(/\\f/g, '\\f');
};
