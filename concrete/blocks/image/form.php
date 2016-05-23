<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$al = Core::make('helper/concrete/asset_library');
$bf = null;
$bfo = null;

if ($controller->getFileID() > 0) {
    $bf = $controller->getFileObject();
}
if ($controller->getFileOnstateID() > 0) {
    $bfo = $controller->getFileOnstateObject();
}
?>

<fieldset>

    <legend><?=t('Files')?></legend>
<?php
$args = array();
$constrain = $maxWidth > 0 || $maxHeight > 0;
if ($maxWidth == 0) {
    $maxWidth = '';
}
if ($maxHeight == 0) {
    $maxHeight = '';
}

?>

<div class="form-group">
	<label class="control-label"><?=t('Image')?></label>
	<?=$al->image('ccm-b-image', 'fID', t('Choose Image'), $bf, $args);?>
</div>
<div class="form-group">
	<label class="control-label"><?=t('Image Hover')?> <small style="color:#999999; font-weight: 200;"><?php echo t('(Optional)'); ?></small></label>
	<?=$al->image('ccm-b-image-onstate', 'fOnstateID', t('Choose Image On-State'), $bfo, $args);?>
</div>

</fieldset>

<fieldset>
    <legend><?=t('HTML')?></legend>

<div class="form-group">
	<?=$form->label('imageLinkType', t('Image Link'))?>
	<select name="linkType" id="imageLinkType" class="form-control">
		<option value="0" <?=(empty($externalLink) && empty($internalLinkCID) ? 'selected="selected"' : '')?>><?=t('None')?></option>
		<option value="1" <?=(empty($externalLink) && !empty($internalLinkCID) ? 'selected="selected"' : '')?>><?=t('Another Page')?></option>
		<option value="2" <?=(!empty($externalLink) ? 'selected="selected"' : '')?>><?=t('External URL')?></option>
	</select>
</div>

<div id="imageLinkTypePage" style="display: none;" class="form-group">
	<?=$form->label('internalLinkCID', t('Choose Page:'))?>
	<?= Core::make('helper/form/page_selector')->selectPage('internalLinkCID', $internalLinkCID); ?>
</div>

<div id="imageLinkTypeExternal" style="display: none;" class="form-group">
	<?=$form->label('externalLink', t('URL'))?>
	<?= $form->text('externalLink', $externalLink); ?>
</div>


<div class="form-group">
	<?=$form->label('altText', t('Alt. Text'))?>
	<?= $form->text('altText', $altText); ?>
</div>

<div class="form-group">
    <?=$form->label('title', t('Title'))?>
    <?= $form->text('title', $title); ?>
</div>

</fieldset>

<fieldset>
    <legend><?=t('Resize Image')?></legend>

    <div class="form-group">
        <div class="checkbox" data-checkbox-wrapper="constrain-image">
            <label><?=$form->checkbox('constrainImage', 1, $constrain)?>
            <?=t('Constrain Image Size')?></label>
        </div>
    </div>

    <div data-fields="constrain-image" style="display: none">
        <div class="form-group">
        <?=$form->label('maxWidth', t('Max Width'))?>
        <?= $form->text('maxWidth', $maxWidth); ?>
        </div>

        <div class="form-group">
            <?=$form->label('maxHeight', t('Max Height'))?>
            <?= $form->text('maxHeight', $maxHeight); ?>
        </div>
    </div>

</fieldset>


<script type="text/javascript">
refreshImageLinkTypeControls = function() {
	var linkType = $('#imageLinkType').val();
	$('#imageLinkTypePage').toggle(linkType == 1);
	$('#imageLinkTypeExternal').toggle(linkType == 2);
}

$(document).ready(function() {
	$('#imageLinkType').change(refreshImageLinkTypeControls);

    $('div[data-checkbox-wrapper=constrain-image] input').on('change', function() {
        if ($(this).is(':checked')) {
            $('div[data-fields=constrain-image]').show();
        } else {
            $('div[data-fields=constrain-image]').hide();
        }
    }).trigger('change');
	refreshImageLinkTypeControls();
});
</script>
