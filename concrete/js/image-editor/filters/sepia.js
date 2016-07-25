var me = this;
im.bind('filterFullyLoaded', function (e, data) {
    if (data.im.namespace === me.im.namespace) {
        //This is me, start initialization.
    }
});
im.bind('filterChange', function (e, data) {
    if (data.im.namespace === me.im.namespace) {
        im.showLoader('Applying Sepia');

        setTimeout(function () {
            // Just apply, there is no variation.

            im.activeElement.setFilter(im.filter.sepia);
            im.activeElement.applyFilter();

            im.hideLoader();
            im.fire('SepiaFilterDidFinish');
            im.fire('filterApplied', me);
            // Apply Filter
        }, 10); // Allow loader to show
    }
});
im.bind('filterApplyExample', function (e, data) {
    if (data.namespace === me.namespace) {
        data.image.setFilter(im.filter.sepia);
        im.fire('filterBuiltExample', me, data.elem);
    }
});
