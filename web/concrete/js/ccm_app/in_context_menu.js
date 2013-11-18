/**
 * Base search class for AJAX forms in the UI
 */

!function(global, $, _) {
	'use strict';

	function ConcreteMenu($element, options) {
		var my = this;
		options = options || {};
		options = $.extend({
			'handle': 'this',
			'menu': false,
			'highlightClassName': 'ccm-menu-highlight',
			'highlightOffset': 0,
			'menuActiveClass': 'ccm-menu-item-active',
			'menuActiveParentClass': 'ccm-parent-menu-item-active',
			'menuLauncherHoverClass': 'ccm-menu-item-hover',
			'menuLauncherHoverParentClass': 'ccm-parent-menu-item-hover',
			'enabled': true
		}, options);

		my.$element = $element;
		my.options = options;
		if (options.handle == 'none') {
			my.$launcher = false;
		} else {
			my.$launcher = (options.handle == 'this') ? my.$element : $(options.handle);
		}
		my.$menu = $(options.menu);

		my.$launcher.on('mousemove.concreteMenu', function(e) {
			my.hoverProxy(e);
		});

		my.setup();
	}

	ConcreteMenu.prototype = {
		
		setup: function() {
			var my = this, options = my.options, global = ConcreteMenuManager;

			if (!global.$clickProxy) {
				global.$clickProxy = $("<div />", {'id': 'ccm-menu-click-proxy'});
				global.$clickProxy.on('mouseout.concreteMenuProxy', function(e) {
					var menu = global.hoverMenu;
					menu.mouseout(e);
				});
				global.$clickProxy.on('mouseover.concreteMenuProxy', function(e) {
					var menu = global.hoverMenu;
					menu.mouseover(e);
				});
				global.$clickProxy.on('click.concreteMenuProxy', function(e) {
					var menu = global.hoverMenu;
					menu.show(e);
				});
				$(document.body).append(global.$clickProxy);
			}
			if (!global.$highlighter) {
				global.$highlighter = $("<div />", {'id': 'ccm-menu-highlighter'});
				$(document.body).append(global.$highlighter);
			}
			if (!global.$container) {
				global.$container = $("<div />", {'id': 'ccm-popover-menu-container', 'class': 'ccm-ui'});
				$(document.body).append(global.$container);
			}
		},

		positionAt: function($elementToPosition, $elementToInspect) {
			var my = this, 
				global = ConcreteMenuManager,
				offset = $elementToInspect.offset(),
				$clickProxy = global.$clickProxy,
				properties = {
					'top': offset.top - my.options.highlightOffset,
					'left': offset.left - my.options.highlightOffset,
					'width': $elementToInspect.outerWidth() + (my.options.highlightOffset * 2),
					'height': $elementToInspect.outerHeight() + (my.options.highlightOffset * 2),
					'border-top-left-radius': $elementToInspect.css('border-top-left-radius'),
					'border-top-right-radius': $elementToInspect.css('border-top-right-radius'),
					'border-bottom-left-radius': $elementToInspect.css('border-bottom-left-radius'),
					'border-bottom-right-radius': $elementToInspect.css('border-bottom-right-radius'),
				};
				$elementToPosition.css(properties);
		},

		hoverProxy: function(e) {
			var my = this, 
				global = ConcreteMenuManager,
				menuLauncherHoverClass = my.options.menuLauncherHoverClass, 
				$clickProxy = global.$clickProxy, 
				$launcher = my.$launcher;
			
			if (!global.enabled || global.activeMenu) {
				return false;
			}

			if (my.$launcher) {
				my.positionAt($clickProxy, $launcher);
				$clickProxy.addClass(menuLauncherHoverClass);
				ConcreteMenuManager.hoverMenu = my;
			}
		},

		mouseover: function(e) {
			var $launcher = this.$launcher, options = this.options;
			$launcher.addClass(options.menuLauncherHoverClass);
			$launcher.parents('*').slice(0,3).addClass(options.menuLauncherHoverParentClass);
		},

		mouseout: function(e) {
			var $launcher = this.$launcher, options = this.options;
			$launcher.removeClass(options.menuLauncherHoverClass);
			$launcher.parents('*').slice(0,3).removeClass(options.menuLauncherHoverParentClass);
		},

		show: function(e) {
			var my = this, 
				global = ConcreteMenuManager,
				options = my.options,
				$launcher = my.$launcher,
				$element = my.$element,
				$container = global.$container,
				$highlighter = global.$highlighter,
				$menu = my.$menu.clone(true,true),
				posX = e.pageX + 2,
				posY = e.pageY + 2;

			e.stopPropagation();
			$highlighter.removeClass();
			my.positionAt($highlighter, $launcher);
			_.defer(function() { 
				$highlighter.addClass(options.highlightClassName)
			});

			$element.addClass(options.menuActiveClass);
			$element.parents('*').slice(0,3).addClass(options.menuActiveParentClass);
			$menu.find('.dialog-launch').dialog();
			$container.html('');
			$menu.appendTo($container);

			$menu.css('opacity', 0).show();
			var	mwidth = $menu.width(),
				mheight = $menu.height(); // have to do this after you show the element

			if ($(window).height() < (e.clientY + mheight + 30)) {
				posY = posY - mheight - 10;
				posX = posX - (mwidth / 2);
				$menu.removeClass('bottom');
				$menu.addClass('top');
			} else {
				posX = posX - (mwidth / 2);
				posY = posY + 10;
				$menu.removeClass('top');
				$menu.addClass('bottom');
			}	

			$menu.css({'top': posY + 'px', 'left': posX + 'px'});
			_.defer(function() {
				$menu.css('opacity', 1);
			});

			$menu.find('a').click(function(e) {
				my.hide(e);
			});

			$(document).unbind('.concreteMenuDisable').on('click.concreteMenuDisable', function(e) {
				my.hide(e);
			});

			my.$menuPointer = $menu;
			ConcreteMenuManager.activeMenu = my;
		},

		hide: function(e) {
			var my = this, 
				global = ConcreteMenuManager,
				reset = {'class': '', 'width': 0, 'height': 0, 'top': 0, 'left': 0}

			if (e) {
				e.stopPropagation();
			}

			if (my.$menuPointer) {
				my.$menuPointer.css('opacity',0);
				_.defer(function() { my.$menuPointer.hide(); });
			}

			_.defer(function() { 
				my.$element.removeClass(my.options.menuActiveClass); 
				my.$element.parents('*').slice(0,3).removeClass(my.options.menuActiveParentClass);
			});

			global.$clickProxy.css(reset);
			global.$highlighter.css(reset);

			ConcreteMenuManager.activeMenu = false;
		}

	}

	var ConcreteMenuManager = {

		enabled: true,
		$clickProxy: false,
		$highlighter: false,
		$container: false,
		hoverMenu: false,
		activeMenu: false,

		reset: function() {

		},

		enable: function() {
			this.enabled = true;
		},

		disable: function() {
			this.enabled = false;
		},

		getActiveMenu: function() {
			return this.activeMenu;
		}

	}


	// jQuery Plugin
	$.fn.concreteMenu = function(options) {
		return $.each($(this), function(i, obj) {
			new ConcreteMenu($(this), options);
		});
	}

	global.ConcreteMenu = ConcreteMenu;
	global.ConcreteMenuManager = ConcreteMenuManager;

}(this, $, _);