
!function (global, $, _) {
    'use strict';

    var ConcreteMarketplace = {

        updatesShowMore: function(obj) {
            $(obj).parent().hide();
            $(obj).parent().parent().find('.ccm-marketplace-update-changelog').css('max-height', 'none');
        },

        getMoreInformation: function(mpID)
        {
            jQuery.fn.dialog.showLoader();
            var params = {'mpID': mpID};
            $.concreteAjax({
                method: 'get',
                url: CCM_DISPATCHER_FILENAME + '/ccm/system/marketplace/connect',
                data: params,
                success: function(resp) {
                    jQuery.fn.dialog.hideLoader();
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
                    jQuery.fn.dialog.closeTop();
                }
            }

            ConcreteEvent.subscribe('MarketplaceRequestComplete', args.onComplete);

            if (closeTop) {
                jQuery.fn.dialog.closeTop(); // this is here due to a weird safari behavior
            }
            jQuery.fn.dialog.showLoader();
            // first, we check our local install to ensure that we're connected to the
            // marketplace, etc..
            var params = {'mpID': mpID};
            $.getJSON(CCM_DISPATCHER_FILENAME + '/ccm/system/marketplace/connect', params, function(resp) {
                jQuery.fn.dialog.hideLoader();
                if (resp.isConnected) {
                    if (!resp.purchaseRequired) {
                        $.fn.dialog.open({
                            title: ccmi18n.communityDownload,
                            href:  CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/marketplace/download?mpID=' + mpID,
                            width: 500,
                            appendButtons: true,
                            modal: false,
                            height: 400
                        });
                    } else {
                        $.fn.dialog.open({
                            title: ccmi18n.communityCheckout,
                            iframe: true,
                            href:  CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/marketplace/checkout?mpID=' + mpID,
                            width: '560px',
                            modal: false,
                            height: '400px'
                        });
                    }

                } else {
                    window.location.href = CCM_DISPATCHER_FILENAME + '/dashboard/extend/connect';
                }
            });
        }
    }


    global.ConcreteMarketplace = ConcreteMarketplace;

}(window, jQuery, _);