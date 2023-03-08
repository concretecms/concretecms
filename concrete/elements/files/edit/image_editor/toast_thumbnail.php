<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Asset\Output\JavascriptFormatter;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Http\ResponseAssetGroup;

/** @var Version $fileVersion */

$group = ResponseAssetGroup::get();
$formatter = new JavascriptFormatter();
$output = $group->getAssetsToOutput();
foreach ($output as $position => $assets) {
    foreach ($assets as $asset) {
        if ($asset instanceof Concrete\Core\Asset\Asset) {
            print $formatter->output($asset);
        }
    }
}
?>
<style type="text/css">
 .border {
     border: 1px solid black;
 }
 .body-container {
     width: 1000px;
 }
 .tui-image-editor-controls {
     min-height: 250px;
 }
 .menu {
     padding: 0;
     margin-bottom: 5px;
     text-align: center;
     color: #544b61;
     font-weight: 400;
     list-style-type: none;
     user-select: none;
     -moz-user-select: none;
     -ms-user-select: none;
     -webkit-user-select: none;
 }
 .logo {
     margin: 0 auto;
     width: 300px;
     vertical-align: middle;
 }
 .header .name {
     padding: 10px;
     line-height: 50px;
     font-size: 30px;
     font-weight: 100;
     vertical-align: middle;
 }
 .header .menu {
     display: inline-block;
 }
 .menu-item {
     padding: 10px;
     display: inline-block;
     cursor: pointer;
     vertical-align: middle;
 }
 .menu-item a {
     text-decoration: none;
 }
 .menu-item.no-pointer {
     cursor: default;
 }
 .menu-item.active,
 .menu-item:hover {
     background-color: #f3f3f3;
 }
 .menu-item.disabled {
     cursor: default;
     color: #bfbebe;
 }
 .align-left-top {
     text-align: left;
     vertical-align: top;
 }
 .range-narrow {
     width: 80px;
 }
 .sub-menu-container {
     font-size: 14px;
     margin-bottom: 1em;
     display: none;
 }
 .tui-image-editor {
     height: 500px;
 }
 .tui-image-editor-canvas-container {
     margin: 0 auto;
     top: 50%;
     transform: translateY(-50%);
     -ms-transform: translateY(-50%);
     -moz-transform: translateY(-50%);
     -webkit-transform: translateY(-50%);
     border: 1px dashed black;
     overflow: hidden;
 }
 .tui-colorpicker-container {
     margin: 5px auto 0;
 }
 .tui-colorpicker-palette-toggle-slider {
     display: none;
 }
 .input-wrapper {
     position: relative;
 }
 .input-wrapper input {
     cursor: pointer;
     position: absolute;
     font-size: 999px;
     left: 0;
     top: 0;
     opacity: 0;
     width: 100%;
     height: 100%;
     overflow: hidden;
 }
 .btn-text-style {
     padding: 5px;
     margin: 3px 1px;
     border: 1px dashed #bfbebe;
     outline: 0;
     background-color: #eee;
     cursor: pointer;
 }
 .icon-text {
     font-size: 20px;
 }
 .select-line-type {
     outline: 0;
     vertical-align: middle;
 }
 #tui-color-picker {
     display: inline-block;
     vertical-align: middle;
 }
 #tui-text-palette {
     display: none;
     position: absolute;
     padding: 10px;
     border: 1px solid #bfbebe;
     background-color: #fff;
     z-index: 9999;
 }
