
!function (global, $, _) {
    'use strict';

    var ConcreteMarketplace = {
        /*
        testConnection: function(onComplete, task, mpID)
        {
            var mpIDStr = '';
            if (mpID) {
                mpIDStr = '&mpID=' + mpID;
            }

            if (!task) {
                task = '';
            }

            params = {'mpID': mpID};

            $.getJSON(CCM_TOOLS_PATH + '/marketplace/connect', params, function(resp) {
                if (resp.isConnected) {
                    onComplete();
                } else {
                    $.fn.dialog.open({
                        title: ccmi18n.community,
                        href:  CCM_TOOLS_PATH + '/marketplace/frame?task=' + task + mpIDStr,
                        width: '90%',
                        modal: false,
                        height: '70%'
                    });
                    return false;
                }
            });
        },

        getDetails: function(mpID)
        {
            jQuery.fn.dialog.showLoader();
            $("#ccm-intelligent-search-results").hide();
            ccm_testMarketplaceConnection(function() {
                $.fn.dialog.open({
                    title: ccmi18n.community,
                    href:  CCM_TOOLS_PATH + '/marketplace/details?mpID=' + mpID,
                    width: 820,
                    appendButtons: true,
                    modal: false,
                    height: 640
                });
            }, 'get_item_details', mpID);
        },*/

        purchaseOrDownload: function(args)
        {
            var mpID = args.mpID;
            var closeTop = args.closeTop;

            this.onComplete = function() { }

            if (args.onComplete) {
                ccm_getMarketplaceItem.onComplete = args.onComplete;
            }

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
                            title: ccmi18n.community,
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
                            href:  CCM_TOOLS_PATH + '/ccm/system/dialogs/marketplace/checkout?mpID=' + mpID,
                            width: '560px',
                            modal: false,
                            height: '400px'
                        });
                    }

                } else {
                    $.fn.dialog.open({
                        title: ccmi18n.community,
                        href:  CCM_TOOLS_PATH + '/ccm/system/dialogs/marketplace/frame?mpID=' + mpID,
                        width: '90%',
                        modal: false,
                        height: '70%'
                    });
                }
            });
        }
    }


    global.ConcreteMarketplace = ConcreteMarketplace;

}(window, jQuery, _);