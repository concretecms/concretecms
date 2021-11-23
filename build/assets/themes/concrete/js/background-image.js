import Backstretch from 'jquery-backstretch';

$(function () {

    var backgroundImage = $('#ccm-page-background-credit').data('background-image'),
        backgroundUrl = $('#ccm-page-background-credit').data('background-url'),
        backgroundFeed = $('#ccm-page-background-credit').data('background-feed'),
        backgroundFade = $('#ccm-page-background-credit').data('background-fade');

    setTimeout(function () {

        var fade_div = $('<div/>').css({
            position: 'absolute',
            top: 0,
            left: 0,
            width: '100%'
        }).prependTo('body').height('200px');

        fade_div.hide()
            .append(
                $('<img/>')
                    .css({width: '100%', height: '100%'})
                    .attr('src', backgroundFade))
            .fadeIn();
    }, 0);
    
    if (backgroundImage !== 'none' && !backgroundUrl) {
        var date = new Date(), shown = false, info;
        date = date.toISOString().split('T')[0].replace(/-/g, '');
        var image = date + '.jpg';
        $.getJSON(CCM_DISPATCHER_FILENAME + '/ccm/system/backend/dashboard/get_image_data', {'image': image}, function (responseData) {
            info = responseData;
        });
        $(window).on('backstretch.show', function () {
            shown = true;
            if (info) {
                $('div.ccm-page-background-credit').fadeIn().find('div.ccm-page-background-photo-credit').children().attr('href', info.link).text(info.author.join());
            }
        });
        $.backstretch(backgroundFeed + '/'  + image, {
            fade: 500
        });
    } else {
        if (backgroundUrl) {
            $.backstretch(backgroundUrl, {fade: 500});
        }
    }


});
