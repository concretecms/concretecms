var me = $(this);

me.find('.cancelbutton').click(function() {
    im.activeElement.clearFilter();
    im.activeElement.draw();
});
im.selected = false;
im.bind('ChangeActiveAction', function (e, data) {
    if (data != im.namespace) {
        if (im.selected)
            im.hideSlideOut();
        im.selected = false;
    } else {
        im.selected = true;
        im.showSlideOut(ul.clone(1))
    }
});
im.bind('ChangeActiveElement', function (e) {
    if (im.activeElement.elementType != 'image') {
        im.disable();
        return;
    }
    im.enable();
});

var img = new Image();
var loaded = false;
var waiting = [];
im.onload = function () {
    loaded = true;
    $.each(waiting, function (e, func) {

    });
};
var lis = {};
img.src = CCM_REL + "/concrete/images/image_editor/default_filter_image.jpg";

var ul = $($.parseHTML('<ul/>')).addClass('slideOutBlockList');
im.bind('filterLoad', function (e, data) {
    var newFilter = data;
    if (!newFilter) return;
    var li = $($.parseHTML('<li/>')).appendTo(ul);
    var title = $($.parseHTML('<span/>')).appendTo(li).text(newFilter.name).addClass('title');
    var controls = me.find(newFilter.settings.selector);
    lis[newFilter.im.namespace] = li;
    (function () {
        var div = document.createElement(div);
        var stage = new Kinetic.Stage({
            container: div,
            width: 160,
            height: 130
        });
        var layer = new Kinetic.Layer();
        var image = new Kinetic.Image({
            image: img,
            width: 160,
            height: 130
        });
        layer.add(image);
        stage.add(layer);
        stage.draw();
        im.bind('filterBuiltExample', function (e) {
            stage.toImage({
                width: 160,
                height: 130,
                x: 0,
                y: 0,
                callback: function (renderedimage) {
                    li.append($(renderedimage));
                }
            });
        }, li.get(0));
        im.fire('filterApplyExample', {namespace: newFilter.im.namespace, image: image, elem: li.get(0)});
    })();
    newFilter.parent = me;

    // Bindings.
    li.click(function () {
        im.fire('filterChange', newFilter);
    });

    im.bind('filterChange', function(event, filter) {
        if (filter === newFilter) {
            controls.addClass('active');
        } else {
            controls.removeClass('active');
        }
    });

    me.append(newFilter.label);
    me.append(newFilter.controls);
    im.fire('filterFullyLoaded', newFilter);
});
im.bind('filterApplied', function (e) {
    im.activeElement.parent.draw();
});
