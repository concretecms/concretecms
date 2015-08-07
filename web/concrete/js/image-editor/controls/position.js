var me = $(this), ratio_h, ratio_v;

im.bind('changeActiveElement', function () {
    if (im.strictSize) {
        im.activeElement.setDraggable(true);
    }
    im.activeElement.setPosition({x: 0, y: 0});
    im.adjustSavers();
    ratio = im.activeElement.getWidth() / im.activeElement.getHeight();
    height_input.val(im.activeElement.getHeight());
    width_input.val(im.activeElement.getWidth());
    resetScale();
});

function Rotation(im, me) {
    var my = this,
        RotationFlipModeVertical = 0,
        RotationFlipModeHorizontal = 1;

    function flip(rotationFlipMode) {
        var scale = im.activeElement.getScale(),
            scaleCopy = {x: scale.x, y: scale.y};
        switch (rotationFlipMode) {
            case RotationFlipModeHorizontal:
                scaleCopy.x *= -1;

                var degChange = im.activeElement.getRotation(),
                    r = -scaleCopy.x * im.activeElement.getWidth();
                im.activeElement.parent.move(r * Math.cos(degChange), r * Math.sin(degChange));
                break;
            case RotationFlipModeVertical:
                scaleCopy.y *= -1;

                im.activeElement.rotateDeg(90);
                var degChange = im.activeElement.getRotation(),
                    r = -scaleCopy.y * im.activeElement.getHeight();
                im.activeElement.rotateDeg(-90);
                im.activeElement.parent.move(r * Math.cos(degChange), r * Math.sin(degChange));

                break;
        }
        im.activeElement.setScale(scaleCopy);
        im.fire('activeElementShouldAdjustLayer');
        im.activeElement.parent.draw();
        im.fire('rotationChanged');
        setTimeout(function () {
            im.activeElement.parent.draw();
        }, 0);
    }

    $('button.hflip', me).click(function () {
        flip(RotationFlipModeHorizontal);
    });
    $('button.vflip', me).click(function () {
        flip(RotationFlipModeVertical);
    });
    $('button.rot', me).click(function () {
        var deg = im.activeElement.getRotationDeg();
        deg = (Math.round((deg + 90) / 90) * 90) % 360;
        im.activeElement.setRotationDeg(deg);
        im.activeElement.setPosition({x: 0, y: 0});
        im.adjustSavers();

        im.fire('activeElementShouldAdjustLayer');

        im.fire('rotationChanged');
        setTimeout(function () {
            im.activeElement.parent.draw();
        }, 0);
    });
    this.box = $('div.angle > input', me).val(0);
}

new Rotation(im, me);

var crop_area = $('div.crop-area', me);
var locked = true,
    lock = $('button.lock', me).click(function () {
        var method;
        if (locked) {
            method = $.fn.removeClass;
        } else {
            method = $.fn.addClass;
            ratio = im.activeElement.getWidth() / im.activeElement.getHeight();
        }
        locked = !locked;
        method.call(lock, 'active');
    });

var height_input = $('input[name="height"]', me).keyup(_.debounce(function (e) {
    if (e.which === 9) {
        return;
    }
    var height = parseInt($(this).val()), width = im.activeElement.getWidth();

    if (locked) {
        width = Math.floor(height * ratio);
    }

    im.activeElement.setWidth(width);
    im.activeElement.setHeight(height);

    im.fire('sizeChanged', { width: width, height: height });

}, 250));

var width_input = $('input[name="width"]', me).keyup(_.debounce(function (e) {
    if (e.which === 9) {
        return;
    }
    var width = parseInt($(this).val()), height = im.activeElement.getHeight();

    if (locked) {
        height = Math.floor(width * (1 / ratio));
    }

    im.activeElement.setWidth(width);
    im.activeElement.setHeight(height);

    im.fire('sizeChanged', { width: width, height: height });

}, 250));

var crop_button = $('button.begincrop', me).click(function() {
    if (im.crop) return;
    im.crop = new Crop;
    crop_button.attr('disabled', 1);
});

im.bind('sizeChanged', function (event, data) {
    im.activeElement.parent.draw();
    im.adjustSavers();

    width_input.val(data.width);
    height_input.val(data.height);

    if (!data.scale) {
        resetScale();
    }
});

var percent = $('span.scale-percent');
var scale = $('div.scale-slider');
var scale_width = 0, scale_height = 0, fired_from_scale = false;

