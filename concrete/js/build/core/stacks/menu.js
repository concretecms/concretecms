/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global ConcreteMenu */

;(function(global, $) {
  'use strict';

  function ConcreteStackMenu($element, options) {
    var my = this;

    options = $.extend({
    }, options || {});

    ConcreteMenu.call(my, $element, options);
  }

  ConcreteStackMenu.prototype = Object.create(ConcreteMenu.prototype);

  ConcreteStackMenu.prototype.setupMenuOptions = function($menu) {
    var my = this,
      parent = ConcreteMenu.prototype,
      cID = my.$element.closest('tr').data('collection-id')
    ;
    parent.setupMenuOptions($menu);

    var $rename = $menu.find('a[data-action=rename]');
    $rename.attr('href', $rename.data('href-template').replace('__folderID__', cID));

    $menu.find('a[data-action=delete]').on('click', function() {
      $('#ccm-dialog-delete-stackfolder')
        .find('input[name=stackfolderID]').val(cID).end()
        .dialog({
          modal: true,
          width: 320,
          height: 'auto',
          resizable: false
        })
      ;
      return false;
    });
  };

  // jQuery Plugin
  $.fn.concreteStackMenu = function(options) {
    return $.each($(this), function(i, obj) {
      new ConcreteStackMenu($(this), options);
    });
  };

  global.ConcreteStackMenu = ConcreteStackMenu;

})(this, jQuery);
