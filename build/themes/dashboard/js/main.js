import * as FrontendFoundation from '../../../foundation/js/frontend/foundation';

// !!! Move these imports into cms foundation?

// Import required libraries.
import 'json5';
import * as _ from 'underscore';
import 'jquery.cookie';

import 'jquery-ui/ui/widgets/dialog';
import 'jquery-ui/ui/widgets/datepicker';
import 'jquery-ui/ui/widgets/draggable';
import 'jquery-ui/ui/widgets/droppable';
import 'jquery-ui/ui/widgets/sortable';
import NProgress from 'NProgress';
import 'selectize';
import 'spectrum-colorpicker';
import 'tristate/jquery.tristate';
import 'jquery-text-counter/textcounter';

window.NProgress = NProgress;
window._ = _;

import '../../../foundation/js/cms/events';
import '../../../foundation/js/cms/asset-loader';
import '../../../foundation/js/cms/page-indexer';
import '../../../foundation/js/cms/concrete5';
import '../../../foundation/js/cms/liveupdate/quicksilver';
import '../../../foundation/js/cms/liveupdate/jquery-liveupdate';

// CMS UI Components
import '../../../foundation/js/cms/panels';
import '../../../foundation/js/cms/toolbar';

// Edit Mode
import '../../../foundation/js/cms/edit-mode';

// AJAX Forms and in-page notifications
import 'jquery-form';
import '../../../foundation/js/cms/ajax-request/base';
import '../../../foundation/js/cms/ajax-request/form';
import '../../../foundation/js/cms/ajax-request/block';
import '../../../foundation/js/cms/dialog';
import '../../../foundation/js/cms/alert';

// Progressive operations
import '../../../foundation/js/cms/progressive-operations';

// Search
import '../../../foundation/js/cms/search/base';
import '../../../foundation/js/cms/search/table';
import '../../../foundation/js/cms/search/field-selector';
import '../../../foundation/js/cms/search/preset-selector';

// Tree
import '../../../foundation/js/cms/tree';
import 'jquery.fancytree/dist/modules/jquery.fancytree.glyph';
import 'jquery.fancytree/dist/modules/jquery.fancytree.persist';
import 'jquery.fancytree/dist/modules/jquery.fancytree.dnd';
import 'jquery.fancytree/dist/modules/jquery.fancytree';

// Sitemap
import  '../../../foundation/js/cms/sitemap/sitemap';
import  '../../../foundation/js/cms/in-context-menu';
import  '../../../foundation/js/cms/sitemap/menu';
import  '../../../foundation/js/cms/sitemap/search';
import  '../../../foundation/js/cms/sitemap/selector';

// Users
import '../../../foundation/js/cms/users';

// Express
import '../../../foundation/js/cms/express';

// In-page editable fields
// TBD

// File Manager
import '../../../foundation/js/cms/file-manager/uploader';
import '../../../foundation/js/cms/file-manager/search';
import '../../../foundation/js/cms/file-manager/selector';
import '../../../foundation/js/cms/file-manager/menu';

// attribute helper scripts
import '../../../foundation/js/cms/jquery-awesome-rating';

// Help
import '../../../foundation/js/cms/help/help';

// Calendar component
import '../../../foundation/js/cms/calendar';

// end possible cms foundation?


// possible account domain?
import '../../../foundation/js/account/draft-list';
// note - these require jquery dialog
import '../../../foundation/js/account/notification';

// Dashboard specific scripts.
// Calendar component
import '../../../foundation/js/backend/calendar';

// Other
import './jquery-bootstrap-select-to-button';
import './translator';
import './stacks/menu';
import './remote-marketplace';

var setupResultMessages = function() {
    if ($('#ccm-dashboard-result-message').length > 0) {
        if ($('.ccm-pane').length > 0) {
            var pclass = $('.ccm-pane').parent().attr('class');
            var gpclass = $('.ccm-pane').parent().parent().attr('class');
            var html = $('#ccm-dashboard-result-message').html();
            $('#ccm-dashboard-result-message').html('<div class="' + gpclass + '"><div class="' + pclass + '">' + html + '</div></div>').fadeIn(400);
        }
    } else {
        $("#ccm-dashboard-result-message").fadeIn(200);
    }
};

