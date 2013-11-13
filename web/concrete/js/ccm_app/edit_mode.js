/**
 * concrete5 in context editing
 */
(function(window, $, _, c5) {
  'use strict';

  /**
   * First lay out objects
   */

  /**
   * Edit mode object for managing editing.
   */
  var EditMode = c5.EditMode = function EditMode() {
    var my = this, area, block;

    c5.createGetterSetters.call(my, {
      dragging: false,
      areas: [],
      blocks: [],
      selectedCache: [],
      selectedThreshold: 5,
      dragAreaBlacklist: []
    });

    c5.event.bind('panel.open', function editModePanelOpenEventHandler(event) {
      my.panelOpened(event.eventData);
    });

    c5.event.bind('EditModeBlockEditInline', function(event) {
      var data = event.eventData, block = data.block, area = block.getArea();
      window.CCMInlineEditMode.editBlock(CCM_CID, area.getId(), area.getHandle(), block.getId(), $(this).data('menu-action-params'));
    });

    c5.event.bind('EditModeBlockAddToClipboard', function(event) {
      var data = event.eventData, block = data.block, area = block.getArea();
      window.CCMEditMode.addBlockToScrapbook(CCM_CID, block.getId(), area.getHandle());
    });

    c5.event.bind('EditModeBlockDelete', function(event) {
      var data = event.eventData, block = data.block, area = block.getArea(), message = data.message;
      window.CCMEditMode.deleteBlock(CCM_CID, block.getId(), area.getId(), area.getHandle(), message);
    });

    c5.event.bind('EditModeBlockDrag', _.throttle(function editModeEditModeBlockDragEventHandler(event) {
      if (!my.getDragging()) {
        return;
      }
      var data = event.eventData, block = data.block, pep = data.pep,
          contenders = _.flatten(_(my.getAreas()).map(function(area) {
        var drag_areas = area.contendingDragAreas(pep, block);
        return drag_areas;
      }), true);

      _.defer(function() {
        c5.event.fire('EditModeContenders', contenders);
        my.selectContender(pep, block, contenders, data.event);
      });
    }, 250, true));

    c5.event.bind('EditModeBlockDragStop', function editModeEditModeBlockDragStopEventHandler() {
      c5.event.fire('EditModeContenders', []);
      c5.event.fire('EditModeSelectableContender');
      my.setDragging(false);
    });

    c5.event.bind('EditModeBlockMove', function editModeEditModeBlockMoveEventHandler(e) {
      var moved_block = e.eventData, area = moved_block.getArea(), data = {
        cID: CCM_CID,
        ccm_token: window.CCM_SECURITY_TOKEN,
        btask: 'ajax_do_arrange',
        area: area.getId(),
        block: moved_block.getId(),
        blocks: {}
      };
      _(area.getBlocks()).each(function(block, key){
        data.blocks[key] = block.getId();
      });
      var loading = false, timeout = setTimeout(function() {
        loading = true;
        $.fn.dialog.showLoader();
      }, 150);

      $.post(window.CCM_DISPATCHER_FILENAME, data, function() {
        if (loading) {
          $.fn.dialog.hideLoader();
        }
        clearTimeout(timeout);
      });
    });

    c5.event.bind('EditModeBlockDragStart', function editModeEditModeBlockDragStartEventHandler() {
      my.setDragging(true);
    });

    $('div.ccm-area').each(function(){
      area = new Area($(this), my);
      my.addArea(area);
    });
    $('div.ccm-block-edit').each(function(){
      my.addBlock(block = new Block($(this), my));
      _(my.getAreas()).findWhere({id: block.getAreaId()}).addBlock(block);
    });
  };

  /**
   * Area object, used for managing areas
   * @param {jQuery}   elem      The area's HTML element
   * @param {EditMode} edit_mode The EditMode instance
   */
  var Area = c5.Area = function Area(elem, edit_mode) {
    var my = this;

    c5.createGetterSetters.call(my, {
      id: elem.data('area-id'),
      elem: elem,
      handle: elem.data('area-handle'),
      dragAreas: [],
      blocks: [],
      editMode: edit_mode,
      blockTypes: elem.data('accepts-block-types').split(' ')
    });

    my.id = my.getId();
    my.addDragArea();
    elem.ccmmenu();
  };

  /**
   * Block's element
   * @param {jQuery}   elem      The blocks HTML element
   * @param {EditMode} edit_mode The EditMode instance
   */
  var Block = c5.Block = function Block(elem, edit_mode){
    var my = this;
    c5.createGetterSetters.call(my, {
      id: elem.data('block-id'),
      handle: elem.data('block-type-handle'),
      areaId: elem.data('area-id'),
      area: null,
      elem: elem,
      dragger: null,
      draggerOffset: {x:0, y:0},
      draggerPosition: {x:0, y:0},
      dragging: false,
      rotationDeg: 0,
      editMode: edit_mode,
      selected: null,
      stepIndex: 0,
      peper: elem.find('a[data-inline-command="move-block"]'),
      pepSettings:{}
    });
    my.id = my.getId();
    elem.ccmmenu();
    elem.find('a[data-menu-action=edit_inline]').on('click', function() {
      c5.event.fire('EditModeBlockEditInline', {block: my, event: event});
    });
    elem.find('a[data-menu-action=block_dialog]').each(function() {
      var href = $(this).attr('data-menu-href'), area = edit_mode.getAreaByID(my.getAreaId());
      href += (href.indexOf('?') !== -1) ? '&cID=' + CCM_CID : '?cID=' + CCM_CID;
      href += '&arHandle=' + encodeURIComponent(area.getHandle()) + '&bID=' + my.getId();
      $(this).attr('href', href);
      $(this).dialog();
    });
    elem.find('a[data-menu-action=block_scrapbook]').on('click', function() {
      c5.event.fire('EditModeBlockAddToClipboard', {block: my, event: event});
    });
    elem.find('a[data-menu-action=delete_block]').on('click', function() {
      c5.event.fire('EditModeBlockDelete', {message: $(this).attr('data-menu-delete-message'), block: my, event: event});
    });
   _(my.getPepSettings()).extend({
      deferPlacement: true,
      moveTo: function() { my.dragPosition(this); },
      initiate: function blockDragInitiate(event, pep) {
        my.setDragging(true);
        my.getDragger().hide().appendTo(window.document.body).css(my.getElem().offset());
        my.setDraggerOffset({x: event.clientX - my.getElem().offset().left + window.document.body.scrollLeft, y: event.clientY - my.getElem().offset().top + window.document.body.scrollTop});
        my.getDragger().fadeIn(250);

        _.defer(function(){
          c5.event.fire('EditModeBlockDragInitialization', {block: my, pep: pep, event: event});
        });
      },
      drag: function blockDrag(event, pep) {
        _.defer(function(){
          c5.event.fire('EditModeBlockDrag', {block: my, pep: pep, event: event});
        });
      },
      start: function blockDragStart(event, pep) {
        my.getDragger().css({top:0, left:0});
        my.setDraggerOffset({x: event.clientX - my.getElem().offset().left + window.document.body.scrollLeft, y: event.clientY - my.getElem().offset().top + window.document.body.scrollTop});
        my.dragPosition(pep);
        var start_pos = my.getDraggerOffset(), start_width = my.getDragger().width();
        my.getDragger().animate({width: 90, height:90}, {duration:250, step:function(now, fx) {
          if (fx.prop == 'width') {
            var change = start_width - now;
            my.setDraggerOffset({x: start_pos.x - change, y: start_pos.y });
            my.dragPosition(pep);
          }
        }});
        _.defer(function(){
          c5.event.fire('EditModeBlockDragStart', {block: my, pep: pep, event: event});
        });
      },
      stop: function blockDragStop(event, pep) {
        var selected_block;
        my.getDragger().stop(1);
        my.getDragger().css({top:0, left:0});
        my.dragPosition(pep);
        if (my.getSelected()) {
          my.getArea().removeBlock(my);
          my.getSelected().getElem().after(my.getElem());
          if (selected_block = my.getSelected().getBlock()) {
            my.getSelected().getArea().addBlock(my, selected_block);
          } else {
            my.getSelected().getArea().addBlockToIndex(my, 0);
          }
          my.getPeper().pep(my.getPepSettings());
          c5.event.fire('EditModeBlockMove', my);
        }

        var dragger_start = {
          x: my.getDraggerPosition().x,
          y: my.getDragger().offset().top,
          width: my.getDragger().width(),
          height: my.getDragger().height()
        };
        my.setDragging(false);

        // Manually animate so that we can ensure that we end at the element.
        my.getDragger().animate({ccm_perc: 0}, {duration: 0, step: function(){}}).animate({
          ccm_perc: 1,
          opacity: 0
        }, {
          duration: 500,
          step: function(now, fx) {
            if (fx.prop == 'ccm_perc') {
              var end_pos = {
                x: my.getElem().offset().left,
                y: my.getElem().offset().top,
                width: my.getElem().width(),
                height: my.getElem().height()
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
                opacity:now
              });
            }
          },
          complete: function(){
            my.getDragger().remove();
            my.setAttr('dragger', null);
          }
        });

        _.defer(function(){
          c5.event.fire('EditModeBlockDragStop', {block: my, pep: pep, event: event});
        });
      },
      place: false
    });

    c5.event.bind('EditModeSelectableContender', function(e) {
      if (my.getDragging() && e.eventData instanceof DragArea) {
        my.setSelected(e.eventData);
      } else {
        if (my.getDragging())
        my.setSelected(null);
      }
    });

    my.getPeper().css('z-index', '50000 !important').click(function(e){
      e.preventDefault();
      e.stopPropagation();
      return false;
    }).pep(my.getPepSettings());
  };

  /**
   * Drag Area that we create for dropping the blocks into
   * @param {jQuery}   elem  The drag area html element
   * @param {Area} area  The area it belongs to
   * @param {Block} block The block that this drag_area is above, this may be null.
   */
  var DragArea = c5.DragArea = function DragArea(elem, area, block) {
    var my = this;

    c5.createGetterSetters.call(my, {
      block: block,
      elem: elem,
      area: area,
      isContender: false,
      isSelectable: false,
      animationLength: 500
    });

    c5.event.bind('EditModeContenders', function(e) {
      var drag_areas = e.eventData;
      my.setIsContender(_.contains(drag_areas, my));
    });
    c5.event.bind('EditModeSelectableContender', function(e) {
      my.setIsSelectable(e.eventData == my);
    });
  };

  EditMode.prototype = {

    panelOpened: function editModePanelOpened(panel) {
      // @todo: panel logic :)
      return panel;
    },

    getAreaByID: function areaGetByID(arID) {
      return _(this.getAreas()).findWhere({id: arID});
    },

    /**
     * Select the correct contender
     * @param  {Pep}      pep        The relevant pep object
     * @param  {Block}    block      The Block
     * @param  {Array}    contenders The possible contenders
     * @param  {Event}    event      The triggering event
     * @return {DragArea}            The selected contender
     */
    selectContender: function editModeSelectContender(pep, block, contenders, event) {
      var my = this;

      // First, remove those that aren't selectable
      contenders = _(contenders).filter(function(drag_area) {
        return drag_area.isSelectable(pep, block, event);
      });
      if (contenders.length < 2) {
        return c5.event.fire('EditModeSelectableContender', _(contenders).first());
      }

      var selectedCache = my.getSelectedCache(), blacklist = my.getDragAreaBlacklist();
      if (my.getSelectedThreshold() == selectedCache.length && !_(selectedCache).without(_(selectedCache).last()).length) {
        blacklist.push(_(selectedCache).last());
        my.setDragAreaBlacklist(blacklist);

        _.delay(function(drag_area){
          var blacklist = my.getDragAreaBlacklist();
          my.setDragAreaBlacklist(_(blacklist).without(drag_area));
        }, 5000, _(selectedCache).last());

      }
      contenders = _(contenders).difference(blacklist);

      // Determine the closest area to center because why not
      var selected = _(contenders).min(function(drag_area) {
        var res = drag_area.centerDistanceToBlock(this);
        return res;
      }, block);

      selectedCache.push(selected);
      my.setSelectedCache(_(selectedCache).last(my.getSelectedThreshold()));

      c5.event.fire('EditModeSelectableContender', selected);
      return selected;
    },

    /**
     * Add an area to the areas
     * @param {Area} area Area to add
     */
    addArea: function editModeAddArea(area) {
      var my = this;

      my.getAreas().push(area);
    },

    /**
     * Add block to the blocks
     * @param {Block} block Block to add
     */
    addBlock: function editModeAddBlock(block) {
      var my = this;

      my.getBlocks().push(block);
    }
  };

  Area.prototype = {

    /**
     * Add block to area
     * @param  {Block}   block       block to add
     * @param  {Block}   sub_block The block that should be above the added block
     * @return {Boolean}             Success, always true
     */
    addBlock: function areaAddBlock(block, sub_block) {
      var my = this;

      if (sub_block) {
        return this.addBlockToIndex(block, _(my.getBlocks()).indexOf(sub_block));
      }

      block.setArea(my);
      my.getBlocks().push(block);
      my.addDragArea(block);
      return true;
    },

    /**
     * Add to specific index, pipes to addBlock
     * @param  {Block}   block Block to add
     * @param  {int}     index Index to add to
     * @return {Boolean}       Success, always true
     */
    addBlockToIndex: function areaAddBlockToIndex(block, index) {
      block.setArea(this);
      this.getBlocks().splice(index, 0, block);
      return true;
    },

    /**
     * Remove block from area
     * @param  {Block}   block The block to remove.
     * @return {Boolean}       Success, always true.
     */
    removeBlock: function areaRemoveBlock(block) {
      var my = this;

      block.getElem().remove();
      my.setBlocks(_(my.getBlocks()).without(block));

      var drag_area = _.first(_(my.getDragAreas()).filter(function(drag_area){
        return drag_area.getBlock() == block;
      }));
      if (drag_area) {
        drag_area.getElem().remove();
        my.setDragAreas(_(my.getDragAreas()).without(drag_area));
      }
      return true;
    },

    /**
     * Add a drag area
     * @param  {Block}    block The block to add this area below.
     * @return {DragArea}       The added DragArea
     */
    addDragArea: function areaAddDragArea(block) {
      var my = this, elem, drag_area;

      if (!block) {
        if (my.getDragAreas().length) {
          throw new Error('No block supplied');
        }
        elem = $('<div class="ccm-area-drag-area"/>');
        drag_area = new DragArea(elem, my, block);
        my.getElem().prepend(elem);
      } else {
        elem = $('<div class="ccm-area-drag-area"/>');
        drag_area = new DragArea(elem, my, block);
        block.getElem().after(elem);
      }
      my.getDragAreas().push(drag_area);
      return drag_area;
    },

    /**
     * Find the contending DragArea's
     * @param  {Pep}      pep   The Pep object from the event.
     * @param  {Block}    block The Block object from the event.
     * @return {Array}          Array of all drag areas that are capable of accepting the block.
     */
    contendingDragAreas: function areaContendingDragAreas(pep, block) {
      var my = this;

      if (!_(my.getBlockTypes()).contains(block.getHandle())) {
        return [];
      }
      return _(my.getDragAreas()).filter(function(drag_area) {
        return drag_area.isContender(pep, block);
      });
    }
  };

  Block.prototype = {

    /**
     * Custom dragger getter, create dragger if it doesn't exist
     * @return {jQuery} dragger
     */
    getDragger: function blockgetDragger() {
      var my = this;

      if (!my.getAttr('dragger')) {
        var dragger = $('<a><p><img src="/concrete/blocks/content/icon.png"><span>Content</span></p></a>').addClass('ccm-block-edit-drag').addClass('ccm-panel-add-block-draggable-block-type');
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
      if(element.filters) {
        if (!element.filters['DXImageTransform.Microsoft.Matrix']) {
          element.style.filter = (element.style.filter ? '' : ' ' ) + 'progid:DXImageTransform.Microsoft.Matrix(sizingMethod=\'auto expand\')';
        }

        element.filters['DXImageTransform.Microsoft.Matrix'].M11 = matrix.elements[0][0];
        element.filters['DXImageTransform.Microsoft.Matrix'].M12 = matrix.elements[0][1];
        element.filters['DXImageTransform.Microsoft.Matrix'].M21 = matrix.elements[1][0];
        element.filters['DXImageTransform.Microsoft.Matrix'].M22 = matrix.elements[1][1];
        element.style.left = -(element.offsetWidth/2) + (element.clientWidth/2) + 'px';
        element.style.top = -(element.offsetHeight/2) + (element.clientHeight/2) + 'px';
      }

      return true;
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
      var css_arr = [matrix[0][0], matrix[0][1], matrix[1][0], matrix[1][1], matrix[0][2], matrix[1][2]];
      return 'matrix(' + css_arr.join(',') + ')';
    },

    /**
     * Method to run after dragging stops for 50ms
     * @return {Boolean} Success, always true.
     */
    endRotation: function blockEndRotation(){
      var my = this;
      var start_rotation = my.getRotationDeg();
      my.getDragger().animate({rotation: 0}, {duration:1, step:function(){}});
      var step_index = my.setStepIndex(my.getStepIndex() + 1);
      my.getDragger().animate({rotation: my.getRotationDeg()}, {queue:false, duration:150, step:function(now) {
        if (my.getStepIndex() != step_index) {
          return;
        }
        my.setRotationDeg(start_rotation - now);
        my.renderPosition();
      }}, 'easeOutElastic');
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
      var position_matrix = [[ 1, 0, x ], [ 0, 1, y ], [ 0, 0, 1 ]], rotation_matrix, final_matrix;
      if (a) {
        rotation_matrix = [[ cos(a), sin(a), 0 ], [ -sin(a), cos(a), 0 ], [ 0 , 0 , 1 ]];
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
      if (!position) position = {x: my.getDragger().offset().left, y: my.getDragger().offset().top};
      var x = position.x - offset.x, y = position.y - offset.y;
      my.setDraggerPosition({ x: x, y: y });
      my.renderPosition();

      return true;
    }
  };

  DragArea.prototype = {

    /**
     * Is DragArea selectable
     * @param  {Pep}       pep   The active Pep
     * @param  {Block}     block The dragging Block
     * @param  {Event}     event The relevant event
     * @return {Boolean}         Is the dragarea selectable
     */
    isSelectable: function dragAreaIsSelectable(pep, block) {
      return pep.isOverlapping(block.getDragger(), this.getElem());
    },

    /**
     * Handle setting the DragArea to selectable, this is generally a visual change.
     * @param  {Boolean} is_selectable true/false
     * @return {Boolean}               Success, always true.
     */
    setIsSelectable: function dragAreaSetIsSelectable(is_selectable) {
      var my = this;

      if (is_selectable && !my.getIsSelectable()) {
        my.getElem().addClass('ccm-area-drag-area-selectable');
      } else if (!is_selectable && my.getIsSelectable()) {
        my.getElem().removeClass('ccm-area-drag-area-selectable');
      }
      my.setAttr('isSelectable', is_selectable);
      return true;
    },

    /**
     * Is this DragArea a contender
     * @param  {Pep}     pep   The relevant Pep object
     * @param  {Block}   block The dragging Block
     * @return {Boolean}       true/false
     */
    isContender: function dragAreaIsContender(pep, block) {
      var my = this;
      _.identity(pep); // This does nothing but quiet the lint

      return (my.getBlock() != block);
    },

    /**
     * Handle setting as contender
     * @param  {Boolean} is_contender Is this a contender
     * @return {Boolean}              Success, always true.
     */
    setIsContender: function dragAreaSetIsContender(is_contender) {
      var my = this;
      if (is_contender && !my.getIsContender()) {
        my.getElem().addClass('ccm-area-drag-area-contender');
      } else if (!is_contender && my.getIsContender()) {
        my.getElem().removeClass('ccm-area-drag-area-contender');
      }
      my.setAttr('isContender', is_contender);
      return true;
    },

    /**
     * Get the distance from the center of the DragArea to the center of a block.
     * @param  {Block}  block The block to measure
     * @return {double}       The distance from center to center
     */
    centerDistanceToBlock: function(block) {
      var my = this;

      var block_elem = block.getDragger(),
          block_center = {
            x: block_elem.offset().left + block_elem.width() / 2,
            y: block_elem.offset().top + block_elem.height() / 2
          },
          my_elem = my.getElem(),
          my_center = {
            x: my_elem.offset().left + my_elem.width() / 2,
            y: my_elem.offset().top + my_elem.height() / 2
          };

        return Math.sqrt(Math.pow(Math.abs(block_center.x - my_center.x), 2) + Math.pow(Math.abs(block_center.y - my_center.y), 2));
    }
  };

}(window, jQuery, _, c5));
