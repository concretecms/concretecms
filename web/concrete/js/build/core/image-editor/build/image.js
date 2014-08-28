if (settings.src) {
    im.showLoader(ccmi18n_imageeditor.loadingImage);
    var img = new Image(), controlSetsLoaded = false;
    im.bind('ControlSetsLoaded', function () {
        controlSetsLoaded = true;
    });

    im.bind('load', function imageLoaded() {
        if (!im.strictSize) {
            im.saveWidth = img.width;
            im.saveHeight = img.height;
            im.fire('saveSizeChange');
            im.buildBackground();
        }
        var center = {
            x: Math.floor(im.center.x - (img.width / 2)),
            y: Math.floor(im.center.y - (img.height / 2))
        };
        var image = new Kinetic.Image({
            image: img,
            x: 0,
            y: 0
        });
        image.setPosition(center);
        im.addElement(image, 'image');
        _.defer(function () {
            im.fire('imageload');
        });
        function activate() {
            _.defer(function activateImageElement() {
                im.stage.draw();
                im.setActiveElement(image);
                im.fire('changeActiveAction', im.controlSetNamespaces[0]);
            });
        }

        if (controlSetsLoaded) {
            activate();
        } else {
            im.bind('ControlSetsLoaded', activate);
        }
    }, img);

    img.src = settings.src;
} else {
    im.fire('imageload');
}