var setupFavorites = function() {
    var $addFavorite = $('a[data-bookmark-action=add-favorite]'),
        $removeFavorite = $('a[data-bookmark-action=remove-favorite]'),
        url = false,
        $link;

    if ($addFavorite.length) {
        url = CCM_DISPATCHER_FILENAME + '/ccm/system/panels/dashboard/add_favorite';
        $link = $addFavorite;
    } else if ($removeFavorite.length) {
        url = CCM_DISPATCHER_FILENAME + '/ccm/system/panels/dashboard/remove_favorite';
        $link = $removeFavorite;
    }

    if (url) {
        $link.on('click', function(e) {
            e.preventDefault();
            $.concreteAjax({
                dataType: 'json',
                type: 'GET',
                data: {'cID': $(this).attr('data-page-id'), 'ccm_token': $(this).attr('data-token')},
                url: url,
                success: function(r) {
                    if (r.action == 'remove') {
                        $link.attr('data-bookmark-action', 'add-favorite');
                        $link.html('<i class="fa fa-lg fa-bookmark-o"></i>');
                    } else {
                        $link.attr('data-bookmark-action', 'remove-favorite');
                        $link.html('<i class="fa fa-lg fa-bookmark"></i>');
                    }
                    $link.off('click');
                    setupFavorites();
                }
            });
        });
    }
};

var setupDetailsURLs = function() {
    $('table.ccm-search-results-table tr[data-details-url]').each(function() {
        $(this).hover(
            function() {
                $(this).addClass('ccm-search-select-hover');
            },
            function() {
                $(this).removeClass('ccm-search-select-hover');
            }
        )
            .on('click', function() {
                window.location.href = $(this).data('details-url');
            });
    });
    $('div.ccm-details-panel[data-details-url]').each(function() {
        $(this)
            .on('click', function() {
                window.location.href = $(this).data('details-url');
            });
    });
};

var setupTooltips = function() {
    if ($("#ccm-tooltip-holder").length == 0) {
        $('<div />').attr('id','ccm-tooltip-holder').attr('class', 'ccm-ui').prependTo(document.body);
    }
    $('.launch-tooltip').tooltip({'container': '#ccm-tooltip-holder'});
};

var setupDialogs = function() {
    $('.dialog-launch').dialog();

    $('div#ccm-dashboard-page').on('click', '[data-dialog]', function() {
        if ($(this).attr('disabled')) {
            return false;
        }

        var width = $(this).attr('data-dialog-width');
        if (!width) {
            width = 320;
        }
        var height = $(this).attr('data-dialog-height');
        if (!height) {
            height = 'auto';
        }
        var title;
        if ($(this).attr('data-dialog-title')) {
            title = $(this).attr('data-dialog-title');
        } else {
            title = $(this).text();
        }
        var element = 'div[data-dialog-wrapper=' + $(this).attr('data-dialog') + ']';
        jQuery.fn.dialog.open({
            element: element,
            modal: true,
            width: width,
            title: title,
            height: height
        });
    });

};

var setupPrivacyPolicy = function() {

    $('div#ccm-dashboard-page').on('click', 'button[data-action=agree-privacy-policy]', function() {
        $('div.ccm-dashboard-privacy-policy').hide();
        var url = CCM_DISPATCHER_FILENAME + '/ccm/system/accept_privacy_policy';
        $.concreteAjax({
            dataType: 'json',
            data: {'ccm_token': $(this).attr('data-token')},
            type: 'POST',
            url: url,
            success: function(r) {

            }
        });
    });
};

var setupSiteListMenuItem = function() {
    $('select[data-select=ccm-header-site-list]').show().selectize({
        'onItemAdd': function(option) {
            window.location.href = option;
        }
    });
};

var setupSelects = function() {
    $('select[data-select=bootstrap]').bootstrapSelectToButton();
};

setupTooltips();
setupResultMessages();
setupSiteListMenuItem();
setupDialogs();
setupSelects();
setupDetailsURLs();
setupFavorites();
setupPrivacyPolicy();
