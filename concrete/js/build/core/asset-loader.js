!function (global, $) {
    'use strict';

    var ConcreteAssetLoader = {};

    ConcreteAssetLoader.getAssetURL = function (item) {
        var char = (item.indexOf('?') != -1 ? '&ts=' : '?ts='),
            timestamp = new Date().getTime();
        return item + char + timestamp;
    }

    ConcreteAssetLoader.loadJavaScript = function (item) {
        if (!($('script[src*="' + item + '"]').length)) {
            if (!($('script[data-source~="' + item + '"]').length)) {
                var item = this.getAssetURL(item);
                $('head').append('<script type="text/javascript" src="' + item + '"></script>');
            }
        }
    }

    ConcreteAssetLoader.loadCSS = function (item) {
        if (navigator.userAgent.indexOf('MSIE') != -1) {
            // Most reliable way found to force IE to apply dynamically inserted stylesheet across jQuery versions
            var item = this.getAssetURL(item),
                ss = document.createElement('link'),
                hd = document.getElementsByTagName('head')[0];
            ss.type = 'text/css';
            ss.rel = 'stylesheet';
            ss.href = item;
            ss.media = 'screen';
            hd.appendChild(ss);
        } else {
            if (!($('head').children('link[href*="' + item + '"]').length)) {
                // we have to also check to make sure it isn't in a data-source attribute.
                if (!($('head').children('link[data-source~="' + item + '"]').length)) {
                    var item = this.getAssetURL(item);
                    $('head').append('<link rel="stylesheet" media="screen" type="text/css" href="' + item + '" />');
                }
            }
        }
    }

    ConcreteAssetLoader.loadOther = function (item) {
        if (!($('head').children(item).length)) {
            $('head').append(item);
        }
    }

    global.ConcreteAssetLoader = ConcreteAssetLoader;

}
(this, $);


/**
 * @deprecated
 */
ccm_addHeaderItem = function (item, type) {
    if (type == 'CSS') {
        ConcreteAssetLoader.loadCSS(item);
    } else if (type == 'JAVASCRIPT') {
        ConcreteAssetLoader.loadJavaScript(item);
    } else {
        ConcreteAssetLoader.loadOther(item);
    }
}
