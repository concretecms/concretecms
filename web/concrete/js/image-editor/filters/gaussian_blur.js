var me = this;
me.controls = $(document.createElement('div'));
im.bind('filterFullyLoaded', function (e, data) {
    if (data.namespace === me.namespace) {
        me.applybutton = $($.parseHTML("<button/>")).text('apply').addClass('btn');
        me.radius = $($.parseHTML("<input/>")).val(1);

        me.controls.append(me.radius).append(me.applybutton);
    }
});
im.bind('filterChange', function (e, data) {
    if (data.im.namespace === me.im.namespace) {
        im.showLoader('Applying Blur');

        setTimeout(function () {
            // Just apply, there is no variation.

            im.activeElement.setFilter(Kinetic.Filters.Blur);
            im.activeElement.setFilterRadius(5);
            im.activeElement.applyFilter();

            im.hideLoader();
            im.fire('BlurFilterDidFinish');
            im.fire('filterApplied', me);
            // Apply Filter
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
