var me = this;
im.bind('filterFullyLoaded', function (e, data) {
    if (data.im.namespace === me.im.namespace) {
        //This is me, start initialization.
    }
});
im.bind('filterChange', function (e, data) {
    if (data.im.namespace === me.im.namespace) {
        im.showLoader('Applying Grayscale');

        setTimeout(function () {
            // Just apply, there is no variation.

            im.activeElement.setFilter(Kinetic.Filters.Grayscale);
            im.activeElement.applyFilter();

            im.hideLoader();
            im.fire('GrayscaleFilterDidFinish');
            im.fire('filterApplied', me);
            // Apply Filter
        }, 10); // Allow loader to show
    }
});
im.bind('filterApplyExample', function (e, data) {
    if (data.namespace === me.im.namespace) {
        data.image.setFilter(im.filter.grayscale);
        im.fire('filterBuiltExample', me, data.elem);
    }
});
