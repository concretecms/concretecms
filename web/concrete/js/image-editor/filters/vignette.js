var buildBezier = function (start, ctrl1, ctrl2, end, lowBound, highBound) {
    var Ax, Ay, Bx, By, Cx, Cy, bezier, curveX, curveY, i, j, leftCoord, rightCoord, t, x0, x1, x2, x3, y0, y1, y2, y3, _i, _j, _k, _ref, _ref1;
    x0 = start[0];
    y0 = start[1];
    x1 = ctrl1[0];
    y1 = ctrl1[1];
    x2 = ctrl2[0];
    y2 = ctrl2[1];
    x3 = end[0];
    y3 = end[1];
    bezier = {};
    Cx = parseInt(3 * (x1 - x0), 10);
    Bx = 3 * (x2 - x1) - Cx;
    Ax = x3 - x0 - Cx - Bx;
    Cy = 3 * (y1 - y0);
    By = 3 * (y2 - y1) - Cy;
    Ay = y3 - y0 - Cy - By;
    for (i = _i = 0; _i < 1000; i = ++_i) {
        t = i / 1000;
        curveX = Math.round((Ax * Math.pow(t, 3)) + (Bx * Math.pow(t, 2)) + (Cx * t) + x0);
        curveY = Math.round((Ay * Math.pow(t, 3)) + (By * Math.pow(t, 2)) + (Cy * t) + y0);
        if (lowBound && curveY < lowBound) {
            curveY = lowBound;
        } else if (highBound && curveY > highBound) {
            curveY = highBound;
        }
        bezier[curveX] = curveY;
    }
    if (bezier.length < end[0] + 1) {
        for (i = _j = 0, _ref = end[0]; 0 <= _ref ? _j <= _ref : _j >= _ref; i = 0 <= _ref ? ++_j : --_j) {
            if (!(bezier[i] != null)) {
                leftCoord = [i - 1, bezier[i - 1]];
                for (j = _k = i, _ref1 = end[0]; i <= _ref1 ? _k <= _ref1 : _k >= _ref1; j = i <= _ref1 ? ++_k : --_k) {
                    if (bezier[j] != null) {
                        rightCoord = [j, bezier[j]];
                        break;
                    }
                }
                bezier[i] = leftCoord[1] + ((rightCoord[1] - leftCoord[1]) / (rightCoord[0] - leftCoord[0])) * (i - leftCoord[0]);
            }
        }
    }
    if (!(bezier[end[0]] != null)) {
        bezier[end[0]] = bezier[end[0] - 1];
    }
    return bezier;
};

var vignette = function (data, stuff, size, strength) {
    var width = data.width,
        height = data.height,
        bezier, center, end, start;
    if (strength == null) {
        strength = 60;
    }

    if (typeof size === 'string' && size.substr(-1) === "%") {
        size = Math.min(width, height) * Number(size.substr(0, size.length - 1)) / 100;
    }
    strength /= 100;
    center = [width / 2, height / 2];
    start = Math.sqrt(Math.pow(center[0], 2) + Math.pow(center[1], 2));
    end = start - size;
    bezier = buildBezier([0, 1], [30, 30], [70, 60], [100, 80]);
    for (var i = 0, l = data.data.length; i < l; i += 4) {
        var dist, div, loc;
        loc = {x: (i / 4) % width, y: Math.floor((i / 4) / width)};
        dist = Math.sqrt(Math.pow(center[0] - loc.x, 2) + Math.pow(center[1] - loc.y, 2));
        if (dist > end) {
            div = Math.max(1, (bezier[Math.round(((dist - end) / size) * 100)] / 10) * strength);
            data.data[i] = Math.pow(data.data[i] / 255, div) * 255;
            data.data[i + 1] = Math.pow(data.data[i + 1] / 255, div) * 255;
            data.data[i + 2] = Math.pow(data.data[i + 2] / 255, div) * 255;
        }
    }
};

var me = this;
im.bind('filterFullyLoaded', function (e, data) {
    if (data.im.namespace === me.im.namespace) {
        //This is me, start initialization.
    }
});
im.bind('filterChange', function (e, data) {
    if (data.im.namespace === me.im.namespace) {
        im.showLoader('Applying Vignette');

        _.defer(function () {
            // Just apply, there is no variation.

            updateVignette();
            // Apply Filter
        }); // Allow loader to show
    }
});
im.bind('filterApplyExample', function (e, data) {
    if (data.namespace === me.im.namespace) {
        data.image.setFilter(_.partial(vignette, _, _, '50%', '50'));
        im.fire('filterBuiltExample', me, data.elem);
    }
});

var updateVignette = _.debounce(function() {
    $.fn.dialog.showLoader();
    im.activeElement.setFilter(_.partial(vignette, _, _, size_slider.slider('value') + '%', strength_slider.slider('value')));
    im.activeElement.applyFilter();
    $.fn.dialog.hideLoader();

    im.fire('VignetteFilterDidFinish');
    im.fire('filterApplied', me);
}, 250);

var elem = im.controlContext.find('.filter.filter-vignette');
var strength_percent = elem.find('.strength-percent');
var strength_slider = elem.find('.strength-slider').slider({
    value: 60,
    step: 1,
    max: 100,
    min: 1,
    slide: function(event, data) {
        updateVignette();
        strength_percent.text(data.value + '%');
    }
});
var size_percent = elem.find('.size-percent');
var size_slider = elem.find('.size-slider').slider({
    value: 50,
    step: 1,
    max: 100,
    min: 1,
    slide: function(event, data) {
        updateVignette();
        size_percent.text(data.value + '%');
    }
});