</style>
<div class="tui-image-editor-controls">
    <ul class="menu">
        <li class="menu-item disabled" id="btn-undo">Undo</li>
        <li class="menu-item disabled" id="btn-redo">Redo</li>
        <li class="menu-item" id="btn-crop">Crop</li>
        <li class="menu-item" id="btn-flip">Flip</li>
        <li class="menu-item" id="btn-rotation">Rotation</li>
        <li class="menu-item" id="btn-zoom">Zoom</li>
    </ul>
    <div class="sub-menu-container" id="crop-sub-menu">
        <ul class="menu">
            <li class="menu-item" id="btn-apply-crop">Apply</li>
            <li class="menu-item" id="btn-cancel-crop">Cancel</li>
        </ul>
    </div>
    <div class="sub-menu-container" id="flip-sub-menu">
        <ul class="menu">
            <li class="menu-item" id="btn-flip-x">FlipX</li>
            <li class="menu-item" id="btn-flip-y">FlipY</li>
            <li class="menu-item" id="btn-reset-flip">Reset</li>
            <li class="menu-item close">Close</li>
        </ul>
    </div>
    <div class="sub-menu-container" id="zoom-sub-menu">
        <ul class="menu">
            <li class="menu-item" id="btn-zoom-in">Zoom In</li>
            <li class="menu-item" id="btn-zoom-out">Zoom Out</li>
            <li class="menu-item" id="btn-reset-zoom">Reset</li>
            <li class="menu-item close">Close</li>
        </ul>
    </div>
    <div class="sub-menu-container" id="rotation-sub-menu">
        <ul class="menu">
            <li class="menu-item" id="btn-rotate-clockwise">Clockwise(30)</li>
            <li class="menu-item" id="btn-rotate-counter-clockwise">Counter-Clockwise(-30)</li>
            <li class="menu-item no-pointer">
                <label>
                    Range input
                    <input id="input-rotation-range" type="range" min="-360" value="0" max="360" />
                </label>
            </li>
            <li class="menu-item close">Close</li>
        </ul>
    </div>
</div>

<div class="tui-image-editor"></div>

<style>
 .tui-image-editor-download-btn, .tui-image-editor-header-buttons {
     display: none !important;
 }
</style>

<div class="dialog-buttons">
    <button class="tui-image-editor-close-btn btn btn-default float-start">
        <?php echo t('Cancel') ?>
    </button>

    <button class="tui-image-editor-save-btn btn btn-primary" disabled="disabled">
        <?php echo t("Save"); ?>
    </button>
</div>