var resetScale = function() {
    scale_width = im.activeElement.getWidth();
    scale_height = im.activeElement.getHeight();
    scale.slider('value', 100);
    percent.text('100%');
};

var resetDebounced = _.debounce(function() {
    resetScale();
}, 2000);

scale.slider({
    value: 100,
    min: 10,
    max: 500,
    step: 5,
    slide: function(event, data) {
        new_width = Math.floor(scale_width * (data.value / 100));
        new_height = Math.floor(scale_height * (data.value / 100));
        im.activeElement.setWidth(new_width);
        im.activeElement.setHeight(new_height);
        im.adjustSavers();

        percent.text(data.value + '%');
        resetDebounced();

        im.fire('sizeChanged', {
            width: new_width,
            height: new_height,
            scale: true
        });
    }
});

function Crop() {
    var crop = this;

    im.bind('ImageEditorWillSave', function() {
       crop.layer.hide();
    });

    this.active = true;
    crop_area.slideDown();
    this.layer = new Kinetic.Layer({
        fill: 'red',
        position: im.activeElement.parent.getPosition(),
        draggable: false,
        listening: true
    });

    im.bind('sizeChanged', function() {
        var old_position = _.clone(crop.layer.getPosition());
        crop.layer.setPosition(im.activeElement.parent.getPosition());
        crop.dragLayer.setPosition(im.activeElement.parent.getPosition());
        var new_position = _.clone(crop.layer.getPosition());

        crop.offset.x -= (new_position.x - old_position.x);
        crop.offset.y -= (new_position.y - old_position.y);

        crop.layer.draw();
    });

    var start_position, start_offset;
    this.cover = new Kinetic.Shape({
        fill: 'black',
        opacity: .4,
        width: im.stage.getWidth() * 2,
        height: im.stage.getHeight() * 2,
        listening: false,
        draggable: false,
        drawFunc: function (context) {
            var dimensions = im.stage.getTotalDimensions();
            context.beginPath();

            var x = dimensions.min.x - crop.layer.getPosition().x - (1 / im.scale * im.stage.getPosition().x),
                y = dimensions.min.y - crop.layer.getPosition().y - (1 / im.scale * im.stage.getPosition().y),
                offset = Math.max(dimensions.visibleWidth, dimensions.visibleHeight) * 2;

            context.rect(x - offset, y - offset, offset * 2, offset * 2);
            context.rect(crop.offset.x + crop.width, crop.offset.y, -crop.width, crop.height);

            context.closePath();
            context.fillStrokeShape(this);
        }
    });

    this.dragLayer = new Kinetic.Layer({
        position: crop.layer.getPosition()
    });

    this.dragRect = new Kinetic.Rect({
        draggable: true,
        listening: true,
        drawFunc: function (context) {
            var dimensions = im.stage.getTotalDimensions();
            context.beginPath();
            context.rect(crop.offset.x + crop.width, crop.offset.y, -crop.width, crop.height);

            context.closePath();
            context.fillStrokeShape(this);
        }
    })
        .on('dragstart', function () {
            start_position = _.clone(this.getPosition());
            start_offset = _.clone(crop.offset);
        })
        .on('dragmove', function () {
            var change = {
                x: this.getPosition().x - start_position.x,
                y: this.getPosition().y - start_position.y
            };
            crop.offset.x = start_offset.x + change.x;
            crop.offset.y = start_offset.y + change.y;

            crop.positionDraggers(this);
            crop.draggers.top.left.parent.draw();
        })
        .on('dragend', function () {
            var me = this;
            _.defer(function () {
                crop.layer.moveToTop();
                im.stage.draw();
                me.setPosition(0, 0);
                me.parent.draw();
            });
        });

    this.dragLayer.add(this.dragRect);

    return this.init();
}

