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

<div class="clearfix">
	<label><?=t('Image')?></label>
	<div class="input">	
		<?=$al->image('ccm-b-image', 'fID', t('Choose Image'), $bf, $args);?>
	</div>
</div>
<div class="clearfix">
	<label><?=t('Image On-State')?></label>
	<div class="input">	
		<?=$al->image('ccm-b-image-onstate', 'fOnstateID', t('Choose Image On-State'), $bfo, $args);?>
	</div>
</div>

</div>

<div class="ccm-block-field-group">
<h4><?=t('Link and Caption')?></h4><br/>

<div class="clearfix">
	<?=$form->label('linkType', t('Image Links to:'))?>
	<div class="input">	
		<select name="linkType" id="linkType">
			<option value="0" <?=(empty($externalLink) && empty($internalLinkCID) ? 'selected="selected"' : '')?>><?=t('Nothing')?></option>
			<option value="1" <?=(empty($externalLink) && !empty($internalLinkCID) ? 'selected="selected"' : '')?>><?=t('Another Page')?></option>
			<option value="2" <?=(!empty($externalLink) ? 'selected="selected"' : '')?>><?=t('External URL')?></option>
		</select>
	</div>
</div>

<div id="linkTypePage" style="display: none;" class="clearfix">
	<?=$form->label('internalLinkCID', t('Choose Page:'))?>
	<div class="input">
		<?= Loader::helper('form/page_selector')->selectPage('internalLinkCID', $internalLinkCID); ?>
	</div>
</div>
<div id="linkTypeExternal" style="display: none;" class="clearfix">
	<?=$form->label('externalLink', t('URL:'))?>
	<div class="input">
	<?= $form->text('externalLink', $externalLink, array('style' => 'width: 250px')); ?>
	</div>
</div>


<div class="clearfix">
	<?=$form->label('altText', t('Alt Text/Caption'))?>
	<div class="input">	
		<?= $form->text('altText', $altText, array('style' => 'width: 250px')); ?>
	</div>
</div>

</div>

<div>
<h4><?=t('Constrain Image Dimensions')?></h4><br/>
<? if ($maxWidth == 0) { 
	$maxWidth = '';
} 
if ($maxHeight == 0) {
	$maxHeight = '';
}
?>

<div class="clearfix">
	<?=$form->label('maxWidth', t('Max Width'))?>
	<div class="input">	
		<?= $form->text('maxWidth', $maxWidth, array('style' => 'width: 60px')); ?>
	</div>
</div>

<div class="clearfix">
	<?=$form->label('maxHeight', t('Max Height'))?>
	<div class="input">	
		<?= $form->text('maxHeight', $maxHeight, array('style' => 'width: 60px')); ?>
	</div>
</div>


<div class="clearfix">
	<?=$form->label('forceImageToMatchDimensions', t('Scale Image'))?>
	<div class="input">	
		<select name="forceImageToMatchDimensions" id="forceImageToMatchDimensions">
			<option value="0" <? if (!$forceImageToMatchDimensions) { ?> selected="selected" <? } ?>><?=t('Automatically')?></option>
			<option value="1" <? if ($forceImageToMatchDimensions == 1) { ?> selected="selected" <? } ?>><?=t('Force Exact Image Match')?></option>
		</select>
	</div>
</div>


</div>