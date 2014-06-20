function toggleCustomPage(value) {
    if (value == "custom") {
        $("#ccm-autonav-page-selector").css('display', 'block');
    } else {
        $("#ccm-autonav-page-selector").hide();
    }
}

function toggleSubPageLevels(value) {
    if (value == "none") {
        $("#displaySubPageLevels").get(0)[0].selected = true;
        $("#displaySubPageLevels").get(0).disabled = true;
        document.getElementById("displaySubPageLevels").disabled = true;
    } else {
        $("#displaySubPageLevels").get(0).disabled = false;
    }
}

function toggleSubPageLevelsNum(value) {
    if (value == "custom") {
        $("#divSubPageLevelsNum").css('display', 'block');
    } else {
        $("#divSubPageLevelsNum").hide();
    }
}


var preview_container, preview_loader, preview_render;

var autonav = {
    showLoader: function () {
        preview_loader.show();
        preview_render.hide();
    },

    hideLoader: function () {
        preview_loader.hide();
        preview_render.show();
    }
};

var request = null, url = null;
reloadPreview = function () {
    if (!url) {
        url = $("input[name=autonavPreviewPane]").val();
    }

    orderBy = $("select[name=orderBy]").val();
    displayPages = $("select[name=displayPages]").val();
    displaySubPages = $("select[name=displaySubPages]").val();
    displaySubPageLevels = $("select[name=displaySubPageLevels]").val();
    displaySubPageLevelsNum = $("input[name=displaySubPageLevelsNum]").val();
    displayUnavailablePages = $("input[name=displayUnavailablePages]").val();
    displayPagesCID = $("input[name=displayPagesCID]").val();
    displayPagesIncludeSelf = $("input[name=displayUnavailablePages]").val();

    if (displayPages == "custom" && !displayPagesCID) {
        return false;
    }

    //$("#ccm-dialog-throbber").css('visibility', 'visible');

    var loaderHTML = '<div style="padding: 20px; text-align: center"><img src="' + CCM_IMAGE_PATH + '/throbber_white_32.gif"></div>';
    $('#ccm-autonavPane-preview').html(loaderHTML);

    autonav.showLoader();

    if (request) {
        request.abort();
    }
    request = $.post(url, {
        orderBy: orderBy,
        cID: $("input[name=autonavCurrentCID]").val(),
        displayPages: displayPages,
        displaySubPages: displaySubPages,
        displaySubPageLevels: displaySubPageLevels,
        displaySubPageLevelsNum: displaySubPageLevelsNum,
        displayUnavailablePages: displayUnavailablePages,
        displayPagesCID: displayPagesCID,
        displayPagesIncludeSelf: displayPagesIncludeSelf
    }, function (resp) {
        preview_render.empty().append($(resp));
        autonav.hideLoader();
        request = null;
    });
};

function reloadCCMCall() {
    reloadPreview();
}


autonavShowPane = function (pane) {
    $('ul#ccm-autonav-tabs li').each(function (num, el) {
        $(el).removeClass('active');
    });
    $(document.getElementById('ccm-autonav-tab-' + pane).parentNode).addClass('active');
    $('div.ccm-autonavPane').each(function (num, el) {
        el.style.display = 'none';
    });
    $('#ccm-autonavPane-' + pane).css('display', 'block');
    if (pane == 'preview') reloadPreview(document.blockForm);
};

Concrete.event.bind('autonav.edit.open', function() {
    preview_container = $('div.autonav-form').find('div.preview'),
    preview_loader = preview_container.children('.loader'),
    preview_render = preview_container.children('.render');

    preview_container.closest('form').change(function () {
        reloadPreview();
    });

    _.defer(function() {
        reloadPreview();
    });
});
