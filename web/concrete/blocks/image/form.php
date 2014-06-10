<?php  
defined('C5_EXECUTE') or die("Access Denied.");
$al = Loader::helper('concrete/asset_library');
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
<?
$args = array();
if ($forceImageToMatchDimensions && $maxWidth && $maxHeight) {
	$args['maxWidth'] = $maxWidth;
	$args['maxHeight'] = $maxHeight;
	$args['minWidth'] = $maxWidth;
	$args['minHeight'] = $maxHeight;
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
<hr/>

<fieldset>

<div class="form-group">
	<?=$form->label('linkType', t('Image Link'))?>
	<select name="linkType" id="linkType" class="form-control" style="width: 60%;">
		<option value="0" <?=(empty($externalLink) && empty($internalLinkCID) ? 'selected="selected"' : '')?>><?=t('None')?></option>
		<option value="1" <?=(empty($externalLink) && !empty($internalLinkCID) ? 'selected="selected"' : '')?>><?=t('Another Page')?></option>
		<option value="2" <?=(!empty($externalLink) ? 'selected="selected"' : '')?>><?=t('External URL')?></option>
	</select>
</div>

<div id="linkTypePage" style="display: none;" class="form-group">
	<?=$form->label('internalLinkCID', t('Choose Page:'))?>
	<?= Loader::helper('form/page_selector')->selectPage('internalLinkCID', $internalLinkCID); ?>
</div>

<div id="linkTypeExternal" style="display: none;" class="form-group">
	<?=$form->label('externalLink', t('URL'))?>
	<?= $form->text('externalLink', $externalLink, array('style'=>'width: 60%;')); ?>
</div>


<div class="form-group">
	<?=$form->label('altText', t('Alt. Text'))?>
	<?= $form->text('altText', $altText, array('style'=>'width: 60%;')); ?>
</div>

</fieldset>

<fieldset>
<hr/>

	<? if ($maxWidth == 0) { 
		$maxWidth = '';
	} 
	if ($maxHeight == 0) {
		$maxHeight = '';
	}
	?>

<label><?php echo t('Constrain Image Size'); ?></label>
<div class="form-group">
	<?=$form->label('maxWidth', t('Max Width'))?>
	<?= $form->text('maxWidth', $maxWidth, array('style' => 'width: 60px')); ?>
</div>

<div class="form-group">
	<?=$form->label('maxHeight', t('Max Height'))?>
	<?= $form->text('maxHeight', $maxHeight, array('style' => 'width: 60px')); ?>
</div>

<div class="form-group">
	<?=$form->label('forceImageToMatchDimensions', t('Scale Image'))?>
	<select name="forceImageToMatchDimensions" class="form-control" id="forceImageToMatchDimensions">
		<option value="0" <? if (!$forceImageToMatchDimensions) { ?> selected="selected" <? } ?>><?=t('Automatically')?></option>
		<option value="1" <? if ($forceImageToMatchDimensions == 1) { ?> selected="selected" <? } ?>><?=t('Force Exact Image Match')?></option>
	</select>
</div>

</fieldset>