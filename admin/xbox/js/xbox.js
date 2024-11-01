window.XBOX = (function(window, document, $){
  'use strict';
  var xbox = {
    duplicate: false,
    media: {
      frames : {
      }
    }
  };

  xbox.init = function() {
    var $xbox = $('.xbox');
    var $form = $xbox.closest('.xbox-form');
    if( ! $form.length ){
      $form = $xbox.closest('form#post');
    }

    //Disable form submit on enter
    $form.on('keyup keypress', 'input[type="text"], input[type="number"], input[type="password"]', function(e){
      var keyCode = e.which;
      if( keyCode === 13 ){
        e.preventDefault();
        return false;
      }
    });

    $(window).resize(function () {
      if( viewport().width <= 850 ){
        $('#post-body').addClass('xbox-columns-1');
      } else {
        $('#post-body').removeClass('xbox-columns-1');
      }
    }).resize();


    xbox.init_image_selector();
    xbox.init_tab();
    xbox.init_switcher();
    xbox.init_spinner();
    xbox.init_checkbox();
    xbox.init_sortable_preview_items();
    xbox.init_sortable_checkbox();
    xbox.init_sortable_repeatable_items();
    xbox.init_tooltip();

    setTimeout(function(){
      xbox.load_icons_for_icon_selector();
    }, 200);

    $xbox.on('click', '#xbox-reset', xbox.on_click_reset_values );

    $xbox.on('click', '.xbox-add-repeatable-item', xbox.add_repeatable_item );
    $xbox.on('click', '.xbox-remove-repeatable-item', xbox.remove_repeatable_item );
    $xbox.on('sort_repeatable_items', '.xbox-repeatable-wrap', xbox.sort_repeatable_items );

    $xbox.on('click', '.xbox-remove-preview', xbox.remove_preview_item );
    $xbox.on('click', '.xbox-get-image', xbox.get_image_from_url );
    $xbox.on('click', '.xbox-type-number .xbox-unit-has-picker-1', xbox.toggle_units_dropdown );
    $xbox.on('click', '.xbox-units-dropdown .xbox-unit-item', xbox.set_unit_number );
    $xbox.on('focus', '.xbox-type-text input.xbox-element', xbox.on_focus_input_type_text );

    $(document).on('click', xbox.hide_units_dropdown );

    xbox.sticky_submit_buttons();
    $( window ).scroll(function(){
      xbox.sticky_submit_buttons();
    });
  };

  xbox.sticky_submit_buttons = function(){
    var $header = $('.xbox-header').first();
    var $actions = $header.find('.xbox-header-actions').first();
    var $my_account = $('#wp-admin-bar-my-account');
    if( ! $actions.length ||  ! $my_account.length || ! $actions.data('sticky') ){
      return;
    }
    if( $(window).scrollTop() > $header.offset().top ){
      $my_account.css('padding-right', $actions.width() + 25);
      $actions.addClass('xbox-actions-sticky');
    } else {
      $my_account.css('padding-right', '');
      $actions.removeClass('xbox-actions-sticky');
    }
  };

  xbox.on_focus_input_type_text = function( event ){
    var $helper = $(this).next('.xbox-field-helper');
    if( $helper.length ){
      $(this).css('padding-right', ($helper.outerWidth() + 6) + 'px' );
    }
  };

  xbox.hide_units_dropdown = function(){
    $('.xbox-units-dropdown').slideUp(200);
  };
  xbox.toggle_units_dropdown = function( event ){
    if( $(event.target).hasClass('xbox-spinner-handler') || $(event.target).hasClass('xbox-spinner-control') ){
      return;
    }
    event.stopPropagation();
    $(this).find('.xbox-units-dropdown').slideToggle(200);
  };
  xbox.set_unit_number = function( event ){
    var $btn = $(this);
    $btn.closest('.xbox-unit').find('input.xbox-unit-number').val($btn.data('value')).trigger('change');
    $btn.closest('.xbox-unit').find('span').text($btn.text());
  };

  xbox.load_icons_for_icon_selector = function( event ){
    var fields = [];
    $('.xbox-type-icon_selector').each(function(index, el) {
      var field_id = $(el).data('field-id');
      var options = $(el).find('.xbox-icons-wrap').data('options');
      if( $.inArray( field_id, fields ) < 0 && options.load_with_ajax ){
        fields.push(field_id);
      }
    });

    $.each(fields, function(index, field_id) {
      xbox.load_icon_selector( $('.xbox-field-id-' + field_id) );
    });

    $(document).on('input', '.xbox-search-icon', function(event) {
      event.preventDefault();
      var value = $(this).val();
      var $container = $(this).closest('.xbox-field').find('.xbox-icons-wrap');
      xbox.filter_items(value, $container, '.xbox-item-icon-selector');
    });
    $(document).on('click', '.xbox-icon-actions .xbox-btn', function(event) {
      var value = $(this).data('search');
      var $container = $(this).closest('.xbox-field').find('.xbox-icons-wrap');
      xbox.filter_items(value, $container, '.xbox-item-icon-selector');
    });

    $(document).on('click', '.xbox-icons-wrap .xbox-item-icon-selector', function(event) {
      var $field = $(this).closest('.xbox-field');
      var $container = $field.find('.xbox-icons-wrap');
      var options = $container.data('options');
      $(this).addClass(options.active_class).siblings().removeClass(options.active_class);
      $field.find('input.xbox-element').val($(this).data('value')).trigger('change');
      $field.find('.xbox-icon-active').html($(this).html());
    });
  };

  xbox.filter_items = function( value, $container, selector ){
    $container.find(selector).each(function(index, item) {
      var data = $(item).data('key');
      if( is_empty( data ) ){
        $(item).hide();
      } else {
        if( value == 'all' || data.indexOf(value) > -1 ){
          $(item).show();
        } else{
          $(item).hide();
        }
      }
    });
  };

  xbox.load_icon_selector = function( $field ){
    var options = $field.find('.xbox-icons-wrap').data('options');
    $.ajax({
      type : 'post',
      dataType : 'json',
      url : XBOX_JS.ajax_url,
      data : {
        action: 'xbox_get_items',
        class_name : options.ajax_data.class_name,
        function_name : options.ajax_data.function_name,
        ajax_nonce : XBOX_JS.ajax_nonce
      },
      beforeSend: function(){
        $field.find('.xbox-icons-wrap').prepend("<i class='xbox-icon xbox-icon-spinner xbox-icon-spin xbox-loader'></i>");
      },
      success: function( response ) {
        if( response ){
          if( response.success ){
            $.each(response.items, function(value, html) {
              var key = 'font ' + value;
              var type = 'icon font';
              if( key.indexOf('.svg') > -1 ){
                key = key.split('/');
                key = key[key.length - 1];
                type = 'svg';
              }
              var $new_item  = $('<div />', {
                'class': 'xbox-item-icon-selector',
                'data-value': value,
                'data-key': key,
                'data-type': type
              });
              $new_item.html(html);
              $field.find('.xbox-icons-wrap').append($new_item);
            });
            $field.find('.xbox-icons-wrap .xbox-item-icon-selector').css({
              'width': options.size,
              'height': options.size,
              'font-size': parseInt( options.size ) - 14,
            });
            //c($field.first().find('.xbox-icons-wrap .xbox-item-icon-selector').length);//total icons
          }
        }
      },
      error: function( jqXHR, textStatus, errorThrown ){
      },
      complete: function( jqXHR, textStatus ){
        $field.find('.xbox-icons-wrap').find('.xbox-loader').remove();
      }
    });

    return '';
  };


  xbox.on_click_reset_values = function( event ){
    var $btn = $(this);
    var $xbox_form = $btn.closest('.xbox-form');
    $.xboxConfirm({
      title: XBOX_JS.text.reset_popup.title,
      content: XBOX_JS.text.reset_popup.content,
      confirm_class: 'xbox-btn-blue',
      confirm_text: XBOX_JS.text.popup.accept_button,
      cancel_text: XBOX_JS.text.popup.cancel_button,
      onConfirm: function(){
        $xbox_form.prepend('<input type="hidden" name="' + $btn.attr('name') + '" value="true">');
        $xbox_form.submit();
      },
      onCancel: function(){
        return false;
      }
    });
    return false;
  };

  xbox.get_image_from_url = function( event ){
    var $btn = $(this);
    var $field = $btn.closest('.xbox-field');
    var $input = $field.find('.xbox-element-text');
    var $wrap_preview = $field.find('.xbox-wrap-preview');
    if( is_empty( $input.val() ) ){
      $.xboxConfirm({
        title: XBOX_JS.text.validation_url_popup.title,
        content: XBOX_JS.text.validation_url_popup.content,
        confirm_text: XBOX_JS.text.popup.accept_button,
        hide_cancel: true
      });
      return false;
    }
    var image_class = $wrap_preview.data('image-class');
    var $new_item  = $('<li />', { 'class': 'xbox-preview-item xbox-preview-image'} );
    $new_item.html(
      '<img src="'+$input.val()+'" class="'+image_class+'">' +
      '<a class="xbox-btn xbox-btn-iconize xbox-btn-small xbox-btn-red xbox-remove-preview"><i class="xbox-icon xbox-icon-times-circle"></i></a>'
    );
    $wrap_preview.fadeOut(400, function(){
      $(this).html('').show();
    });
    $field.find('.xbox-get-image i').addClass('xbox-icon-spin');
    setTimeout(function(){
      $wrap_preview.html( $new_item );
      $field.find('.xbox-get-image i').removeClass('xbox-icon-spin');
    }, 1200);
    return false;
  };

  xbox.remove_preview_item = function( event ){
    var $btn = $(this);
    var $field = $btn.closest('.xbox-field');
    var field_id = $field.closest('.xbox-row').data('field-id');
    var control_data_img = $field.closest('.xbox-type-group').find('.xbox-group-control').data('image-field-id');
    var $wrap_preview = $field.find('.xbox-wrap-preview');
    var multiple = $field.hasClass('xbox-has-multiple');

    $field.trigger( 'xbox_before_remove_preview_item', [ multiple ] );

    if( ! multiple ){
      $field.find('.xbox-element').attr('value', '');
    }
    $btn.closest('.xbox-preview-item').remove();

    if( ! multiple && $btn.closest('.xbox-preview-item').hasClass('xbox-preview-image' ) ){
      if( field_id == control_data_img ){
        xbox.synchronize_selector_preview_image( '.xbox-control-image', $wrap_preview, 'remove', '' );
      }
      xbox.synchronize_selector_preview_image( '', $wrap_preview, 'remove', '' );
    }
    $field.find('.xbox-element').trigger('change');
    $field.trigger( 'xbox_after_remove_preview_item', [ multiple ] );
    return false;
  };

  xbox.synchronize_selector_preview_image = function( selectors, $wrap_preview, action, value ){
    selectors = selectors || $wrap_preview.data('synchronize-selector');
    if( ! is_empty( selectors ) ){
      selectors = selectors.split(',');
      $.each( selectors, function(index, selector) {
        var $element = $(selector);
        if( $element.closest('.xbox-type-group').length ){
          if( $element.closest('.xbox-group-control').length ){
            $element = $element.closest('.xbox-group-control-item.xbox-active').find(selector);
          } else {
            $element = $element.closest('.xbox-group-item.xbox-active').find(selector);
          }
        }
        if( $element.is('img') ){
          $element.fadeOut(300, function() {
            if( $element.closest('.xbox-group-control').length ){
              $element.attr('src', value);
            } else {
              $element.attr('src', value);
            }
          });
        } else {
          $element.fadeOut(300, function() {
            if( $element.closest('.xbox-group-control').length ){
              $element.css('background-image', 'url('+ value +')');
            } else {
              $element.css('background-image', 'url('+ value +')');
            }
          });
        }
        if( action == 'add' ){
          $element.fadeIn(300);
        }
        var $input = $element.closest('.xbox-field').find('input.xbox-element');
        if( $input.length ){
          $input.attr( 'value', value );
        }

        var $close_btn = $element.closest('.xbox-preview-item').find('.xbox-remove-preview');
        if( $close_btn.length ){
          if( action == 'add' && $input.is(':visible') ){
            $close_btn.show();
          }
          if( action == 'remove' ){
            $close_btn.hide();
          }
        }
      });
    }
  };

  xbox.reinit_js_plugins = function( $new_element ){
    //Inicializar Tabs
    $new_element.find('.xbox-tab').each( function( iterator, item ) {
      xbox.init_tab( $(item) );
    });

    //Inicializar Switcher
    $new_element.find('.xbox-type-switcher input.xbox-element').each( function( iterator, item ) {
      $(item).xboxSwitcher('destroy');
      xbox.init_switcher( $(item) );
    });

    //Inicializar Spinner
    $new_element.find('.xbox-type-number .xbox-field.xbox-has-spinner').each( function( iterator, item ) {
      xbox.init_spinner( $(item) );
    });

    //Inicializar radio buttons y checkboxes
    $new_element.find('.xbox-has-icheck .xbox-radiochecks.init-icheck').each( function( iterator, item ) {
      xbox.destroy_icheck( $(item) );
      xbox.init_checkbox( $(item) );
    });

    //Inicializar Sortable de items repetibles
    $new_element.find('.xbox-repeatable-wrap.xbox-sortable').each( function( iterator, item ) {
      xbox.init_sortable_repeatable_items( $(item) );
    });

    //Inicializar Sortable de preview items
    $new_element.find('.xbox-wrap-preview-multiple').each( function( iterator, item ) {
      xbox.init_sortable_preview_items( $(item) );
    });

    //Inicializar Tooltip
    xbox.init_tooltip( $new_element.find('.xbox-tooltip-handler') );
  };

  xbox.init_switcher = function( $selector ){
    $selector = is_empty( $selector ) ? $('.xbox-type-switcher input.xbox-element') : $selector;
    $selector.xboxSwitcher();
  };

  xbox.init_spinner = function( $selector ){
    $selector = is_empty( $selector ) ? $('.xbox-type-number .xbox-field.xbox-has-spinner') : $selector;
    $selector.spinner('delay', 300);
    $selector.spinner('changing', function( e, newVal, oldVal ) {
      $(this).trigger('xbox_changed_value', newVal );
    });
  };

  xbox.init_tab = function( $selector ){
    $selector = is_empty( $selector ) ? $('.xbox-tab') : $selector;
    $selector.find('.xbox-tab-nav .xbox-item').removeClass('active');
    $selector.find('.xbox-accordion-title').removeClass('active');
    var type_tab = 'responsive';
    if( $selector.closest('#side-sortables').length ){
      type_tab = 'accordion';
    }
    $selector.xboxTabs({
      collapsible : true,
      type : type_tab
    });

  };

  xbox.init_tooltip = function( $selector ){
    $selector = is_empty( $selector ) ? $('.xbox-tooltip-handler') : $selector;
    $selector.each(function(index, el) {
      var title_content = '';
      var title_tooltip = $(el).data('tipso-title');
      var position = $(el).data('tipso-position') ? $(el).data('tipso-position') : 'top';
      if( ! is_empty( title_tooltip ) ){
        title_content = '<h3>' + title_tooltip +'</h3>';
      }
      $(el).tipso({
        delay: 10,
        speed: 100,
        offsetY: 2,
        tooltipHover: true,
        position: position,
        titleContent: title_content,
        onBeforeShow: function($element, element, e){
          $(e.tipso_bubble).addClass( $(el).closest('.xbox').data('skin') );
        },
        onShow: function($element, element, e){
          //$(e.tipso_bubble).removeClass('top').addClass(position);
        },
        //hideDelay: 1000000
      });
    });
  };

  xbox.init_checkbox = function( $selector ){
    $selector = is_empty( $selector ) ? $('.xbox-has-icheck .xbox-radiochecks.init-icheck') : $selector;
    $selector.find('input').iCheck({
      radioClass: 'iradio_flat-blue',
    });
  };

  xbox.destroy_icheck = function( $selector ){
    $selector.find('input').each(function(index, input) {
      $(input).attr('style', '');
      $(input).next('ins').remove();
      $(input).unwrap();
    });
  };

  xbox.init_image_selector = function( $selector ){
    $selector = is_empty( $selector ) ? $('.xbox-type-image_selector .init-image-selector') : $selector;
    $selector.xboxImageSelector({
      active_class: 'xbox-active'
    });
  };

  xbox.init_sortable_preview_items = function( $selector ){
    $selector = is_empty( $selector ) ? $('.xbox-wrap-preview-multiple') : $selector;
    $selector.sortable({
      items: '.xbox-preview-item',
      placeholder: "xbox-preview-item xbox-sortable-placeholder",
      start: function(event, ui) {
        ui.placeholder.css({
          'width': ui.item.css('width'),
          'height': ui.item.css('height'),
        });
      },
    }).disableSelection();
  };

  xbox.init_sortable_checkbox = function( $selector ){
    $selector = is_empty( $selector ) ? $('.xbox-has-icheck .xbox-radiochecks.init-icheck.xbox-sortable') : $selector;
    $selector.sortable({
      items: '>label',
      placeholder: "xbox-icheck-sortable-item xbox-sortable-placeholder",
      start: function(event, ui) {
        ui.placeholder.css({
          'width': ui.item.css('width'),
          'height': ui.item.css('height'),
        });
      },
    }).disableSelection();
  };

  xbox.init_sortable_repeatable_items = function( $selector ){
    $selector = is_empty( $selector ) ? $('.xbox-repeatable-wrap.xbox-sortable') : $selector;
    $selector.sortable({
      handle: '.xbox-sort-item',
      items: '.xbox-repeatable-item',
      placeholder: "xbox-repeatable-item xbox-sortable-placeholder",
      start: function(event, ui) {
        ui.placeholder.css({
          'width': ui.item.css('width'),
          'height': ui.item.css('height'),
        });
      },
      update: function(event, ui) {
        // No funciona bien con wp_editor, mejor usamos 'stop'
        // var $repeatable_wrap = $(event.target);
        // $repeatable_wrap.trigger('sort_repeatable_items');
      },
      stop: function(event, ui){
        var $repeatable_wrap = $(event.target);
        $repeatable_wrap.trigger('sort_repeatable_items');
      }
    }).disableSelection();
  };

  xbox.add_repeatable_item = function( event ){
    var $btn = $(this);
    var $repeatable_wrap = $btn.closest('.xbox-repeatable-wrap');
    $repeatable_wrap.trigger( 'xbox_before_add_repeatable_item' );

    var $source_item = $btn.prev('.xbox-repeatable-item');
    var index  = parseInt( $source_item.data('index') );
    var $cloned = $source_item.clone();
    var $new_item = $('<div />', { 'class': $cloned.attr('class'), 'data-index': index + 1, 'style': 'display: none' });

    xbox.set_changed_values( $cloned, $repeatable_wrap.closest('.xbox-row').data('field-type') );

    $new_item.html( $cloned.html() );
    $source_item.after( $new_item );
    $new_item.slideDown(150, function(){
      //Ordenar y cambiar ids y names
      $repeatable_wrap.trigger('sort_repeatable_items');
      //Actualizar eventos
      xbox.reinit_js_plugins( $new_item );
    });
    $repeatable_wrap.trigger( 'xbox_after_add_repeatable_item' );
    return false;
  };

  xbox.remove_repeatable_item = function( event ){
    var $repeatable_wrap = $(this).closest('.xbox-repeatable-wrap');
    if ( $repeatable_wrap.find('.xbox-repeatable-item').length > 1 ) {
      $repeatable_wrap.trigger( 'xbox_before_remove_repeatable_item' );
      var $item = $(this).closest('.xbox-repeatable-item');
      $item.slideUp(150, function() {
        $item.remove();
        $repeatable_wrap.trigger('sort_repeatable_items');
        $repeatable_wrap.trigger( 'xbox_after_remove_repeatable_item' );
      });
    }
    return false;
  };

  xbox.sort_repeatable_items = function( event ){
    var $repeatable_wrap = $(event.target);
    var row_level = parseInt( $repeatable_wrap.closest('[class*="xbox-row"]').data('row-level') );

    $repeatable_wrap.find('.xbox-repeatable-item' ).each( function( index, item ) {
      xbox.update_attributes( $(item), index, row_level );

      xbox.update_fields_on_item_active( $(item) );
    });
  };

  xbox.set_changed_values = function( $new_item, field_type ){
    var $textarea, $input;
    $new_item.find('.xbox-field').each( function( iterator, item ) {
      var type =  field_type || $(item).closest('.xbox-row').data('field-type');
      switch(type){
        case 'text':
        case 'number':
        case 'image':
          $input = $(item).find('input.xbox-element');
          $input.attr('value', $input.val() );
          break;
      }
    });
  };

  xbox.update_fields_on_item_active = function( $group_item ){
  };

  xbox.sort_group_control_items = function( event ){
    var $group_control = $(event.target);
    var row_level = parseInt( $group_control.closest('.xbox-row').data('row-level') );
    $group_control.children('.xbox-group-control-item').each( function( index, item ) {
      xbox.update_group_control_item( $(item), index, row_level );
    });
  };

  xbox.sort_group_items = function( event, start_index, end_index ){

  };

  xbox.update_group_control_item = function( $item, index, row_level ){
    $item.data('index', index).attr('data-index', index);
    $item.find('.xbox-info-order-item').text( '#'+(index+1) );
    var value;
    if( $item.find('.xbox-inner input').length ){
      value = $item.find('.xbox-inner input').val();
      $item.find('.xbox-inner input').val( value.replace( /(#\d+)/g, '#' + (index+1) ) );
    }

    //Cambiar names
    $item.find('*[name]' ).each( function( i, item ) {
      xbox.update_name_ttribute( $(item), index, row_level );
    });
  };

  xbox.update_attributes = function( $new_item, index, row_level ){
    $new_item.data('index', index).attr('data-index', index);

    $new_item.find('*[name]' ).each( function( i, item ) {
      xbox.update_name_ttribute( $(item), index, row_level );
    });

    $new_item.find('*[id]' ).each( function( i, item ) {
      xbox.update_id_attribute( $(item), index, row_level );
    });

    $new_item.find('label[for]' ).each( function( i, item ) {
      xbox.update_for_attribute( $(item), index, row_level );
    });

    $new_item.find('*[data-field-name]' ).each( function( i, item ) {
      xbox.update_data_name_attribute( $(item), index, row_level );
    });

    $new_item.find('*[data-editor]' ).each( function( i, item ) {
      xbox.update_data_editor_attribute( $(item), index, row_level );
    });

    $new_item.find('*[data-wp-editor-id]' ).each( function( i, item ) {
      xbox.update_data_wp_editor_id_attribute( $(item), index, row_level );
    });

    xbox.set_checked_inputs( $new_item, row_level );
  };

  xbox.set_checked_inputs = function( $group_item, row_level ){
    $group_item.find('.xbox-field').each( function( iterator, item ) {
      if( $(item).hasClass('xbox-has-icheck') || $(item).closest('.xbox-type-image_selector').length ) {
        var $input = $(item).find('input[type="radio"], input[type="checkbox"]');
        $input.each(function(i, input) {
          if( $(input).parent('div').hasClass('checked') ){
            $(input).attr('checked', 'checked').prop('checked', true);
          } else {
            $(input).removeAttr('checked').prop('checked', false);
          }
          if( $(input).next('img').hasClass('xbox-active') ){
            $(input).attr('checked', 'checked').prop('checked', true);
          }
        });
      }
    });
  };

  xbox.update_name_ttribute = function( $el, index, row_level ){
    var old_name = $el.attr('name');
    var new_name = '';
    if ( typeof old_name !== 'undefined' ) {
      new_name = xbox.nice_replace(/(\[\d+\])/g, old_name, '[' + index + ']', row_level);
      $el.attr( 'name', new_name );
    }
  };

  xbox.update_id_attribute = function( $el, index, row_level ){
    var old_id = $el.attr('id');
    var new_id = '';
    if ( typeof old_id !== 'undefined' ) {
      new_id = xbox.nice_replace(/(__\d+__)/g, old_id, '__' + index + '__', row_level);
      $el.attr( 'id', new_id );
    }
  };

  xbox.update_for_attribute = function( $el, index, row_level ){
    var old_for = $el.attr('for');
    var new_for = '';
    if ( typeof old_for !== 'undefined' ) {
      new_for = xbox.nice_replace(/(__\d+__)/g, old_for, '__' + index + '__', row_level);
      $el.attr( 'for', new_for );
    }
  };
  xbox.update_data_name_attribute = function( $el, index, row_level ){
    var old_data = $el.attr('data-field-name');
    var new_data = '';
    if ( typeof old_data !== 'undefined' ) {
      new_data = xbox.nice_replace(/(\[\d+\])/g, old_data, '[' + index + ']', row_level);
      $el.attr( 'data-field-name', new_data );
    }
  };

  xbox.update_data_editor_attribute = function( $el, index, row_level ){
    var old_data = $el.attr('data-editor');
    var new_data = '';
    if ( typeof old_data !== 'undefined' ) {
      new_data = xbox.nice_replace(/(__\d+__)/g, old_data, '__' + index + '__', row_level);
      $el.attr( 'data-editor', new_data );
    }
  };
  xbox.update_data_wp_editor_id_attribute = function( $el, index, row_level ){
    var old_data = $el.attr('data-wp-editor-id');
    var new_data = '';
    if ( typeof old_data !== 'undefined' ) {
      new_data = xbox.nice_replace(/(__\d+__)/g, old_data, '__' + index + '__', row_level);
      $el.attr( 'data-wp-editor-id', new_data );
    }
  };

  xbox.set_default_values = function( $group ){
    $group.find('*[data-default]').each( function( iterator, item ) {
      var $field = $(item);
      var default_value = $field.data('default');
      if( $field.closest('.xbox-type-number').length ){
        var unit = $field.find('.xbox-unit').data('unit');
        xbox.set_field_value($field, default_value, unit );
      } else {
        xbox.set_field_value($field, default_value );
      }
    });
  };

  xbox.set_field_value = function( $field, value, extra_value, update_data_value ){
    var $input, array;
    var type = $field.closest('.xbox-row').data('field-type');
    var field_id = $field.closest('.xbox-row').data('field-id');
    value = is_empty( value ) ? '' : value;
    update_data_value = update_data_value || false;
    switch(type){
      case 'number':
        if( value == $field.find('input.xbox-element').val() ){
          return;
        }
        $field.find('input.xbox-element').attr('value', value);
        var unit = extra_value;
        if( extra_value === undefined ){
          unit = $field.find('.xbox-unit').data('unit');
        }
        $field.find('input.xbox-unit-number').attr('value', unit).trigger('change');
        unit = unit === '' ? '#'  : unit;
        $field.find('.xbox-unit span').text(unit);
        if( update_data_value ){
          $field.find('input.xbox-element').attr('data-value', value);
        }
        break;

      case 'text':
      case 'hidden':
      case 'colorpicker':
        if( value == $field.find('input.xbox-element').val() ){
          return;
        }
        $field.find('input.xbox-element').attr('value', value).trigger('change').trigger('input');
        if( type == 'colorpicker' ){
          $field.find('.xbox-colorpicker-color').attr('value', value).css('background-color', value);
        }
        if( update_data_value ){
          $field.find('input.xbox-element').attr('data-value', value);
        }
        break;

      case 'image':
        $field.find('input.xbox-element').attr('value', value);
        $field.find('img.xbox-element-image').attr('src', value);
        if( is_empty( value ) ){
          $field.find('img.xbox-element-image').hide().next('.xbox-remove-preview').hide();
        }
        break;

      case 'switcher':
        $input = $field.find('input');
        if( $input.val() !== value ){
          if( $input.next().hasClass( 'xbox-sw-on' ) ) {
            $input.xboxSwitcher( 'set_off' );
          } else {
            $input.xboxSwitcher( 'set_on' );
          }
        }
        if( update_data_value ){
          $field.find('input.xbox-element').attr('data-value', value);
        }
        break;

      case 'icon_selector':
        $field.find('input.xbox-element').attr('value', value).trigger('change');
        var html = '';
        if( value.indexOf('.svg') > -1 ){
          html = '<img src="'+value+'">';
        } else {
          html = '<i class="'+value+'"></i>';
        }
        $field.find('.xbox-icon-active').html(html);
        break;

      case 'image_selector':
        value = value.toString().toLowerCase();
        $input = $field.find('input');

        if( ! $input.closest('.xbox-image-selector').data('image-selector').like_checkbox ){
          if( is_empty( $input.filter(':checked').val() ) ){
            return;
          }
          if( $input.filter(':checked').val().toLowerCase() != value ){
            $input.filter(function(i){
              return $(this).val().toLowerCase() == value;
            }).trigger('click.img_selector');
          }
        } else {
          if( get_value_checkbox( $input, ',' ).toLowerCase() != value ){
            $input.first().trigger('img_selector_disable_all');
            array = value.replace(/ /g,'').split(',');
            $.each( array, function(index){
              $input.filter(function(i){
                return $(this).val().toLowerCase() == array[index];
              }).trigger('click.img_selector');
            });
          }
        }
        break;

      case 'radio':
        value = value.toString().toLowerCase();
        if( $field.hasClass('xbox-has-icheck') && $field.find('.init-icheck').length ) {
          $input = $field.find('input');
          if( type == 'radio' ){
            if( is_empty( $input.filter(':checked').val() ) ){
              return;
            }
            $input.iCheck('uncheck');
            //if( $input.filter(':checked').val().toLowerCase() != value ){
              $input.filter(function(i){
                return $(this).val().toLowerCase() == value;
              }).iCheck('check');
            //}
          } else if( type == 'checkbox' ) {
            if( get_value_checkbox( $input, ',' ).toLowerCase() != value ){
              $input.iCheck('uncheck');
              array = value.replace(/ /g,'').split(',');
              $.each( array, function(index){
                $input.filter(function(i){
                  return $(this).val().toLowerCase() == array[index];
                }).iCheck('check');
              });
            }
          }
        }
        break;
    }
  };

  xbox.nice_replace = function( regex, string, replace_with, row_level, offset ){
    offset = offset || 0;
    //http://stackoverflow.com/questions/10584748/find-and-replace-nth-occurrence-of-bracketed-expression-in-string
    var n = 0;
    string = string.replace(regex, function (match, i, original) {
      n++;
      return (n === row_level + offset) ? replace_with : match;
    });
    return string;
  };

  xbox.get_object_id = function(){
    return $('.xbox').data('object-id');
  };

  xbox.get_object_type = function(){
    return $('.xbox').data('object-type');
  };

  xbox.get_group_object_values = function( $group_item ){
    var values = $group_item.find('input[name],select[name],textarea[name]').serializeArray();
    return values;
  };

  xbox.get_group_values = function( $group_item ){
    var object_values = xbox.get_group_object_values( $group_item );
    var values = {};
    $.each(object_values, function(index, field) {
      values[field.name] = field.value;
    });
    return values;
  };

  xbox.compare_values_by_operator = function( value1, operator, value2 ){
    switch( operator ) {
      case '<':
        return value1 < value2;
      case '<=':
        return value1 <= value2;
      case '>':
        return value1 > value2;
      case '>=':
        return value1 >= value2;
      case '==':
      case '=':
        return value1 == value2;
      case '!=':
        return value1 != value2;
      default:
        return false;
    }
    return false;
  };

  xbox.add_style_attribute = function( $element, new_style ) {
    var old_style = $element.attr('style') || '';
    $element.attr('style', old_style + '; ' + new_style);
  };

  xbox.is_image_file = function( value ){
    value = $.trim(value.toString());
    return(value.match(/\.(jpeg|jpg|gif|png)$/) !== null);
  };


  //Funciones privadas
  function is_empty( value ){
    return ( value === undefined || value === false || $.trim(value).length === 0 );
  }

  function get_class_starts_with($elment, starts_with){
    return $.grep($elment.attr('class').split(" "), function(v, i){
      return v.indexOf(starts_with) === 0;
    }).join();
  }

  function get_value_checkbox( $elment, separator ){
    separator = separator || ',';
    if( $elment.attr('type') != 'checkbox' ){
      return '';
    }
    var value = $elment.filter(':checked').map(function() {
      return this.value;
    }).get().join(separator);
    return value;
  }

  function viewport() {
    var e = window, a = 'inner';
    if (!('innerWidth' in window )) {
        a = 'client';
        e = document.documentElement || document.body;
    }
    return { width : e[ a+'Width' ] , height : e[ a+'Height' ] };
  }


  //Debug
  function c(msg){
    console.log(msg);
  }

  //Document Ready
  $(function() {
    xbox.init();
  });

  return xbox;

})(window, document, jQuery);


/**
 * jQuery alterClass plugin
 *
 * Remove element classes with wildcard matching. Optionally add classes:
 *   $( '#foo' ).alterClass( 'foo-* bar-*', 'foobar' )
 *
 * Copyright (c) 2011 Pete Boere (the-echoplex.net)
 * Free under terms of the MIT license: http://www.opensource.org/licenses/mit-license.php
 *
 */
(function ( $ ) {
$.fn.alterClass = function ( removals, additions ) {
  var self = this;
  if ( removals.indexOf( '*' ) === -1 ) {
    // Use native jQuery methods if there is no wildcard matching
    self.removeClass( removals );
    return !additions ? self : self.addClass( additions );
  }
  var patt = new RegExp( '\\s' +
      removals.
        replace( /\*/g, '[A-Za-z0-9-_]+' ).
        split( ' ' ).
        join( '\\s|\\s' ) +
      '\\s', 'g' );
  self.each( function ( i, it ) {
    var cn = ' ' + it.className + ' ';
    while ( patt.test( cn ) ) {
      cn = cn.replace( patt, ' ' );
    }
    it.className = $.trim( cn );
  });
  return !additions ? self : self.addClass( additions );
};
})( jQuery );

