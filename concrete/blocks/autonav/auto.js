function toggleCustomPage(value) {
    if (value == "custom") {
        $("#ccm-autonav-page-selector", container).slideDown();
    } else {
        $("#ccm-autonav-page-selector", container).slideUp();
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
        $("#divSubPageLevelsNum").slideDown();
    } else {
        $("#divSubPageLevelsNum").slideUp();
    }
}


var container, preview_container, preview_loader, preview_render;

var autonav = {
    showLoader: function (element) {
        var position = element.position(),
            group = element.closest('.form-group'),
            top = element.position().top,
            left = group.position().left + group.width() + 10;

        preview_loader.css({
            left: left,
            top: top
        }).show();
    },

    hideLoader: function () {
        preview_loader.hide();
    }
};

var request = null, url = null;
function reloadPreview(event) {
    if (!url) {
        url = $("input[name=autonavPreviewPane]").val();
    }
    orderBy = $("select[name=orderBy]", container).val();
    displayPages = $("select[name=displayPages]", container).val();
    displaySubPages = $("select[name=displaySubPages]", container).val();
    displaySubPageLevels = $("select[name=displaySubPageLevels]", container).val();
    displaySubPageLevelsNum = $("input[name=displaySubPageLevelsNum]", container).val();
    displayUnavailablePages = $("input[name=displayUnavailablePages]", container).is(':checked') ? 1 : 0;
    displayPagesCID = $("input[name=displayPagesCID]", container).val();
    displayPagesIncludeSelf = displayUnavailablePages;

    if (displayPages == "custom" && !displayPagesCID) {
        return false;
    }

    if (event && event.target) {
        autonav.showLoader($(event.target));
    }

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
}

Concrete.event.bind('autonav.edit.open', function() {
    container = $('div.autonav-form');
    preview_container = $('div.autonav-preview');
    preview_loader = container.find('div.loader');
    preview_render = preview_container.children('div.render');

    preview_container.closest('form').change(function (e) {
        reloadPreview(e);
    });

    container.find('input[name=displaySubPageLevelsNum]').keyup(_.debounce(function(e) {
        var element = $(this).parent();
        _.defer(function() {
            reloadPreview();
            autonav.showLoader(element);
        });
    }, 500));

    _.defer(function() {
        reloadPreview();
    });
});

ConcreteEvent.subscribe('SitemapSelectPage', function(){
    _.defer(function() {
        reloadPreview();
        autonav.showLoader($("#ccm-autonav-page-selector", container));
    });
});