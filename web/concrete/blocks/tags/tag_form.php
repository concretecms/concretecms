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
	<div class="ccm-block-field-group">
		<h2><?php echo t('Display Title')?></h2>
		<?php echo $form->text('title',$title);?>
		<br/><br/>
		
		<div>
			<label>
				<?php echo $form->radio('displayMode','page',$displayMode)?>
				<?php echo t('Display Tags for the current page')?>
			</label>
		</div>
		<div>
			<label>
				<?php echo $form->radio('displayMode','cloud',$displayMode)?>
				<?php echo t('Display available tags');?>
			</label>
		</div>
		<div id="ccm-tags-display-page">
			<h2><?php echo $ak->getAttributeKeyName();?></h2>
			<?php
				$av = $c->getAttributeValueObject($ak);
				$ak->render('form',$av);
			?>
		</div>
		<div id="ccm-tags-display-cloud" style="display: none;">
			<h2><?php echo t('Number of tags to display');?></h2>
			<?php echo $form->text('cloudCount',$cloudCount,array('size'=>4))?>
		</div>
	</div>
</div>

<div id="ccm-tagsPane-advanced" class="ccm-tagsPane" style="display:none";>
	<div class="ccm-block-field-group">
		<h2><?php echo t('Tags Link to Location')?></h2>
		<div class="ccm-tags">
			<?php
			$form_selector = Loader::helper('form/page_selector');
			print $form_selector->selectPage('targetCID', $targetCID);
			?>
		</div>
		<br/><br/>
	</div>
</div>
<?php } ?>