
!function (global, $, _) {
    'use strict';

    var ConcreteMarketplace = function()
    {

    }

    ConcreteMarketplace.prototype = {

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
        },

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
            params = {'mpID': mpID};
            $.getJSON(CCM_TOOLS_PATH + '/marketplace/connect', params, function(resp) {
                jQuery.fn.dialog.hideLoader();
                if (resp.isConnected) {
                    if (!resp.purchaseRequired) {
                        $.fn.dialog.open({
                            title: ccmi18n.community,
                            href:  CCM_TOOLS_PATH + '/marketplace/download?install=1&mpID=' + mpID,
                            width: 500,
                            appendButtons: true,
                            modal: false,
                            height: 400
                        });
                    } else {
                        $.fn.dialog.open({
                            title: ccmi18n.communityCheckout,
                            iframe: true,
                            href:  CCM_TOOLS_PATH + '/marketplace/checkout?mpID=' + mpID,
                            width: '560px',
                            modal: false,
                            height: '400px'
                        });
                    }

                } else {
                    $.fn.dialog.open({
                        title: ccmi18n.community,
                        href:  CCM_TOOLS_PATH + '/marketplace/frame?task=get&mpID=' + mpID,
                        width: '90%',
                        modal: false,
                        height: '70%'
                    });
                }
            });
        }
    }



}(window, jQuery, _);