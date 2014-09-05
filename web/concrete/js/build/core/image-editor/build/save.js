im.save = function saveImage() {
    im.fire('ChangeActiveAction');
    im.fire('ImageEditorWillSave');

    $.fn.dialog.showLoader();

    im.stage.toDataURL({
        callback: function (data) {
            var fake_canvas = $('<img />').addClass('fake_canvas').appendTo(im.editorContext.children('.Editor'));
            fake_canvas.attr('src', data);

            fake_canvas.css({
                position: 'absolute',
                top: 0,
                left: 0,
                backgroundColor: 'white'
            });

            var oldStagePosition = im.stage.getPosition(),
                oldScale = im.scale,
                oldWidth = im.stage.getWidth(),
                oldHeight = im.stage.getHeight();

            im.stage.setPosition(-im.saveArea.getX(), -im.saveArea.getY());
            im.stage.setScale(1);
            im.background.hide();
            im.foreground.hide();
            im.stage.setHeight(im.saveHeight + 100);
            im.stage.setWidth(im.saveWidth + 100);
            im.stage.draw();

            im.stage.toDataURL({
                width: im.saveWidth,
                height: im.saveHeight,
                callback: function saveImageDataUrlCallback(url) {
                    im.stage.setPosition(oldStagePosition);
                    im.background.show();
                    im.foreground.show();
                    im.stage.setScale(oldScale);
                    im.stage.setHeight(oldHeight);
                    im.stage.setWidth(oldWidth);
                    im.stage.draw();

                    fake_canvas.remove();

                    $.post(im.saveUrl, _.extend(im.saveData, {
                        fID: im.fileId,
                        imgData: url
                    }), function (res) {
                        $.fn.dialog.hideLoader();
                        var result = JSON.parse(res);
                        if (result.error === 1) {
                            alert(result.message);
                            $('button.save[disabled]').attr('disabled', false);
                        } else if (result.error === 0) {
                            im.fire('ImageEditorDidSave', _.extend(im.saveData, {
                                fID: im.fileId,
                                imgData: url
                            }));
                            Concrete.event.fire('ImageEditorDidSave', _.extend(im.saveData, {
                                fID: im.fileId,
                                imgData: url
                            }));
                        }
                    });
                }
            });
        }
    });
};

im.actualPosition = function actualPosition(x, y, cx, cy, rad) {
    var ay = y - cy,
        ax = x - cx,
        degChange = im.activeElement.getRotation() + Math.atan2(ay, ax),
        r = Math.sqrt(Math.pow(ax, 2) + Math.pow(ay, 2));
    return [cx + (r * Math.cos(degChange)), cy + (r * Math.sin(degChange))];
};

im.getActualRect = function actualRect(cx, cy, elem) {
    var rect = [], rad = elem.getRotation();
    rect.push(im.actualPosition(elem.getX(), elem.getY(), cx, cy, rad));
    rect.push(im.actualPosition(elem.getX() + elem.getWidth() * elem.getScaleX(), elem.getY(), cx, cy, rad));
    rect.push(im.actualPosition(elem.getX() + elem.getWidth() * elem.getScaleX(), elem.getY() + elem.getHeight() * elem.getScaleY(), cx, cy, rad));
    rect.push(im.actualPosition(elem.getX(), elem.getY() + elem.getHeight() * elem.getScaleY(), cx, cy, rad));
    return rect;
};

im.adjustSavers = function AdjustingSavers(fire) {
    if (im.activeElement.nodeType === 'Stage') return;
    im.foreground.autoCrop = false;
    im.background.autoCrop = false;
    var i, e, u, score = {min: {x: false, y: false}, max: {x: false, y: false}};
    /*
     for (var i = im.stage.children.length - 1; i >= 0; i--) {
     var layer = im.stage.children[i];
     if (layer.autoCrop === false) continue;
     for (var e = layer.children.length - 1; e >= 0; e--) {
     var child = layer.children[e],
     rect = im.getActualRect(0, 0, child);
     console.log(child);

     for (var u = rect.length - 1; u >= 0; u--) {
     var point = rect[u], x = point[0] + layer.getX(), y = point[1] + layer.getY();
     if (x > score.max.x || score.max.x === false) score.max.x = x;
     if (x < score.min.x || score.min.x === false) score.min.x = x;
     if (y > score.max.y || score.max.y === false) score.max.y = y;
     if (y < score.min.y || score.min.y === false) score.min.y = y;
     }
     }
     }
     */
    var child = im.activeElement,
        layer = child.parent,
        rect = im.getActualRect(0, 0, child),
        u, size;

    for (u = rect.length - 1; u >= 0; u--) {
        var point = rect[u], x = point[0] + layer.getX(), y = point[1] + layer.getY();
        if (x > score.max.x || score.max.x === false) score.max.x = x;
        if (x < score.min.x || score.min.x === false) score.min.x = x;
        if (y > score.max.y || score.max.y === false) score.max.y = y;
        if (y < score.min.y || score.min.y === false) score.min.y = y;
    }

    size = {width: score.max.x - score.min.x, height: score.max.y - score.min.y};
    if (!im.strictSize) {
        im.alterCore('saveWidth', Math.round(size.width));
        im.alterCore('saveHeight', Math.round(size.height));
        im.buildBackground();
    }

    var ap = [im.center.x - (im.activeElement.getWidth() * im.activeElement.getScaleX()) / 2,
            im.center.y - (im.activeElement.getHeight() * im.activeElement.getScaleY()) / 2],
        adj = im.actualPosition(ap[0], ap[1], im.center.x, im.center.y, im.activeElement.getRotation());

    im.activeElement.parent.setPosition(adj.map(Math.round));

    if (fire !== false) im.fire('adjustedsavers');
    im.stage.draw();
};

im.bind('imageLoad', function () {
    setTimeout(im.adjustSavers, 0);
});
