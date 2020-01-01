<?php defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Form\Service\DestinationPicker\DestinationPicker $destinationPicker
 * @var array $imageLinkPickers
 * @var string $imageLinkHandle
 * @var mixed $imageLinkValue
 */

$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
$ps = $app->make('helper/form/page_selector');
$al = $app->make('helper/concrete/asset_library');
?>

<fieldset>
    <legend><?php echo t('Files'); ?></legend>

    <div class="form-group">
        <?php
        echo $form->label('ccm-b-image', t('Image'));
        echo $al->image('ccm-b-image', 'fID', t('Choose Image'), $bf);
        ?>
    </div>

    <div id="imageFallback" style="display: none;" class="form-group">
        <label class="control-label"><?php echo t('Image Fallback')?> <small style="color: #999999; font-weight: 200;"><?php echo t('(Optional)'); ?></small></label>
        <i class="fa fa-question-circle launch-tooltip" title="" data-original-title="<?php echo t('If your image is a WebP image, this fallback will be used in browsers that do not support the WebP format.'); ?>"></i>
        <?php
        echo $al->image('ccm-b-image-fallback', 'fFallbackID', t('Choose Image Fallback'), $bff);
        ?>
    </div>

    <div class="form-group">
        <label class="control-label"><?php echo t('Image Hover')?> <small style="color: #999999; font-weight: 200;"><?php echo t('(Optional)'); ?></small></label>
        <i class="fa fa-question-circle launch-tooltip" title="" data-original-title="<?php echo t('The image hover effect requires constraining the image size.'); ?>"></i>
        <?php
        echo $al->image('ccm-b-image-onstate', 'fOnstateID', t('Choose Image On-State'), $bfo);
        ?>
    </div>

    <div id="imageOnStateFallback" style="display: none;" class="form-group">
        <label class="control-label"><?php echo t('Image Hover Fallback')?> <small style="color: #999999; font-weight: 200;"><?php echo t('(Optional)'); ?></small></label>
        <i class="fa fa-question-circle launch-tooltip" title="" data-original-title="<?php echo t('If your hover image is a WebP image, this fallback will be used in browsers that do not support the WebP format.'); ?>"></i>
        <?php
        echo $al->image('ccm-b-image-onstate-fallback', 'fFallbackOnstateID', t('Choose Image Hover Fallback'), $bffo);
        ?>
    </div>
</fieldset>

<fieldset>
    <legend><?php echo t('HTML'); ?></legend>

    <div class="form-group">
        <?= $form->label('imageLink', t('Image Link')) ?>
        <?=
            $destinationPicker->generate(
                'imageLink',
                $imageLinkPickers,
                $imageLinkHandle,
                $imageLinkValue
            )
        ?>
    </div>

    <div id="imageLinkOpenInNewWindow" style="display: none;" class="form-group">
        <div class="checkbox">
            <label>
            <?php
            echo $form->checkbox('openLinkInNewWindow', 'openLinkInNewWindow', isset($openLinkInNewWindow) ? $openLinkInNewWindow : false);
            echo t('Open link in new window');
            ?>
            </label>
        </div>
    </div>

    <div class="form-group">
        <?php
        echo $form->label('altText', t('Alt Text'));
        echo $form->text('altText', isset($altText) ? $altText : '', ['maxlength' => 255]);
        ?>
    </div>

    <div class="form-group">
        <?php
        echo $form->label('title', t('Title'));
        echo $form->text('title', isset($title) ? $title : '', ['maxlength' => 255]);
        ?>
    </div>
</fieldset>

<fieldset>
    <legend><?php echo t('Resize Image'); ?></legend>

    <div class="form-group">
        <div class="checkbox" data-checkbox-wrapper="constrain-image">
            <label>
                <?php
                echo $form->checkbox('constrainImage', 1, $constrainImage);
                echo t('Constrain Image Size');
                ?>
            </label>
        </div>
    </div>

    <div data-fields="constrain-image" style="display: none">
        <div class="well">
            <div class="form-group">
                <div class="checkbox">
                <label>
                    <?php
                    echo $form->checkbox('cropImage', 1, isset($cropImage) ? $cropImage : false);
                    echo t('Crop Image');
                    ?>
                </label>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->label('maxWidth', t('Max Width')); ?>
                <div class="input-group">
                    <?php echo $form->number('maxWidth', isset($maxWidth) ? $maxWidth : '', ['min' => 0]); ?>
                    <span class="input-group-addon"><?php echo t('px'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->label('maxHeight', t('Max Height')); ?>
                <div class="input-group">
                    <?php echo $form->number('maxHeight', isset($maxHeight) ? $maxHeight : '', ['min' => 0]); ?>
                    <span class="input-group-addon"><?php echo t('px'); ?></span>
                </div>
            </div>
        </div>
    </div>
</fieldset>

<script>
$(document).ready(function() {
    $('#imageLink__which')
        .change(function() {
        	$('#imageLinkOpenInNewWindow').toggle($('#imageLink__which').val() !== 'none');
        })
        .trigger('change')
    ;
    $('#constrainImage').on('change', function() {
        $('div[data-fields=constrain-image]').toggle($(this).is(':checked'));

        if (!$(this).is(':checked')) {
            $('#cropImage').prop('checked', false);
            $('#maxWidth').val('');
            $('#maxHeight').val('');
        }
    }).trigger('change');
    $('#fID')
        .change(function() {
            var ext = getImageExtension($(this));
            $('#imageFallback').toggle(ext == 'webp')
        })
        .trigger('change')
    ;
    $('#fOnstateID')
        .change(function() {
            var ext = getImageExtension($(this));
            $('#imageOnStateFallback').toggle(ext == 'webp')
        })
        .trigger('change')
    ;
    function getImageExtension(el) {
        el = el || false;
        var ext = '';
        if (el && el.val()) {
            ext = el
                    .siblings('.ccm-file-selector-file-selected-thumbnail')
                    .eq(0)
                    .find('img')
                    .attr('src')
                    .split('.')
                    .pop();
        }
        return ext;
    }
});
</script>
