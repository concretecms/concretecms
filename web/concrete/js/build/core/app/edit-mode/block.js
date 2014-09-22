(function (window, $, _, Concrete) {
    'use strict';

    /**
     * Block's element
     * @param {jQuery}   elem      The blocks HTML element
     * @param {EditMode} edit_mode The EditMode instance
     */
    var Block = Concrete.Block = function Block(elem, edit_mode, peper) {
        this.init.apply(this, _(arguments).toArray());
    };

    Block.prototype = {

        init: function blockInit(elem, edit_mode, peper) {
            var my = this;
            elem.data('Concrete.block', my);

            if (!elem.children('.ccm-block-cover').length) {
                $('<div/>').addClass('ccm-block-cover').appendTo(elem);
            }

            Concrete.createGetterSetters.call(my, {
                id: elem.data('block-id'),
                active: true,
                handle: elem.data('block-type-handle'),
                areaId: elem.data('area-id'),
                cID: elem.data('cid'),
                wraps: !!elem.data('block-type-wraps'),
                area: null,
                elem: elem,
                dragger: null,
                draggerOffset: {x: 0, y: 0},
                draggerPosition: {x: 0, y: 0},
                dragging: false,
                rotationDeg: 0,
                editMode: edit_mode,
                selected: null,
                stepIndex: 0,
                peper: peper || elem
                    .children('.ccm-edit-mode-inline-commands, .ccm-custom-style-container')
                    .find('a[data-inline-command="move-block"]'),
                pepSettings: {}
            });

            my.id = my.getId();

            _(my.getPepSettings()).extend({
                deferPlacement: true,
                moveTo: function () {
                    my.dragPosition(this);
                },
                initiate: function blockDragInitiate(event, pep) {
                    my.pepInitiate.call(my, this, event, pep);
                },
                drag: function blockDrag(event, pep) {
                    my.pepDrag.call(my, this, event, pep);
                },
                start: function blockDragStart(event, pep) {
                    my.pepStart.call(my, this, event, pep);
                },
                stop: function blockDragStop(event, pep) {
                    my.pepStop.call(my, this, event, pep);
                },
                place: false
            });

            my.bindEvent('EditModeSelectableContender', function (e, data) {
                if (my.getDragging() && data instanceof Concrete.DragArea) {
                    my.setSelected(data);
                } else {
                    if (my.getDragging()) {
                        my.setSelected(null);
                    }
                }
            });

            my.getPeper().click(function (e) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }).pep(my.getPepSettings());
        },

        /**
         * This is fired when the object is destroyed and needs to be unbound.
         */
        destroy: function blockDestroy() {
            this.getPeper().unbind();
            $.pep.unbind(this.getPeper());
            this.setAttr('active', false);
        },

        bindEvent: function blockBindEvent(event, handler) {
            return Concrete.EditMode.prototype.bindEvent.apply(this, _(arguments).toArray());
        },

        getContainer: function blockGetActualElement() {
            var current = this.getElem();
            while (!current.parent().hasClass('ccm-area-block-list') && !current.parent().is('[data-area-id]')) {
                if (!current.parent().length) {
                    break;
                }
                current = current.parent();
            }
            return current;
        },

        addToDragArea: function blockAddToDragArea(drag_area) {
            var my = this,
                sourceArea = my.getArea(),
                targetArea = drag_area.getArea(),
                selected_block, wrapper;

            sourceArea.removeBlock(my);

            my.getContainer().remove();
            if (my.getWraps()) {
                wrapper = $(targetArea.getBlockTemplate()());
                drag_area.getElem().after(wrapper);
                if (wrapper.children().length) {
                    wrapper.find('div.block').replaceWith(my.getElem());
                } else {
                    wrapper.append(my.getElem());
                }
            } else {
                drag_area.getElem().after(my.getElem());
            }
            selected_block = drag_area.getBlock();
            if (selected_block) {
                drag_area.getArea().addBlock(my, selected_block);
            } else {
                drag_area.getArea().addBlockToIndex(my, 0);
            }
            my.getPeper().pep(my.getPepSettings());

            my.getEditMode().scanBlocks();
            Concrete.event.fire('EditModeBlockMove', {
                block: my,
                sourceArea: sourceArea,
                targetArea: targetArea
            });
        },

        handleAddResponse: function blockHandleAddResponse(response, area, after_block, onComplete) {
            var my = this,
                arEnableGridContainer = area.getEnableGridContainer() ? 1 : 0;

            if (response.error) {
                return;
            }
            $.get(CCM_DISPATCHER_FILENAME + '/ccm/system/block/render',
                {
                    arHandle: response.arHandle,
                    cID: response.cID,
                    bID: response.bID,
                    arEnableGridContainer: arEnableGridContainer
                }, function (html) {
                    if (after_block) {
                        after_block.getContainer().after(html);
                    } else {
                        area.getBlockContainer().prepend(html);
                    }
                    $.fn.dialog.hideLoader();
                    _.defer(function () {
                        my.getEditMode().scanBlocks();
                        my.showSuccessfulAdd();
                        Concrete.forceRefresh();

                        if (onComplete) {
                            onComplete();
                        }
                    });
                });
            return true;
        },

        showSuccessfulAdd: function blockShowSuccessfulAdd() {
            ConcreteAlert.notify({
                'message': ccmi18n.addBlockMsg,
                'title': ccmi18n.addBlock
            });
        },

        delete: function blockDelete(msg) {
            var my = this, bID = my.getId(),
                area = my.getArea(),
                block = area.getBlockByID(bID),
                cID = my.getCID(),
                arHandle = area.getHandle();

            ConcreteToolbar.disableDirectExit();
            area.removeBlock(block);
            ConcreteAlert.notify({
                'message': ccmi18n.deleteBlockMsg,
                'title': ccmi18n.deleteBlock
            });

            $.ajax({
                type: 'POST',
                url: CCM_DISPATCHER_FILENAME,
                data: 'cID=' + cID + '&ccm_token=' + CCM_SECURITY_TOKEN + '&isAjax=true&btask=remove&bID=' + bID + '&arHandle=' + encodeURIComponent(arHandle)
            });
        },

        /**
         * replaces a block in an area with a new block by ID and content
         */
        replace: function blockReplace(content) {
            var new_block_elem = $(content);

            this.getContainer().replaceWith(new_block_elem);

            this.getArea().scanBlocks();
            return this.getArea().getBlockByID(new_block_elem.data('block-id'));
        },

        getMenuElem: function blockGetMenuElem() {
            var my = this;
            return $('div.ccm-edit-mode-block-menu', this.getElem()).first();
        },

        bindMenu: function blockBindMenu() {
            var my = this,
                elem = my.getElem(),
                menuHandle = elem.attr('data-block-menu-handle'),
                $menuElem = my.getMenuElem();

            if (menuHandle !== 'none') {

                var menu_config = {
                    'highlightClassName': 'ccm-block-highlight',
                    'menuActiveClass': 'ccm-block-highlight',
                    'menu': $menuElem
                };

                if (my.getArea() && my.getArea().getElem().hasClass('ccm-global-area')) {
                    menu_config.menuActiveClass += " ccm-global-area-block-highlight";
                    menu_config.highlightClassName += " ccm-global-area-block-highlight";
                }

                my.setAttr('menu', new ConcreteMenu(elem, menu_config));

                $menuElem.find('a[data-menu-action=edit_inline]').unbind('click.core').on('click.core', function (event) {
                    Concrete.event.fire('EditModeBlockEditInline', {block: my, event: event});
                });

                $menuElem.find('a[data-menu-action=block_scrapbook]').unbind('click.core').on('click.core', function (event) {
                    Concrete.event.fire('EditModeBlockAddToClipboard', {block: my, event: event});
                });

                $menuElem.find('a[data-menu-action=delete_block]').unbind('click.core').on('click.core', function (event) {
                    Concrete.event.fire('EditModeBlockDelete', {
                        message: $(this).attr('data-menu-delete-message'),
                        block: my,
                        event: event
                    });
                });

                $menuElem.find('a[data-menu-action=block_design]').unbind('click.core').on('click.core', function (e) {
                    e.preventDefault();
                    Concrete.event.fire('EditModeBlockEditInline', {
                        block: my, event: e, action: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/block/design'
                    });
                });
            }
        },

        setArea: function blockSetArea(area) {
            this.setAttr('area', area);

            var my = this;
            my.getElem().find('a[data-menu-action=block_dialog]').each(function () {
                var href = $(this).data('menu-href');
                href += (href.indexOf('?') !== -1) ? '&cID=' + my.getCID() : '?cID=' + my.getCID();
                href += '&arHandle=' + encodeURIComponent(area.getHandle()) + '&bID=' + my.getId();
                $(this).attr('href', href).dialog();
            });

            my.bindMenu();
        },

        /**
         * Custom dragger getter, create dragger if it doesn't exist
         * @return {jQuery} dragger
         */
        getDragger: function blockgetDragger() {
            var my = this;

            if (!my.getAttr('dragger')) {
                var dragger = $('<a />')
                        .html(my.getElem().data('dragging-avatar') || ('<p><img src="/concrete/blocks/content/icon.png"><span>' + ccmi18n.content + '</span></p>'))
                        .addClass('ccm-block-edit-drag ccm-panel-add-block-draggable-block-type')
                    ;
                my.setAttr('dragger', dragger.css({
                    width: my.getElem().width(),
                    height: my.getElem().height()
                }));
            }
            return my.getAttr('dragger');
        },

        /**
         * Apply cross-browser compatible transformation
         * @param  {[String]} transformation String containing the css matrix
         * @return {Boolean}                 Success, always true
         */
        transform: function blockTransform(transformation, matrix) {
            var my = this;
            var element = my.getDragger().css({
                '-webkit-transform': transformation,
                '-moz-transform': transformation,
                '-ms-transform': transformation,
                '-o-transform': transformation,
                'transform': transformation
            }).get(0);

            // Modified transformie polyfill
            if (element.filters && !!element.filters['DXImageTransform.Microsoft.Matrix']) {
                var matrix_shim = {
                    elements: _(matrix).groupBy(function(v, key) { return Math.floor(key / 3); })
                };
                element.filters['DXImageTransform.Microsoft.Matrix'].M11 = matrix_shim.elements[0][0];
                element.filters['DXImageTransform.Microsoft.Matrix'].M12 = matrix_shim.elements[0][1];
                element.filters['DXImageTransform.Microsoft.Matrix'].M21 = matrix_shim.elements[1][0];
                element.filters['DXImageTransform.Microsoft.Matrix'].M22 = matrix_shim.elements[1][1];
                element.style.left = -(element.offsetWidth / 2) + (element.clientWidth / 2) + 'px';
                element.style.top = -(element.offsetHeight / 2) + (element.clientHeight / 2) + 'px';
            }
            return true;
        },

        resetTransform: function blockResetTransform() {
            var transformation = '';
            var element = this.getDragger().css({
                top: 0,
                left: 0,
                '-webkit-transform': transformation,
                '-moz-transform': transformation,
                '-ms-transform': transformation,
                '-o-transform': transformation,
                'transform': transformation
            }).get(0);

            if (element.filters) {
                element.filters = [];
            }

            this.setDraggerPosition({x: 0, y: 0});
            return this.renderPosition();
        },

        /**
         * Quick method to multiplty matrices, modified from a version on RosettaCode
         * @param  {Array}  matrix1 Array containing a matrix
         * @param  {Array}  matrix2 Array containing a matrix
         * @return {Array}          Array containing a matrix
         */
        multiplyMatrices: function blockMultiplyMatrices(matrix1, matrix2) {
            var result = [];
            for (var i = 0; i < matrix1.length; i++) {
                result[i] = [];
                for (var j = 0; j < matrix1[0].length; j++) {
                    var sum = 0;
                    for (var k = 0; k < matrix1[0].length; k++) {
                        sum += matrix1[i][k] * matrix2[k][j];
                    }
                    result[i][j] = sum;
                }
            }
            return result;
        },

        /**
         * Convert matrix to CSS value
         * @param  {Array}  matrix Array containing a matrix
         * @return {String}        CSS string
         */
        matrixToCss: function blockMatrixToCss(matrix) {
            var precision = 4, multiplier = Math.pow(10, precision), round = function (number) {
                return Math.round(number * multiplier) / multiplier;
            };
            matrix[0] = _(matrix[0]).map(round);
            matrix[1] = _(matrix[1]).map(round);
            var css_arr = [matrix[0][0], matrix[0][1], matrix[1][0], matrix[1][1], matrix[0][2], matrix[1][2]];
            return 'matrix(' + css_arr.join(',') + ')';
        },

        /**
         * Method to run after dragging stops for 50ms
         * @return {Boolean} Success, always true.
         */
        endRotation: function blockEndRotation() {
            var my = this;
            var start_rotation = my.getRotationDeg();
            my.getDragger().animate({rotation: 0}, {
                duration: 1, step: function () {
                }
            });
            var step_index = my.setStepIndex(my.getStepIndex() + 1);
            my.getDragger().animate({rotation: my.getRotationDeg()}, {
                queue: false, duration: 150, step: function (now) {
                    if (my.getStepIndex() !== step_index) {
                        return;
                    }
                    my.setRotationDeg(start_rotation - now);
                    my.renderPosition();
                }
            }, 'easeOutElastic');
            return true;
        },

        /**
         * Render the dragger in the correct position.
         * @return {Boolean} Success, always true.
         */
        renderPosition: function blockRenderPosition() {
            var my = this;

            var x = my.getDraggerPosition().x, y = my.getDraggerPosition().y, a = my.getRotationDeg() * (Math.PI / 180);

            var cos = _.bind(Math.cos, Math),
                sin = _.bind(Math.sin, Math);
            var position_matrix = [
                [1, 0, x],
                [0, 1, y],
                [0, 0, 1]
            ], rotation_matrix, final_matrix;
            if (a) {
                rotation_matrix = [
                    [cos(a), sin(a), 0],
                    [-sin(a), cos(a), 0],
                    [0, 0, 1]
                ];
                final_matrix = my.multiplyMatrices(position_matrix, rotation_matrix);
            } else {
                final_matrix = position_matrix;
            }
            return this.transform(my.matrixToCss(final_matrix), final_matrix);
        },

        /**
         * Position the dragger
         * @param  {Event}   event The triggering event
         * @param  {Pep}     pep   The pep instance
         * @return {Boolean}       Success, always true
         */
        dragPosition: function blockDragPosition(pep) {
            var my = this;

            my.setRotationDeg(Math.max(-15, Math.min(15, pep.velocity().x / 15)));
            my.endRotation();
            var position = _.last(pep.velocityQueue), offset = my.getDraggerOffset();
            if (!position) {
                position = {x: my.getDragger().offset().left, y: my.getDragger().offset().top};
            }
            var x = position.x - offset.x, y = position.y - offset.y;
            my.setDraggerPosition({x: x, y: y});
            my.renderPosition();

            return true;
        },

        pepInitiate: function blockPepInitiate(context, event, pep) {
            var my = this;
            my.resetTransform();
            my.setDragging(true);
            my.getDragger().hide().appendTo(window.document.body).css(my.getElem().offset());
            my.setDraggerOffset({
                x: event.clientX - my.getElem().offset().left + window.document.body.scrollLeft,
                y: event.clientY - my.getElem().offset().top + window.document.body.scrollTop
            });
            my.getDragger().fadeIn(250);

            _.defer(function () {
                Concrete.event.fire('EditModeBlockDragInitialization', {block: my, pep: pep, event: event});
            });
        },
        pepDrag: function blockPepDrag(context, event, pep) {
            var my = this;
            _.defer(function () {
                Concrete.event.fire('EditModeBlockDrag', {block: my, pep: pep, event: event});
            });
        },
        pepStart: function blockPepStart(context, event, pep) {
            var my = this;
            my.resetTransform();

            var elem = my.getElem(),
                mouse_position = {x: event.pageX, y: event.pageY},
                elem_position = {
                    x: elem.offset().left,
                    y: elem.offset().top
                },
                mouse_percentage = {
                    x: (elem_position.x - mouse_position.x) / elem.width(),
                    y: (elem_position.y - mouse_position.y) / elem.height()
                };

            my.setDraggerPosition({x: elem_position.x, y: elem_position.y});
            my.renderPosition();

            my.setDraggerOffset({
                x: -1 * (mouse_percentage.x * elem.width()),
                y: -1 * (mouse_percentage.y * elem.height())
            });

            my.getDragger().animate({
                width: 90,
                height: 90
            }, {
                duration: 250,
                step: function (now, fx) {
                    my.setDraggerOffset({
                        x: -1 * (mouse_percentage.x * $(this).width()),
                        y: -1 * (mouse_percentage.y * $(this).height())
                    });
                    my.dragPosition(pep);
                }
            });

            _.defer(function () {
                Concrete.event.fire('EditModeBlockDragStart', {block: my, pep: pep, event: event});
            });
        },

        pepStop: function blockPepStop(context, event, pep) {
            var my = this, drag_area;
            my.getDragger().stop(1);
            my.getDragger().css({top: 0, left: 0});
            my.dragPosition(pep);

            if ((drag_area = my.getSelected())) {
                my.addToDragArea(drag_area);
            }

            my.animateToElem();

            _.defer(function () {
                Concrete.event.fire('EditModeBlockDragStop', {block: my, pep: pep, event: event});
            });
        },

        animateToElem: function blockAnimateToElem(element) {
            var my = this, elem = element || my.getElem(), dragger_start = {
                x: my.getDraggerPosition().x,
                y: my.getDragger().offset().top,
                width: my.getDragger().width(),
                height: my.getDragger().height()
            };
            my.setDragging(false);
            my.getDragger().animate({ccm_perc: 0}, {
                duration: 0, step: function () {
                }
            }).animate({
                ccm_perc: 1,
                opacity: 0
            }, {
                duration: 500,
                step: function (now, fx) {
                    if (fx.prop === 'ccm_perc') {
                        var end_pos = {
                            x: elem.offset().left,
                            y: elem.offset().top,
                            width: elem.width(),
                            height: elem.height()
                        }, change = {
                            x: (end_pos.x - dragger_start.x) * now,
                            y: (end_pos.y - dragger_start.y) * now,
                            width: (end_pos.width - dragger_start.width) * now,
                            height: (end_pos.height - dragger_start.height) * now
                        };

                        my.setDraggerPosition({
                            x: dragger_start.x + change.x,
                            y: dragger_start.y + change.y
                        });
                        my.renderPosition();

                        my.getDragger().css({
                            width: dragger_start.width + change.width,
                            height: dragger_start.height + change.height
                        });
                    } else {
                        my.getDragger().css({
                            opacity: now
                        });
                    }
                },
                complete: function () {
                    my.getDragger().remove();
                    my.setAttr('dragger', null);
                }
            });
        }
    };


}(window, jQuery, _, Concrete));
