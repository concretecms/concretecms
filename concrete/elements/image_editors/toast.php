<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Entity\File\Version;

/** @var Version $fileVersion */

?>

<div id="tui-image-editor-container"></div>

<script>
    var imageEditor = new tui.ImageEditor('#tui-image-editor-container', {
        includeUI: {
            loadImage: {
                path: '<?php echo h($fileVersion->getDownloadURL()); ?>',
                name: '<?php echo h($fileVersion->getFileName()); ?>'
            },
            menuBarPosition: 'bottom'
        }
    });

    $('#myDownloadButton').on('click', () => {
        var myImage = imageEditor.toDataURL();
        // Posting myImage to server
        console.log(myImage);
    });
</script>