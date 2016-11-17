!function (global, $, _) {
    'use strict';

    function ConcreteMenu($element, options) {
        var my = this;
        options = options || {};
        options = $.extend({
            'handle': 'this',
            'menu': false,
            'highlightClassName': 'ccm-menu-highlight',
            'highlightOffset': 0,
            'menuContainerClass': false,
            'menuActiveClass': 'ccm-menu-item-active',
            'menuActiveParentClass': 'ccm-parent-menu-item-active',
            'menuLauncherHoverClass': 'ccm-menu-item-hover',
            'menuLauncherHoverParentClass': 'ccm-parent-menu-item-hover',
            'enabled': true,
            'enableClickProxy': true,
            'onHide': false
        }, options);

        my.$element = $element;
        my.options = options;
        if (options.handle == 'none') {
            my.$launcher = false;
        } else {
            my.$launcher = (options.handle == 'this') ? my.$element : $(options.handle);
            if (my.options.enableClickProxy) {
                my.$launcher.each(function () {
                    var $specificLauncher = $(this);
                    $specificLauncher.on('mousemove.concreteMenu', function (e) {
                        my.hoverProxy(e, $(this));
                    });
                });
            }
        }
        my.$menu = $(options.menu);
        my.setup();

        Concrete.event.bind('EditModeBlockDragInitialization', function () {
            my.hide();
        });
    }

    ConcreteMenu.prototype = {

        setup: function () {
            var my = this, options = my.options, global = ConcreteMenuManager;

            if (options.enableClickProxy) {
                if (!global.$clickProxy) {
                    global.$clickProxy = $("<div />", {'id': 'ccm-menu-click-proxy'});
                    global.$clickProxy.on('mouseout.concreteMenuProxy', function (e) {
                        var menu = global.hoverMenu;
                        menu.mouseout(e);
                    });
                    global.$clickProxy.on('mouseover.concreteMenuProxy', function (e) {
                        var menu = global.hoverMenu;
                        menu.mouseover(e);
                    });
                    global.$clickProxy.on('click.concreteMenuProxy', function (e) {
                        var menu = global.hoverMenu;
                        menu.show(e);
                    });
                    $(document.body).append(global.$clickProxy);
                }
                if (!global.$highlighter) {
                    global.$highlighter = $("<div />", {'id': 'ccm-menu-highlighter'});
                    $(document.body).append(global.$highlighter);
                }
            } else if (my.$launcher) {
                my.$launcher.on('mouseover.concreteMenu', function (e) {
                    my.mouseover(e);
                });
                my.$launcher.on('mouseout.concreteMenu', function (e) {
                    my.mouseout(e);
                });
                my.$launcher.on('click.concreteMenu', function (e) {
                    my.show(e);
                });
            }
            if (!global.$container) {
                global.$container = $("<div />", {'id': 'ccm-popover-menu-container', 'class': 'ccm-ui'});
                $(document.body).append(global.$container);
            }
        },

        destroy: function () {
            var my = this, global = ConcreteMenuManager;
            my.hide();
            my.$launcher.each(function () {
                $(this).unbind('mousemove.concreteMenu');
            });
        },

        positionAt: function ($elementToPosition, $elementToInspect) {
            if (!$elementToInspect) {
                return false;
            }

            var my = this,
                offset = $elementToInspect.offset(),
                properties = {
                    'top': offset.top - my.options.highlightOffset,
                    'left': offset.left - my.options.highlightOffset,
                    'width': $elementToInspect.outerWidth() + (my.options.highlightOffset * 2),
                    'height': $elementToInspect.outerHeight() + (my.options.highlightOffset * 2),
                    'border-top-left-radius': $elementToInspect.css('border-top-left-radius'),
                    'border-top-right-radius': $elementToInspect.css('border-top-right-radius'),
                    'border-bottom-left-radius': $elementToInspect.css('border-bottom-left-radius'),
                    'border-bottom-right-radius': $elementToInspect.css('border-bottom-right-radius')
                };

            $elementToPosition.css(properties);
        },

        hoverProxy: function (e, $specificLauncher) {
            e.stopPropagation();

            // we pass $launcher in because some menus can have multiple items
            // launch the same and we want to know which item triggered the launch
            var my = this,
                global = ConcreteMenuManager,
                menuLauncherHoverClass = my.options.menuLauncherHoverClass,
                $clickProxy = global.$clickProxy;

            if (!global.enabled || global.activeMenu) {
                return false;
            }

            my.positionAt($clickProxy, $specificLauncher);
            $clickProxy.removeClass().addClass(menuLauncherHoverClass);
            ConcreteMenuManager.hoverMenu = my;
        },

        mouseover: function (e) {
            var $launcher = this.$launcher, options = this.options;
            $launcher.addClass(options.menuLauncherHoverClass);
            $launcher.parents('*').slice(0, 3).addClass(options.menuLauncherHoverParentClass);
        },

        mouseout: function (e) {
            var $launcher = this.$launcher, options = this.options;
            $launcher.removeClass(options.menuLauncherHoverClass);
            $launcher.parents('*').slice(0, 3).removeClass(options.menuLauncherHoverParentClass);
        },

        setupMenuOptions: function ($menu) {
            $menu.find('.dialog-launch').dialog();
        },

        show: function (e) {
            var my = this,
                global = ConcreteMenuManager,
                options = my.options,
                $launcher = my.$launcher,
                $element = my.$element,
                $container = global.$container,
                $highlighter = global.$highlighter,
                $menu = my.$menu.clone(true, true),
                posX = e.pageX + 2,
                posY = e.pageY + 2;

            if (global.getActiveMenu() == my) {
                return false;
            }

            $menu.on('contextmenu', function() {
                return false;
            });
            e.stopPropagation();
            if (options.enableClickProxy) {
                $highlighter.removeClass();
                my.positionAt($highlighter, $launcher);
                _.defer(function () {
                    $highlighter.addClass(options.highlightClassName)
                });
            }

            $element.addClass(options.menuActiveClass);
            $element.parents('*').slice(0, 3).addClass(options.menuActiveParentClass);

            my.setupMenuOptions($menu);
            $container.html('');
            $menu.appendTo($container);
            if (options.menuContainerClass) {
                $container.addClass(options.menuContainerClass);
            }
            $menu.css('opacity', 0).show();

            var mwidth = $menu.width(),
                mheight = $menu.height(),
                wheight = $(window).height(),
                wwidth = $(window).width(),
                hshift = mwidth / 2 - 5,
                vshift = mheight / 2 - 5;

            var available = ['bottom', 'top', 'right', 'left'], all = available.slice(0);

            if (e.clientX < mwidth + 30) {
                available = _(available).without('left');
            }
            if (wwidth < (e.clientX + mwidth + 30)) {
                available = _(available).without('right');
            }
            if (wheight < (e.clientY + mheight + 30)) {
                available = _(available).without('bottom');
            }
            if (e.clientY < mheight + 30) {
                available = _(available).without('top');
            }

            if (wwidth < e.clientX + hshift ||
                e.clientX < hshift) {
                available = _(available).without('top', 'bottom');
            }

            if (wheight < e.clientY + vshift ||
                e.clientY < vshift) {
                available = _(available).without('left', 'right');
            }


            var placement = available.shift();
            $menu.removeClass(all).addClass(placement);

            e.pageX -= 2;
            e.pageY -= 2;
            switch (placement) {
                case 'left':
                    posX = e.pageX - mwidth;
                    posY = e.pageY - mheight / 2;
                    break;
                case 'right':
                    posX = e.pageX;
                    posY = e.pageY - mheight / 2;
                    break;
                case 'top':
                    posY = e.pageY - mheight;
                    posX = e.pageX - (mwidth / 2);
                    break;
                case 'bottom':
                    posY = e.pageY;
                    posX = e.pageX - (mwidth / 2);
                    break;
            }

            $menu.css({'top': posY, 'left': posX});
            _.defer(function () {
                $menu.css('opacity', 1);
            });

            $menu.find('a').click(function (e) {
                my.hide(e);
            });

            $(document).unbind('.concreteMenuDisable').on('click.concreteMenuDisable', function (e) {
                my.hide(e);
            });

            my.$menuPointer = $menu;
            ConcreteMenuManager.activeMenu = my;

            ConcreteEvent.publish('ConcreteMenuShow', {menu: my, menuElement: $menu});
        },

        hide: function (e) {
            var my = this,
                global = ConcreteMenuManager,
                reset = {'class': '', 'width': 0, 'height': 0, 'top': 0, 'left': 0};

            if (e) {
                e.stopPropagation();
            }

            if (my.$menuPointer) {
                my.$menuPointer.css('opacity', 0);
                _.defer(function () {
                    my.$menuPointer.hide();
                });
            }

            _.defer(function () {
                my.$element.removeClass(my.options.menuActiveClass);
                my.$element.parents('*').slice(0, 3).removeClass(my.options.menuActiveParentClass);
                if (my.options.enableClickProxy) {
                    global.$highlighter.removeClass();
                    global.$container.removeClass().addClass('ccm-ui').html('');
                }
            });

            if (my.options.enableClickProxy) {
                global.$clickProxy.css(reset);
                global.$highlighter.css(reset);
            }

            ConcreteMenuManager.activeMenu = false;

            if (my.options.onHide) {
                my.options.onHide(my);
            }

        }

    };

    var ConcreteMenuManager = {

        enabled: true,
        $clickProxy: false,
        $highlighter: false,
        $container: false,
        hoverMenu: false,
        activeMenu: false,

        reset: function () {
            this.$clickProxy.css('width', 0).css('height', 0);
            this.$container.html('');
        },

        enable: function () {
            this.enabled = true;
            this.reset();
        },

        disable: function () {
            this.enabled = false;
            this.reset();
        },

        getActiveMenu: function () {
            return this.activeMenu;
        }

    };


    // jQuery Plugin
    $.fn.concreteMenu = function (options) {
        return $.each($(this), function (i, obj) {
            new ConcreteMenu($(this), options);
        });
    };

    global.ConcreteMenu = ConcreteMenu;
    global.ConcreteMenuManager = ConcreteMenuManager;

}(this, $, _);
