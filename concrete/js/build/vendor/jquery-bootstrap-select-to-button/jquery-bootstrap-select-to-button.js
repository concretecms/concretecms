(function (window, document, $) {

  'use strict';

  // :icontains
  $.expr[":"].icontains = $.expr.createPseudo(function(arg) {
    return function(el) {
      return $(el).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
    };
  });

  var bootstrapSelectToButton = (function () {

    /**
     * Build button text from template
     * @param $select
     * @returns {*|jQuery|HTMLElement}
     */
    var buildButtonText = function ($select) {
      var options = $select.data('options');
      var $buttonText = $(options.templateButtonText);
      $select.data('$buttonText', $buttonText);
      return $buttonText;
    };

    /**
     * Build button icon from template
     * @param $select
     * @returns {*|jQuery|HTMLElement}
     */
    var buildButtonIcon = function ($select) {
      var options = $select.data('options');
      var $buttonIcon = $(options.templateButtonIcon);
      $select.data('$buttonIcon', $buttonIcon);
      return $buttonIcon;
    };

    /**
     * Build button from template
     * @param $select
     * @returns {*|jQuery|HTMLElement}
     */
    var buildButton = function ($select) {
      var options = $select.data('options');
      var $button = $(options.templateButton);
      // add style from options
      $button.addClass(options.classButtonStyle);
      $select.data('$button', $button);
      return $button;
    };

    /**
     * Build button group from template
     * @param $select
     * @returns {*|jQuery|HTMLElement}
     */
    var buildButtonGroup = function ($select) {
      var options = $select.data('options');
      var $buttonGroup = $(options.templateButtonGroup);
      // store reference to select for buttonGroupHandlers
      $buttonGroup.data('$select', $select);
      return $buttonGroup;
    };

    /**
     * Build menu from template using menu items
     * @param $select
     * @returns {*|jQuery}
     */
    var buildMenu = function ($select) {
      var options = $select.data('options');
      return $(options.templateMenu);
    };

    /**
     * Build menu item from template
     * @param index
     * @param value
     * @returns {*|jQuery|HTMLElement}
     */
    var buildMenuItem = function (index, value) {
      var options = this.data('options');
      var $menuItem = $(options.templateMenuItem);
      var $menuItemLink = $(options.templateMenuItemLink);
      // set text
      $menuItemLink.text($(value).text());
      // set disabled
      if ($(value).prop('disabled')) {
        $menuItem.addClass('disabled');
        $menuItemLink.prop('tabIndex', -1);
      }
      return $menuItem.append($menuItemLink);
    };

    /**
     * Build menu items and return as jQuery collection
     * @param $select
     * @returns {*}
     */
    var buildMenuItems = function ($select) {
      // setup proxy so we have access to options in menu item build
      var buildMenuItemProxy = $.proxy(buildMenuItem, $select);
      return $select.find('option').map(buildMenuItemProxy).toArray();
    };

    /**
     * Build menu search box
     * @param $select
     * @param menuItemsLength
     * @returns {*}
     */
    var buildMenuSearch = function ($select, menuItemsLength) {
      var options = $select.data('options');
      if (menuItemsLength > options.minItemsForSearch) {
        return $(options.templateSearchForm);
      }
      return $();
    };

    /**
     * Build menu search box
     * @param $select
     * @param $button
     * @returns {*}
     */
    var linkButtonToLabel = function ($select, $button) {
      var options = $select.data('options');
      var originalId = $select.attr('id');
      var newId = options.labelPrefix + $select.attr('id');

      var $label = $('label[for="' + originalId + '"]');
      $label.attr('for', newId);
      $button.attr('id', newId);
    };

    
    /**
     * Update button text after change
     * @param $select
     */
    var updateButtonText = function ($select) {
      var $buttonText = $select.data('$buttonText');
      $buttonText.text($select.find(':selected').text());
    };

    /**
     * Update button state after change
     * @param $select
     */
    var updateButtonState = function ($select) {
      var $button = $select.data('$button');
      $button.prop('disabled', $select.prop('disabled'));
    };

    /**
     * Update button focus after change
     * @param $select
     */
    var updateFocus = function ($select) {
      var $button = $select.data('$button');
      $button.focus();
    };

    /**
     * Event handler for select change
     * @param e
     */
    var handleSelectChange = function (e) {
      var $select = $(e.delegateTarget);
      updateButtonText($select);
      updateButtonState($select);
      updateFocus($select);
    };

    /**
     * Event handler for button item click
     * @param e
     */
    var handleClick = function (e) {
      e.preventDefault();
      var $btnGroup = $(e.delegateTarget);
      var $btnGroupItem = $(e.currentTarget);
      var $select = $btnGroup.data('$select');
      var selectedIndex = $btnGroup.find('li').index($btnGroupItem);
      var $option = $select.find('option').eq(selectedIndex);
      if (!$option.prop('disabled')) {
        $option.prop('selected', true).change();
      }
    };

    /**
     * Event handler for shown dropdown
     * @param e
     */
    var handleShown = function (e) {
      var $buttonGroup = $(this);
      var $searchInput = $buttonGroup.find('.dropdown input');
      if ($searchInput.length) {
        $searchInput.focus();
      } else {
        var selectedIndex = $buttonGroup.data('$select').find(':selected').index();
        $buttonGroup.find('li:eq(' + selectedIndex + ') a').focus();
      }
    };

    /**
     * Event handler for search
     * @param e
     */
    var handleSearch = function (e) {
      var $menuItems        = $(this).find('li').hide();
      var $menuItemsMatched = $menuItems.filter(':icontains(' + $(e.target).val() + ')').show();
      var $noResults        = $(this).find('.help-block');
      if ($menuItemsMatched.length) {
        $noResults.hide();
      } else {
        $noResults.show();
      }
    };

    /**
     * Transform native select into bootstrap dropdown
     */
    var transformSelect = function () {

      var $select = $(this);

      var $buttonIcon = buildButtonIcon($select);
      var $buttonText = buildButtonText($select);
      var $button     = buildButton($select);

      $button.append($buttonText);
      $button.append($buttonIcon);

      var $buttonGroup = buildButtonGroup($select);
      var $menuItems   = buildMenuItems($select);
      var $menuSearch  = buildMenuSearch($select, $menuItems.length);
      var $menu        = buildMenu($select);

      // add items to menu
      if ($menuSearch.length) {
        $menuItems.unshift($menuSearch);
      }
      $menu.append($menuItems)

      // add button and menu to group
      $buttonGroup.append($button);
      $buttonGroup.append($menu);

      // change label "for"
      linkButtonToLabel($select, $button);

      // insert group after select
      $buttonGroup.insertAfter($select);

      // set initial state
      updateButtonText($select);
      updateButtonState($select);
      
      // events
      $select.on('change', handleSelectChange);
      $buttonGroup.on('click', 'li', handleClick);
      $buttonGroup.on('shown.bs.dropdown', handleShown);
      $buttonGroup.on('.dropdown input', handleSearch);
    };

    return {
      transformSelect: transformSelect
    };

  })();

  /**
   * jQuery fn
   */
  $.fn.bootstrapSelectToButton = function(options) {
    options = $.extend({}, $.fn.bootstrapSelectToButton.defaults, options);
    return this.each(function() {
      $(this).data('options', options);
      this.style.display = 'none';
      bootstrapSelectToButton.transformSelect.call(this);
    });
  };

  $.fn.bootstrapSelectToButton.defaults = {
    templateButton:       '<button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>',
    templateButtonIcon:   '<span class="caret"></span>',
    templateButtonText:   '<span>{text}</span>',
    templateButtonGroup:  '<div class="btn-group"></div>',
    templateMenu:         '<ul class="dropdown-menu"></ul>',
    templateMenuItem:     '<li></li>',
    templateMenuItemLink: '<a href="#"></a>',
    templateSearchForm:   '<div class="dropdown"><form><input class="form-control" placeholder="Search"><span class="help-block" style="display:none">No results found</span></form></div>',
    classButtonStyle:     'btn-default',
    minItemsForSearch:    Infinity,
    labelPrefix:          'bstb-'
  };

}(this, this.document, this.jQuery));
