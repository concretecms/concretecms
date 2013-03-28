<?
defined('C5_EXECUTE') or die("Access Denied.");
?>
<div class="ccm-composer-form-layout-control-set-control" data-composer-form-layout-control-set-control-id="<?=$control->getComposerFormLayoutSetControlID()?>">
<div class="ccm-composer-item-control-bar">
	<ul class="ccm-composer-item-controls">
		<li><a href="#" data-command="move-set-control" style="cursor: move"><i class="icon-move"></i></a></li>
		<li><a data-command="edit-form-set-control" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/composer/form/edit_control?cmpFormLayoutSetControlID=<?=$control->getComposerFormLayoutSetControlID()?>" class="dialog-launch" dialog-width="400" dialog-height="200" dialog-modal="true" dialog-title="<?=t('Edit Form Control')?>"><i class="icon-pencil"></i></a></li>
		<li><a href="#" data-delete-set-control="<?=$control->getComposerFormLayoutSetControlID()?>"><i class="icon-trash"></i></a></li>
	</ul>

	<div style="display: none">
		<div data-delete-set-control-dialog="<?=$control->getComposerFormLayoutSetControlID()?>">
			<?=t("Delete this control? This cannot be undone.")?>
			<?=Loader::helper('validation/token')->output('delete_set_control')?>
		</div>
	</div>

<div class="ccm-composer-form-layout-control-set-control-inner">
	<?
	print $control->getComposerControlLabel();
	?>
</div>
</div>
</div>
