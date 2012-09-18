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
<div class="ccm-block-field-group">
<h4><?=t('Image to Display')?></h4><br/>
<?
$args = array();
if ($forceImageToMatchDimensions && $maxWidth && $maxHeight) {
	$args['maxWidth'] = $maxWidth;
	$args['maxHeight'] = $maxHeight;
	$args['minWidth'] = $maxWidth;
	$args['minHeight'] = $maxHeight;
}
?>

<div class="control-group">
	<label class="control-label"><?=t('Image')?></label>
	<div class="controls">	
		<?=$al->image('ccm-b-image', 'fID', t('Choose Image'), $bf, $args);?>
	</div>
</div>
<div class="control-group">
	<label class="control-label"><?=t('Image On-State')?></label>
	<div class="controls">	
		<?=$al->image('ccm-b-image-onstate', 'fOnstateID', t('Choose Image On-State'), $bfo, $args);?>
	</div>
</div>

</div>

<div class="ccm-block-field-group">
<h4><?=t('Link and Caption')?></h4><br/>

<div class="control-group">
	<?=$form->label('linkType', t('Image Links to:'))?>
	<div class="controls">	
		<select name="linkType" id="linkType">
			<option value="0" <?=(empty($externalLink) && empty($internalLinkCID) ? 'selected="selected"' : '')?>><?=t('Nothing')?></option>
			<option value="1" <?=(empty($externalLink) && !empty($internalLinkCID) ? 'selected="selected"' : '')?>><?=t('Another Page')?></option>
			<option value="2" <?=(!empty($externalLink) ? 'selected="selected"' : '')?>><?=t('External URL')?></option>
		</select>
	</div>
</div>

<div id="linkTypePage" style="display: none;" class="control-group">
	<?=$form->label('internalLinkCID', t('Choose Page:'))?>
	<div class="controls">
		<?= Loader::helper('form/page_selector')->selectPage('internalLinkCID', $internalLinkCID); ?>
	</div>
</div>
<div id="linkTypeExternal" style="display: none;" class="control-group">
	<?=$form->label('externalLink', t('URL:'))?>
	<div class="controls">
	<?= $form->text('externalLink', $externalLink, array('style' => 'width: 200px')); ?>
	</div>
</div>


<div class="control-group">
	<?=$form->label('altText', t('Alt Text/Caption'))?>
	<div class="controls">	
		<?= $form->text('altText', $altText, array('style' => 'width: 200px')); ?>
	</div>
</div>

</div>

<div>
<h4><?=t('Constrain Image Dimensions')?></h4>
<? if ($maxWidth == 0) { 
	$maxWidth = '';
} 
if ($maxHeight == 0) {
	$maxHeight = '';
}
?>

<div class="control-group">
	<?=$form->label('maxWidth', t('Max Width'))?>
	<div class="controls">	
		<?= $form->text('maxWidth', $maxWidth, array('style' => 'width: 60px')); ?>
	</div>
</div>

<div class="control-group">
	<?=$form->label('maxHeight', t('Max Height'))?>
	<div class="controls">	
		<?= $form->text('maxHeight', $maxHeight, array('style' => 'width: 60px')); ?>
	</div>
</div>


<div class="control-group">
	<?=$form->label('forceImageToMatchDimensions', t('Scale Image'))?>
	<div class="controls">	
		<select name="forceImageToMatchDimensions" id="forceImageToMatchDimensions">
			<option value="0" <? if (!$forceImageToMatchDimensions) { ?> selected="selected" <? } ?>><?=t('Automatically')?></option>
			<option value="1" <? if ($forceImageToMatchDimensions == 1) { ?> selected="selected" <? } ?>><?=t('Force Exact Image Match')?></option>
		</select>
	</div>
</div>


</div>