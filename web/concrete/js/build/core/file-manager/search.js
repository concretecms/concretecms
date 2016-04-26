!function(global, $) {
    'use strict';

    function ConcreteFileManager($element, options) {
        'use strict';
        var my = this;
        options = $.extend({
            selectMode: 'multiple' // Enables multiple advanced item selection, range click, etc
        }, options);

        ConcreteAjaxSearch.call(my, $element, options);

        my.setupFolders();
    }

    ConcreteFileManager.prototype = Object.create(ConcreteAjaxSearch.prototype);

    ConcreteFileManager.prototype.setupFolders = function() {
        var my = this;
        var $total = my.$element.find('tbody tr');
        my.$element.find('tbody tr').on('dblclick', function() {
            var index = $total.index($(this));
            if (index > -1) {
                var result = my.getResult().items[index];
                if (result) {
                    if (result.isFolder) {
                        my.openFolder(result);
                    }
                }
            }
        });
    }

    ConcreteFileManager.prototype.openFolder = function(folderNode) {
        var my = this;
        var data = my.getSearchData();
        data.push({'name': 'folder', 'value': folderNode.treeNodeID});
        console.log(my.options.result);
        my.ajaxUpdate(my.options.result.baseUrl, data);
    }

    $.fn.concreteFileManager = function(options) {
        return $.each($(this), function(i, obj) {
            new ConcreteFileManager($(this), options);
        });
    };

    global.ConcreteFileManager = ConcreteFileManager;
    //global.ConcreteFileManagerMenu = ConcreteFileManagerMenu;

}(window, $);
