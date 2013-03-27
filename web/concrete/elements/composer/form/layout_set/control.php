<?
defined('C5_EXECUTE') or die("Access Denied.");
?>
<div class="ccm-composer-form-layout-control-set-control" data-composer-form-layout-control-set-control-id="<?=$control->getComposerFormLayoutSetControlID()?>">
<div class="ccm-composer-item-control-bar">
	<ul class="ccm-composer-item-controls">
		<li><a href="#" data-command="move-set-control" style="cursor: move"><i class="icon-move"></i></a></li>
		<li><a href="#" data-edit-set="<?=$control->getComposerFormLayoutSetControlID()?>"><i class="icon-pencil"></i></a></li>
		<li><a href="#" data-delete-set-control="<?=$control->getComposerFormLayoutSetControlID()?>"><i class="icon-trash"></i></a></li>
	</ul>

	<div style="display: none">
		<div data-delete-set-control-dialog="<?=$control->getComposerFormLayoutSetControlID()?>">
			<?=t("Delete this control? This cannot be undone.")?>
			<?=Loader::helper('validation/token')->output('delete_set_control')?>
		</div>
	</div>

	<? /*

	<div style="display: none">
		<div data-edit-set-dialog="<?=$set->getComposerFormLayoutSetID()?>">
			<form data-edit-set-form="<?=$set->getComposerFormLayoutSetID()?>" action="<?=$this->action('update_set', $set->getComposerFormLayoutSetID())?>" method="post">
			<div class="control-group">
				<?=$form->label('cmpFormLayoutSetName', t('Set Name'))?>
				<div class="controls">
					<?=$form->text('cmpFormLayoutSetName', $set->getComposerFormLayoutSetName())?>
				</div>
			</div>
			<?=Loader::helper('validation/token')->output('update_set')?>
			</form>
		</div>
	</div>
	*/
	?>
<div class="ccm-composer-form-layout-control-set-control-inner">
	<?
	$object = $control->getComposerControlObject();
	print $object->getComposerControlName();
	?>
</div>
</div>
</div>
