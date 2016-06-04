<?php
defined('C5_EXECUTE') or die("Access Denied.");

$ps = Core::make('helper/form/page_selector');
$al = Core::make('helper/concrete/asset_library');
?>

<fieldset>
    <legend><?php echo t('Files') ?></legend>

    <div class="form-group">
        <?php
        echo $form->label('ccm-b-image', t('Image'));
        echo $al->image('ccm-b-image', 'fID', t('Choose Image'), $bf);
        ?>
    </div>

    <div class="form-group">
        <label class="control-label"><?php echo t('Image Hover')?> <small style="color: #999999; font-weight: 200;"><?php echo t('(Optional)'); ?></small></label>
        <?php
        echo $al->image('ccm-b-image-onstate', 'fOnstateID', t('Choose Image On-State'), $bfo);
        ?>
    </div>
</fieldset>

<fieldset>
    <legend><?php echo t('HTML') ?></legend>

    <div class="form-group">
        <?php
        $options = [
            0 => t('None'),
            1 => t('Another Page'),
            2 => t('External URL'),
        ];

        echo $form->label('imageLinkType', t('Image Link'));
        echo $form->select('linkType', $options, $linkType);
        ?>
    </div>

    <div id="imageLinkTypePage" style="display: none;" class="form-group">
        <?php
        echo $form->label('internalLinkCID', t('Choose Page:'));
        echo $ps->selectPage('internalLinkCID', $internalLinkCID);
        ?>
    </div>

    <div id="imageLinkTypeExternal" style="display: none;" class="form-group">
        <?php
        echo $form->label('externalLink', t('URL'));
        echo $form->text('externalLink', $externalLink);
        ?>
    </div>

    <div class="form-group">
        <?php
        echo $form->label('altText', t('Alt. Text'));
        echo $form->text('altText', $altText, ['maxlength' => 255]);
        ?>
    </div>

    <div class="form-group">
        <?php
        echo $form->label('title', t('Title'));
        echo $form->text('title', $title, ['maxlength' => 255]);
        ?>
    </div>
</fieldset>

<fieldset>
    <legend><?php echo t('Resize Image') ?></legend>

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
        <div class="form-group">
            <?php
            echo $form->label('maxWidth', t('Max Width'));
            echo $form->number('maxWidth', $maxWidth, ['min' => 0]);
            ?>
        </div>

        <div class="form-group">
            <?php
            echo $form->label('maxHeight', t('Max Height'));
            echo $form->number('maxHeight', $maxHeight, ['min' => 0]);
            ?>
        </div>
    </div>
</fieldset>

<script type="text/javascript">
refreshImageLinkTypeControls = function() {
    var linkType = $('#linkType').val();
    $('#imageLinkTypePage').toggle(linkType == 1);
    $('#imageLinkTypeExternal').toggle(linkType == 2);
};

$(document).ready(function() {
    $('#linkType').change(refreshImageLinkTypeControls);

    $('#constrainImage').on('change', function() {
        $('div[data-fields=constrain-image]').toggle($(this).is(':checked'));
    }).trigger('change');

    refreshImageLinkTypeControls();
});
</script>
