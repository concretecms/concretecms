var me = this;
im.bind('filterChange', function (e, data) {
    if (data.im.namespace === me.im.namespace) {
        im.activeElement.clearFilter();
        im.activeElement.draw();

        im.hideLoader();
        im.fire('NoneFilterDidFinish');
        im.fire('filterApplied', me);

    }
});
im.bind('filterApplyExample', function (e, data) {
    if (data.namespace === me.namespace) {
        im.fire('filterBuiltExample', me, data.elem);
    }
});
