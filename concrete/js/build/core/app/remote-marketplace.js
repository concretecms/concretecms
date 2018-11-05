/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global CCM_DISPATCHER_FILENAME, ConcreteEvent, ccmi18n */

;(function(global, $) {
    'use strict';

    var ConcreteMarketplace = {
        getMoreInformation: function(mpID)
        {
            $.fn.dialog.showLoader();
            var params = {'mpID': mpID};
            $.concreteAjax({
                method: 'get',
                url: CCM_DISPATCHER_FILENAME + '/ccm/system/marketplace/connect',
                data: params,
                success: function(resp) {
                    $.fn.dialog.hideLoader();
                    if (resp.isConnected) {
                        window.location.href = resp.localURL;
                    } else {
                        window.location.href = CCM_DISPATCHER_FILENAME + '/dashboard/extend/connect';
                    }
                 }
            });
        },

        purchaseOrDownload: function(args)
        {
            var mpID = args.mpID;
            var closeTop = args.closeTop;

            if (!args.onComplete) {
                args.onComplete = function(e, data) {
                    $.fn.dialog.closeTop();
                };
            }

            ConcreteEvent.subscribe('MarketplaceRequestComplete', args.onComplete);

            if (closeTop) {
                $.fn.dialog.closeTop(); // this is here due to a weird safari behavior
            }
            $.fn.dialog.showLoader();
            // first, we check our local install to ensure that we're connected to the
            // marketplace, etc..
            var params = {'mpID': mpID};
            $.getJSON(CCM_DISPATCHER_FILENAME + '/ccm/system/marketplace/connect', params, function(resp) {
                $.fn.dialog.hideLoader();
                if (resp.isConnected) {
                    if (!resp.purchaseRequired) {
                        $.fn.dialog.open({
                            title: ccmi18n.communityDownload,
                            href:  CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/marketplace/download?mpID=' + mpID,
                            width: 500,
                            appendButtons: true,
                            modal: true,
                            height: 400
                        });
                    } else {
                        $.fn.dialog.open({
                            title: ccmi18n.communityCheckout,
                            iframe: true,
                            href:  CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/marketplace/checkout?mpID=' + mpID,
                            width: '560px',
                            modal: true,
                            height: '400px'
                        });
                    }

                } else {
                    window.location.href = CCM_DISPATCHER_FILENAME + '/dashboard/extend/connect';
                }
            });
        }
    };

    global.ConcreteMarketplace = ConcreteMarketplace;

})(window, jQuery);
