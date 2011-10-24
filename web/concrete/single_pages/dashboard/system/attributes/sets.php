<? defined('C5_EXECUTE') or die("Access Denied.");?>
<? if (in_array($this->controller->getTask(), array('update_set', 'update_set_attributes', 'edit', 'delete_set'))) { ?>

<h1><span><?=t('Set Attributes')?></span></h1>
<div class="ccm-dashboard-inner ccm-ui">
<p><?=t('Add the following attributes to this set.')?></p>

<form class="form-stacked" method="post" action="<?=$this->action('update_set_attributes')?>">
<input type="hidden" name="asID" value="<?=$set->getAttributeSetID()?>" />
<?=Loader::helper('validation/token')->output('update_set_attributes')?>

<? 
$cat = AttributeKeyCategory::getByID($set->getAttributeSetKeyCategoryID());
$list = AttributeKey::getList($cat->getAttributeKeyCategoryHandle());
$unassigned = $cat->getUnassignedAttributeKeys();
if (count($list) > 0) { ?>

	<div class="clearfix">
	<ul class="inputs-list">
	
	<?
	foreach($list as $ak) { 

	$disabled = '';
	if (!in_array($ak, $unassigned) && (!$ak->inAttributeSet($set))) { 
		$disabled = array('disabled' => 'disabled');
	}
	
	?>
		<li><label>
			<?=$form->checkbox('akID[]', $ak->getAttributeKeyID(), $ak->inAttributeSet($set), $disabled)?>
			<span><?=$ak->getAttributeKeyName()?></span>
			<span class="ccm-note"><?=$ak->getAttributeKeyHandle()?></span>
		</label>
		</li>	
	<? } ?>

	</div>
	
	<div class="actions">
		<?=$form->submit('submit', t('Update Attributes'), array('class' => 'primary'))?>
		<a class="btn" href="<?=$this->url('/dashboard/settings/attribute_sets', 'view', $set->getAttributeSetKeyCategoryID())?>"><?=t('Cancel')?></a>	
	</div>
<? 
} else { ?>
	<p><?=t('No attributes found.')?></p>
<? } ?>

</form>
</div>

<h1><span><?=t("Update Set Details")?></span></h1>
<div class="ccm-dashboard-inner ccm-ui">
	
	<? if ($set->isAttributeSetLocked()) { ?>
		<div class="info block-message alert-message"><p><?=t('This attribute set is locked. It cannot be deleted, and its handle cannot be changed.')?></p></div>	
	<? } ?>

	<form method="post" action="<?=$this->action('update_set')?>">
	<input type="hidden" name="asID" value="<?=$set->getAttributeSetID()?>" />
	<?=Loader::helper('validation/token')->output('update_set')?>
	<div class="clearfix">
		<?=$form->label('asHandle', t('Handle'))?>
		<div class="input">
			<? if ($set->isAttributeSetLocked()) { ?>
				<?=$form->text('asHandle', $set->getAttributeSetHandle(), array('disabled' => 'disabled'))?>
			<? } else { ?>
				<?=$form->text('asHandle', $set->getAttributeSetHandle())?>
			<? } ?>
		</div>
	</div>
	
	<div class="clearfix">
		<?=$form->label('asName', t('Name'))?>
		<div class="input">
			<?=$form->text('asName', $set->getAttributeSetName())?>
		</div>
	</div>
	
	<div class="actions">
		<?=$form->submit('submit', t('Update Set'), array('class' => 'primary'))?>
		<a class="btn" href="<?=$this->url('/dashboard/settings/attribute_sets', 'view', $set->getAttributeSetKeyCategoryID())?>"><?=t('Cancel')?></a>	
	</div>
	</form>
</div>


	<? if (!$set->isAttributeSetLocked()) { ?>	

<h1><span><?=t("Delete Set")?></span></h1>
<div class="ccm-dashboard-inner ccm-ui">

	<p><?=t('Warning, this cannot be undone. No attributes will be deleted but they will no longer be grouped together.')?></p>
	<form method="post" action="<?=$this->action('delete_set')?>" class="form-stacked">
		<input type="hidden" name="asID" value="<?=$set->getAttributeSetID()?>" />
		<?=Loader::helper('validation/token')->output('delete_set')?>
	
		<div class="actions">
			<?=$form->submit('submit', t('Delete Set'), array('class' => 'danger'))?>
			<a class="btn" href="<?=$this->url('/dashboard/settings/attribute_sets', 'view', $set->getAttributeSetKeyCategoryID())?>"><?=t('Cancel')?></a>	
		</div>
	</form>
	
</div>
<? } ?>

<? } else { ?>

<h1><span><?=t("Attribute Sets")?></span></h1>
<div class="ccm-dashboard-inner ccm-ui">


	<? if (count($sets) > 0) { ?>
	
	<div class="ccm-attribute-sortable-set-list">
	
		<? foreach($sets as $asl) { ?>
			<div class="ccm-group" id="asID_<?=$asl->getAttributeSetID()?>">
				<img class="ccm-group-sort" src="<?=ASSETS_URL_IMAGES?>/icons/up_down.png" width="14" height="14" />
				<a class="ccm-group-inner" href="<?=$this->url('/dashboard/settings/attribute_sets/', 'edit', $asl->getAttributeSetID())?>" style="background-image: url(<?=ASSETS_URL_IMAGES?>/icons/group.png)"><?=$asl->getAttributeSetName()?></a>
			</div>
		<? } ?>
	</div>
	
	<? } else { ?>
		<?=t('No attribute sets currently defined.')?>
	<? } ?>
	
</div>

<script type="text/javascript">
	$("div.ccm-attribute-sortable-set-list").sortable({
		handle: 'img.ccm-group-sort',
		cursor: 'move',
		opacity: 0.5,
		stop: function() {
			var ualist = $(this).sortable('serialize');
			ualist += '&categoryID=<?=$categoryID?>';
			$.post('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/dashboard/attribute_set_order_update', ualist, function(r) {

			});
		}
	});
</script>

</div>


<h1><span><?=t("Add Set")?></span></h1>
<div class="ccm-dashboard-inner ccm-ui">
	<p><?=t('Group attributes into sets for better organization and management.')?></p>
	
	<form method="post" action="<?=$this->action('add_set')?>">
	<input type="hidden" name="categoryID" value="<?=$categoryID?>" />
	<?=Loader::helper('validation/token')->output('add_set')?>
	<div class="clearfix">
		<?=$form->label('asHandle', t('Handle'))?>
		<div class="input">
			<?=$form->text('asHandle')?>
		</div>
	</div>
	
	<div class="clearfix">
		<?=$form->label('asName', t('Name'))?>
		<div class="input">
			<?=$form->text('asName')?>
		</div>
	</div>
	
	<div class="actions">
		<?=$form->submit('submit', t('Add Set'), array('class' => 'primary'))?>
	</div>
	</form>

</div>

<? } ?>