Crop.prototype = {

    init: function () {
        this.locked = true;

        this.initializeDraggers();

        var dragger = this.draggers, layer = this.layer, crop = this;
        layer.add(this.cover);
        layer.add(dragger.top.left);
        layer.add(dragger.top.right);
        layer.add(dragger.bottom.left);
        layer.add(dragger.bottom.right);

        layer.setRotation(im.activeElement.getRotation());
        layer.setScale(im.activeElement.getScale());
        this.dragLayer.setRotation(im.activeElement.getRotation());
        this.dragLayer.setScale(im.activeElement.getScale());

        this.width = im.activeElement.getWidth();
        this.height = im.activeElement.getHeight();
        this.offset = {
            x: 0,
            y: 0
        };

        im.stage.add(this.dragLayer);
        im.stage.add(layer);
        layer.moveToTop();
        im.stage.draw();

        im.bind('scaleChange', function (event) {
            if (!crop.active) return;
            dragger.top.left.setScale(1 / im.scale);
            dragger.top.right.setScale(1 / im.scale);
            dragger.bottom.left.setScale(1 / im.scale);
            dragger.bottom.right.setScale(1 / im.scale);
            crop.dragRect.parent.draw();
        });

        var start_position;
        im.bind('rotationChanged', function() {
            _.defer(function(){
                layer.setRotation(im.activeElement.getRotation());
                layer.setScale(im.activeElement.getScale());
                crop.dragLayer.setRotation(im.activeElement.getRotation());
                crop.dragLayer.setScale(im.activeElement.getScale());
                im.stage.draw();
            });
        });

        im.bind('sizeChanged', function (event, data) {
            if (!crop.active) return;
            crop.positionDraggers();
            crop.dragRect.parent.draw();
        });

        var ratio = im.activeElement.getWidth() / im.activeElement.getHeight();

        var lock_button = $('button.croplock').click(function () {
            if (!crop.active) return;

            var method;
            if (crop.locked) {
                method = $.fn.removeClass;
            } else {
                method = $.fn.addClass;
                ratio = crop.width / crop.height;
            }
            crop.locked = !crop.locked;
            method.call(lock_button, 'active');
        }).addClass('active');
        im.bind('changeActiveElement', function () {
            if (crop !== im.crop) Concrete.event.unbind(event, im.stage.getContainer());

            ratio = crop.width / crop.height;
            im.stage.draw();
        });

        var do_crop = $('button.docrop', me).click(function() {
            if (!crop.active) return;
            crop.finalize();
        });
        var cancel_crop = $('button.cancel', me).click(function() {
            crop.destroy();
            im.adjustSavers();
        });


        var height_input = $('input[name="cropheight"]', me).keyup(_.debounce(function () {
            var height = parseInt($(this).val()), width = crop.width;

            if (!height || isNaN(height)) {
                height = im.activeElement.getHeight();
            }

            if (crop.locked) {
                width = Math.floor(height * ratio);
            }

            crop.width = width;
            crop.height = height;

            im.fire('cropSizeChanged', { width: width, height: height });

        }, 250)).val(crop.height);

        var width_input = $('input[name="cropwidth"]', me).keyup(_.debounce(function () {
            var width = parseInt($(this).val()), height = crop.height;

            if (!width || isNaN(width)) {
                width = im.activeElement.getHeight();
            }

            if (crop.locked) {
                height = Math.floor(width * (1 / ratio));
            }

            crop.width = Math.abs(width);
            crop.height = Math.abs(height);

            im.fire('cropSizeChanged', { width: width, height: height });

        }, 250)).val(crop.width);

        im.bind('cropSizeChanged', function (event, data) {
            crop.width = Math.floor(data.width);
            crop.height = Math.floor(data.height);
            width_input.val(crop.width);
            height_input.val(crop.height);
            _.defer(function () {
                crop.positionDraggers();
                layer.draw();
            });
        });

        this.positionDraggers();
    },

    initializeDraggers: function () {
        var crop = this, start_position, start_offset, start_width, start_height,
            dragger = new Kinetic.Rect({
                width: 10,
                height: 10,
                offset: {
                    x: 5,
                    y: 5
                },
                scale: 1 / im.scale,
                stroke: 'black',
                fill: 'white',
                draggable: true,
                listening: true
            }).on('dragstart',function () {
                    start_position = _.clone(this.getPosition());
                    start_offset = _.clone(crop.offset);
                    start_width = crop.width;
                    start_height = crop.height;
                }).on('dragend',function () {
                    crop.positionDraggers();

                    im.stage.draw();
                }).on('dragmove', function () {
                    _.defer(function () {
                        im.fire('cropSizeChanged', {
                            width: crop.width,
                            height: crop.height
                        });
                    });
                });

        var drawFunc = dragger.getDrawFunc();
        dragger.setDrawFunc(function () {
            crop.positionDraggers();
            drawFunc.apply(this, arguments);
        });

        this.draggers = {
            top: {
                left: dragger,
                right: dragger.clone()
            },
            bottom: {
                left: dragger.clone(),
                right: dragger.clone()
            }
        };

        function getOffset() {
            return {
                x: this.getPosition().x - start_position.x,
                y: this.getPosition().y - start_position.y
            };
        }

        this.draggers.top.left.on('dragmove', function () {
            var offset = getOffset.call(this);

            if (crop.locked) {
                offset.y = offset.x * (start_height / start_width);
            }

            crop.width = start_width - offset.x;
            crop.height = start_height - offset.y;
            crop.offset.y = start_offset.y + offset.y;
            crop.offset.x = start_offset.x + offset.x;

            crop.positionDraggers(this);
        });

        this.draggers.top.right.on('dragmove', function () {
            var offset = getOffset.call(this);

            if (crop.locked) {
                offset.y = -offset.x * (start_height / start_width);
            }

            crop.width = start_width + offset.x;
            crop.height = start_height - offset.y;
            crop.offset.y = start_offset.y + offset.y;

            crop.positionDraggers(this);
        });

        this.draggers.bottom.left.on('dragmove', function () {
            var offset = getOffset.call(this);

            if (crop.locked) {
                offset.y = -offset.x * (start_height / start_width);
            }
            crop.width = start_width - offset.x;
            crop.height = start_height + offset.y;
            crop.offset.x = start_offset.x + offset.x;

            crop.positionDraggers(this);
        });

        this.draggers.bottom.right.on('dragmove', function () {
            var offset = getOffset.call(this);

            if (crop.locked) {
                offset.y = offset.x * (start_height / start_width);
            }
            crop.width = start_width + offset.x;
            crop.height = start_height + offset.y;

            crop.positionDraggers(this);
        });
    },

    positionDraggers: function (omit_dragger) {
        var top_left = this.draggers.top.left, top_right = this.draggers.top.right,
            bottom_left = this.draggers.bottom.left, bottom_right = this.draggers.bottom.right,
            offset;

        if (this.height < 0 || this.width < 0) {
            this.height = Math.max(this.height, 0);
            this.width = Math.max(this.width, 0);
            omit_dragger = null;
        }
        offset = {
            width: (-this.width * im.scale) + 5,
            height: (-this.height * im.scale) + 5
        };

        if (top_left !== omit_dragger) {
            top_left.setPosition({
                x: this.offset.x,
                y: this.offset.y
            });
        }
        if (top_right !== omit_dragger) {
            top_right.setPosition({
                x: this.offset.x,
                y: this.offset.y
            }).setOffset({
                x: offset.width,
                y: 5
            });
        }
        if (bottom_left !== omit_dragger) {
            bottom_left.setPosition({
                x: this.offset.x,
                y: this.offset.y
            }).setOffset({
                x: 5,
                y: offset.height
            });
        }
        if (bottom_right !== omit_dragger) {
            bottom_right.setPosition({
                x: this.offset.x,
                y: this.offset.y
            }).setOffset({
                x: offset.width,
                y: offset.height
            });
        }
    },

    destroy: function () {
        var me = this;
        this.active = false;
        this.layer.removeChildren();
        this.layer.remove();
        this.dragLayer.removeChildren();
        this.dragLayer.remove();
        im.crop = null;
        crop_button.enable();
        crop_area.slideUp();

        _(this).chain().functions().each(function(val) {
            me[val] = function(){};
        });
    },

    finalize: function() {
        var crop = this,
            elem = im.activeElement,
            old_filter = im.activeElement.getFilter(),
            old_rotation = im.activeElement.getRotationDeg(),
            old_scale = im.activeElement.getScale(),
            url;

        im.stage.setScale(1);
        im.stage.setPosition(0, 0);
        im.activeElement.parent.setPosition(0, 0);
        im.activeElement.setRotationDeg(0);
        im.activeElement.setScale(1, 1);
        im.activeElement.clearFilter();
        elem.toImage({
            x: 0,
            y: 0,
            width: elem.getWidth(),
            height: elem.getHeight(),
            callback: function(image) {
                im.activeElement.setImage(image);
                im.activeElement.setFilter(old_filter);
                im.activeElement.setRotationDeg(old_rotation);
                im.activeElement.setScale(old_scale);
                elem.setCrop({
                    x: crop.offset.x,
                    y: crop.offset.y,
                    width: crop.width,
                    height: crop.height
                });
                im.stage.setScale(im.scale);

                elem.setWidth(crop.width);
                elem.setHeight(crop.height);

                elem.parent.draw();
                im.adjustSavers();
                crop.destroy();
                im.stage.draw();

                im.fire('activeElementSizeChange');
                im.fire('sizeChanged', {
                    width: crop.width,
                    height: crop.height
                });
            }
        });

    }

};