<script>
 $(document).ready(function() {
     /* eslint-disable vars-on-top,no-var,strict,prefer-template,prefer-arrow-callback,prefer-destructuring,object-shorthand,require-jsdoc,complexity,prefer-const,no-unused-vars */
     var rImageType = /data:(image\/.+);base64,/;
     var activeObjectId;

     // Buttons
     var $btns = $('.menu-item');
     var $btnsActivatable = $btns.filter('.activatable');
     var $btnUndo = $('#btn-undo');
     var $btnRedo = $('#btn-redo');

     var $btnCrop = $('#btn-crop');
     var $btnFlip = $('#btn-flip');
     var $btnRotation = $('#btn-rotation');
     var $btnZoom = $('#btn-zoom');

     var $btnApplyCrop = $('#btn-apply-crop');
     var $btnCancelCrop = $('#btn-cancel-crop');
     var $btnZoomOut = $('#btn-zoom-out');
     var $btnZoomIn = $('#btn-zoom-in');
     var $btnResetZoom = $('#btn-reset-zoom');
     var $btnFlipX = $('#btn-flip-x');
     var $btnFlipY = $('#btn-flip-y');
     var $btnResetFlip = $('#btn-reset-flip');
     var $btnRotateClockwise = $('#btn-rotate-clockwise');
     var $btnRotateCounterClockWise = $('#btn-rotate-counter-clockwise');
     var $btnClose = $('.close');

     // Input etc.
     var $inputRotationRange = $('#input-rotation-range');

     // Sub menus
     var $displayingSubMenu = $();
     var $cropSubMenu = $('#crop-sub-menu');
     var $flipSubMenu = $('#flip-sub-menu');
     var $zoomSubMenu = $('#zoom-sub-menu');
     var $rotationSubMenu = $('#rotation-sub-menu');

     var $saveBtn = $('.tui-image-editor-save-btn');

     // Image editor
     var imageEditor = new window.tuiImageEditor('.tui-image-editor', {
         cssMaxWidth: 700,
         cssMaxHeight: 500,
         selectionStyle: {
             cornerSize: 20,
             rotatingPointOffset: 70,
         },
     });


     function base64ToBlob(data) {
         var mimeString = '';
         var raw, uInt8Array, i, rawLength;

         raw = data.replace(rImageType, function (header, imageType) {
             mimeString = imageType;

             return '';
         });

         raw = atob(raw);
         rawLength = raw.length;
         uInt8Array = new Uint8Array(rawLength); // eslint-disable-line

         for (i = 0; i < rawLength; i += 1) {
             uInt8Array[i] = raw.charCodeAt(i);
         }

         return new Blob([uInt8Array], { type: mimeString });
     }

     function resizeEditor() {
         var $editor = $('.tui-image-editor');
         var $container = $('.tui-image-editor-canvas-container');
         var height = parseFloat($container.css('max-height'));

         $editor.height(height);
     }

     function showSubMenu(type) {
         var $submenu;

         switch (type) {
             case 'shape':
                 $submenu = $drawShapeSubMenu;
                 break;
             case 'icon':
                 $submenu = $iconSubMenu;
                 break;
             case 'text':
                 $submenu = $textSubMenu;
                 break;
             default:
                 $submenu = 0;
         }

         $displayingSubMenu.hide();
         $displayingSubMenu = $submenu.show();
     }


     // Attach image editor custom events
     imageEditor.on({
         objectAdded: function (objectProps) {
         },
         undoStackChanged: function (length) {
             if (length) {
                 $btnUndo.removeClass('disabled');
             } else {
                 $btnUndo.addClass('disabled');
             }
             resizeEditor();
         },
         redoStackChanged: function (length) {
             if (length) {
                 $btnRedo.removeClass('disabled');
             } else {
                 $btnRedo.addClass('disabled');
             }
             resizeEditor();
         },
     });

     // Attach button click event listeners
     $btns.on('click', function () {
         $btnsActivatable.removeClass('active');
     });

     $btnsActivatable.on('click', function () {
         $(this).addClass('active');
     });

     $btnUndo.on('click', function () {
         $displayingSubMenu.hide();

         if (!$(this).hasClass('disabled')) {
             imageEditor.discardSelection();
             imageEditor.undo();
         }
     });

     $btnRedo.on('click', function () {
         $displayingSubMenu.hide();

         if (!$(this).hasClass('disabled')) {
             imageEditor.discardSelection();
             imageEditor.redo();
         }
     });

     $btnCrop.on('click', function () {
	 var canvasSize = imageEditor.getCanvasSize(),
	     thumbnailHeight = 50,
	     thumbnailWidth = 50,
	     cropper,
	     activeObject;
         imageEditor.startDrawingMode('CROPPER');
         imageEditor.setCropzoneRect(1);
	 cropper = imageEditor._graphics._componentMap.CROPPER;
	 cropper._onFabricMouseMove = () => {}; /* override to disallow creating a new crop zone */
         cropper._cropzone.width = thumbnailWidth;
         cropper._cropzone.height = thumbnailHeight;
	 cropper._cropzone.top = (canvasSize.height / 2) - (thumbnailHeight / 2);
	 cropper._cropzone.left = (canvasSize.width / 2) - (thumbnailWidth / 2);
	 activeObject = imageEditor._graphics.getActiveObject();
	 activeObject.lockScalingY = true;
         activeObject.lockScalingX = true;
         $displayingSubMenu.hide();
         $displayingSubMenu = $cropSubMenu.show();
     });

     $btnFlip.on('click', function () {
         imageEditor.stopDrawingMode();
         $displayingSubMenu.hide();
         $displayingSubMenu = $flipSubMenu.show();
     });

     $btnRotation.on('click', function () {
         imageEditor.stopDrawingMode();
         $displayingSubMenu.hide();
         $displayingSubMenu = $rotationSubMenu.show();
     });

     $btnZoom.on('click', function () {
         $displayingSubMenu.hide();
         $displayingSubMenu = $zoomSubMenu.show();
     });

     $btnClose.on('click', function () {
         imageEditor.stopDrawingMode();
         $displayingSubMenu.hide();
     });

     $btnApplyCrop.on('click', function () {
         imageEditor.crop(imageEditor.getCropzoneRect()).then(function () {
             imageEditor.stopDrawingMode();
             resizeEditor();
	     imageEditor._graphics._componentMap.ZOOM.stopHandMode();
	     $saveBtn.attr('disabled', false);
         });
     });

     $btnCancelCrop.on('click', function () {
         imageEditor.stopDrawingMode();
     });

     $btnZoomIn.on('click', function () {
	 var canvasSize = imageEditor.getCanvasSize();
         imageEditor.zoom({x: canvasSize.width/2, y: canvasSize.height/2, zoomLevel: imageEditor._graphics.getCanvas().getZoom() + 1});
	 imageEditor._graphics._componentMap.ZOOM.startHandMode();
	 imageEditor.changeCursor('move');
     });

     $btnZoomOut.on('click', function () {
	 var canvasSize = imageEditor.getCanvasSize(),
	     currentZoomLevel = imageEditor._graphics.getCanvas().getZoom(),
	     newZoomLevel = (currentZoomLevel == 1) ? 1 : currentZoomLevel -= 1;
	 if (newZoomLevel == 1) {
	     imageEditor._graphics._componentMap.ZOOM.stopHandMode();
	     imageEditor.changeCursor('default');
	 }
	 imageEditor.zoom({x: canvasSize.width/2, y: canvasSize.height/2, zoomLevel: newZoomLevel});
     });

     $btnResetZoom.on('click', function () {
	 imageEditor.changeCursor('default');
         imageEditor.resetZoom();
     });

     $btnFlipX.on('click', function () {
         imageEditor.flipX();
     });

     $btnFlipY.on('click', function () {
         imageEditor.flipY();
     });

     $btnResetFlip.on('click', function () {
         imageEditor.resetFlip();
     });

     $btnRotateClockwise.on('click', function () {
         imageEditor.rotate(30);
     });

     $btnRotateCounterClockWise.on('click', function () {
         imageEditor.rotate(-30);
     });

     $inputRotationRange.on('mousedown', function () {
         var changeAngle = function () {
	     imageEditor.setAngle(parseInt($inputRotationRange.val(), 10))['catch'](function () {});
         };
         $(document).on('mousemove', changeAngle);
         $(document).on('mouseup', function stopChangingAngle() {
	     $(document).off('mousemove', changeAngle);
	     $(document).off('mouseup', stopChangingAngle);
         });
     });

     $inputRotationRange.on('change', function () {
         imageEditor.setAngle(parseInt($inputRotationRange.val(), 10))['catch'](function () {});
     });

     imageEditor.loadImageFromURL(
         '<?php echo h($fileVersion->getURL()); ?>',
         '<?php echo h($fileVersion->getFileName()); ?>'
     ).then(function () {
         imageEditor.clearUndoStack();
     });

     $(".tui-image-editor-close-btn").on('click', function () {
         $.fn.dialog.closeTop();
     });

     $('.tui-image-editor-save-btn').on('click', function () {
         var url = CCM_DISPATCHER_FILENAME + '/ccm/system/file/edit/save/<?php echo h($fileVersion->getFileID()); ?>';

         $.concreteAjax({
	     dataType: 'json',
	     data: {
                 token: CCM_SECURITY_TOKEN,
                 imageData: imageEditor.toDataURL()
	     },
	     type: 'POST',
	     url: url,
	     success: function (r) {
                 $.fn.dialog.closeTop();
	     }
         });
     });
 });
</script>

