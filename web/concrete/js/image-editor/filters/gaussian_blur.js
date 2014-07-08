var me = this;
im.bind('filterFullyLoaded', function (e, data) {
    if (data.namespace === me.namespace) {
        var slider_span = me.parent.find('.filter.filter-gaussian_blur').find('.radius-percent');
        slider = me.parent.find('.filter.filter-gaussian_blur').find('.radius-slider').slider({
            min: 1,
            max: 50,
            step: 1,
            value: 5,
            slide: function(event, data) {
                applyBlur();
                slider_span.text(data.value);
            }
        });
    }
});
im.bind('filterChange', function (e, data) {
    if (data.im.namespace === me.im.namespace) {
        im.showLoader('Applying Blur');

        _.delay(function () {
            applyBlur();
        }, 10); // Allow loader to show
    }
});
im.bind('filterApplyExample', function (e, data) {
    if (data.namespace === me.namespace) {
        data.image.setFilter(Kinetic.Filters.Blur);
        data.image.setFilterRadius(5);
        im.fire('filterBuiltExample', me, data.elem);
    }
});

var applyBlur = _.debounce(function() {
    im.activeElement.setFilter(Kinetic.Filters.Blur);
    im.activeElement.setFilterRadius(slider.slider('value'));
    im.activeElement.applyFilter();

    im.hideLoader();
    im.fire('BlurFilterDidFinish');
    im.fire('filterApplied', me);
}, 250);

var slider;
