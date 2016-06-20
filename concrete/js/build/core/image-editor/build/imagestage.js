im.stage.setDragBoundFunc(function (ret) {
    var dim = im.stage.getTotalDimensions();

    var maxx = Math.max(dim.max.x, dim.min.x) + 100,
        minx = Math.min(dim.max.x, dim.min.x) - 100,
        maxy = Math.max(dim.max.y, dim.min.y) + 100,
        miny = Math.min(dim.max.y, dim.min.y) - 100;

    ret.x = Math.floor(ret.x);
    ret.y = Math.floor(ret.y);

    if (ret.x > maxx) ret.x = maxx;
    if (ret.x < minx) ret.x = minx;
    if (ret.y > maxy) ret.y = maxy;
    if (ret.y < miny) ret.y = miny;

    ret.x = Math.floor(ret.x);
    ret.y = Math.floor(ret.y);

    return ret;
});
im.setActiveElement(im.stage);
im.stage.setDraggable(true);
im.autoCrop = true;
im.on('imageLoad', function () {
    var padding = 50;

    var w = im.stage.getWidth() - (padding * 2), h = im.stage.getHeight() - (padding * 2);
    if (im.saveWidth < w && im.saveHeight < h) return;
    var perc = Math.max(im.saveWidth / w, im.saveHeight / h);

    im.scale = 1 / perc;
    im.scale = Math.round(im.scale * 100) / 100;
    im.alterCore('scale', im.scale);

    im.stage.setScale(im.scale);
    im.stage.setX((im.stage.getWidth() - (im.stage.getWidth() * im.stage.getScale().x)) / 2);
    im.stage.setY((im.stage.getHeight() - (im.stage.getHeight() * im.stage.getScale().y)) / 2);

    var pos = (im.stage.getDragBoundFunc())({x: im.stage.getX(), y: im.stage.getY()});
    im.stage.setX(pos.x);
    im.stage.setY(pos.y);

    im.fire('scaleChange');
    im.fire('stageChanged');
    im.buildBackground();
});

im.fit = function (wh, scale) {
    if (scale === false) {
        return {
            width: im.saveWidth,
            height: im.saveHeight
        };
    }
    var height = wh.height,
        width = wh.width;

    if (width > im.saveWidth) {
        height /= width / im.saveWidth;
        width = im.saveWidth;
    }
    if (height > im.saveHeight) {
        width /= height / im.saveHeight;
        height = im.saveHeight;
    }
    return {width: width, height: height};
};
