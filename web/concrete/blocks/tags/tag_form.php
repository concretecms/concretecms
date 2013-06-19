<?php defined('C5_EXECUTE') or die("Access Denied.");  
$form = Loader::helper('form');
$c = Page::getCurrentPage();

if(!$ak instanceof CollectionAttributeKey) {?>
	<div class="ccm-error"><?=t('Error: The required page attribute with the handle of: "%s" doesn\'t exist',$controller->attributeHandle)?><br/><br/></div>
<? } else { ?>
<input type="hidden" name="attributeHandle" value="<?=$controller->attributeHandle?>" />
<ul id="ccm-tags-tabs" class="tabs">
	<li class="active"><a id="ccm-tags-tab-add" href="javascript:void(0);"><?=($bID>0)? t('Edit') : t('Add') ?></a></li>
	<li class=""><a id="ccm-tags-tab-advanced"  href="javascript:void(0);"><?=t('Advanced')?></a></li>
</ul>

<div id="ccm-tagsPane-add" class="ccm-tagsPane">

	<div class="clearfix">
	<?=$form->label('title', t('Display Title'))?>
	<div class="input">
		<?php echo $form->text('title',$title);?>
	</div>
	</div>

	<div class="clearfix">
	<label><?=t('Display')?></label>
	<div class="input">
	<ul class="inputs-list">
		<li><label><?php echo $form->radio('displayMode','page',$displayMode)?> <span><?php echo t('Display Tags for the current page')?></span></label></li>
		<li><label><?php echo $form->radio('displayMode','cloud',$displayMode)?> <span><?php echo t('Display available tags')?></span></label></li>
	</ul>
	</div>
	</div>

	<div id="ccm-tags-display-page" class="clearfix">
	<label><?php echo tc('AttributeKeyName', $ak->getAttributeKeyName());?></label>
	<div class="input">
		<?php
			$av = $c->getAttributeValueObject($ak);
			$ak->render('form',$av);
		?>
	</div>
	</div>

	<div id="ccm-tags-display-cloud" class="clearfix">
	<?=$form->label('cloudCount', t('Number to Display'))?>
	<div class="input">
			<?php echo $form->text('cloudCount',$cloudCount,array('size'=>4))?>
	</div>
	</div>

</div>

<div id="ccm-tagsPane-advanced" class="ccm-tagsPane" style="display:none">
	<div class="clearfix">
	<label><?=t('Link Tags to Page')?></label>
	<div class="input">
		<?php
		$form_selector = Loader::helper('form/page_selector');
		print $form_selector->selectPage('targetCID', $targetCID);
		?>
	</div>
	</div>

</div>
<?php } ?>
