(function () {
    CKEDITOR.plugins.add('concrete5link', {
        requires: 'link',
        init: function (editor) {
            CKEDITOR.on('dialogDefinition', function (ev) {
                // Take the dialog name and its definition from the event data.
                var dialogName = ev.data.name;
                var dialogDefinition = ev.data.definition;

                // Check if the definition is from the dialog window you are interested in (the "Link" dialog window).
                if (dialogName == 'link') {
                    // Get a reference to the "Link Info" tab.
                    var infoTab = dialogDefinition.getContents('info');
                    if (infoTab.get('sitemapBrowse') === null) {
                        infoTab.add(
                            {
                                type: 'button',
                                id: 'sitemapBrowse',
                                label: 'Sitemap',
                                title: 'Sitemap',
                                onClick: function () {
                                    jQuery.fn.dialog.open({
                                        width: '90%',
                                        height: '70%',
                                        modal: false,
                                        title: ccmi18n_sitemap.choosePage,
                                        href: CCM_TOOLS_PATH + '/sitemap_search_selector'
                                    });
                                    ConcreteEvent.unsubscribe('SitemapSelectPage');
                                    ConcreteEvent.subscribe('SitemapSelectPage', function (e, data) {
                                        jQuery.fn.dialog.closeTop();
                                        var element = dialogDefinition.dialog.getContentElement('info', 'url');
                                        if (element) {
                                            element.setValue(CCM_APPLICATION_URL + '/index.php?cID=' + data.cID);
                                        }
                                    });
                                }
                            },
                            'browse'
                        );
                    }
                }
            });
        }
    });
})();