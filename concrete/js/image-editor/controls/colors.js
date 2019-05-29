function setSaveAreaBgColor(color) {
    if (color) {
        im.saveArea.setFill(color.toHexString());
    } else {
        im.saveArea.setFill('');
    }
    im.saveArea.draw();
}

$(function () {
    var defaultColor = '';
    if (im.settings && im.settings.saveAreaBackgroundColor) {
        defaultColor = im.settings.saveAreaBackgroundColor;
    }
    $('[data-color-picker]').spectrum({
        color: defaultColor,
        className: 'ccm-widget-colorpicker',
        showInitial: true,
        showInput: true,
        allowEmpty: true,
        preferredFormat: 'hex',
        showAlpha: false,
        move: setSaveAreaBgColor,
        hide: setSaveAreaBgColor
    });
});