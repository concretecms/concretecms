<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-ui" id="ccm-permission-detail">

<? Loader::element('permission/message_list'); ?>

<? if ($permissionKey->hasCustomOptionsForm() || $permissionKey->canPermissionKeyTriggerWorkflow()) { ?>
	<ul class="tabs" id="ccm-permission-detail-tabs">
		<li class="active"><a href="#" data-tab="access-types"><?=t('Access')?></a></li>
		<? if ($permissionKey->canPermissionKeyTriggerWorkflow()) { ?><li><a href="#" data-tab="workflow"><?=t('Workflow')?></a><? } ?></li>
		<? if ($permissionKey->hasCustomOptionsForm()) { ?><li><a href="#" data-tab="custom-options"><?=t('Details')?></a><? } ?></li>
	</ul>
<div class="clearfix"></div>
<? } ?>
	
<? if ($permissionKey->getPermissionKeyDescription()) { ?>
<div class="dialog-help">
<?=$permissionKey->getPermissionKeyDescription()?>
</div>
<? } ?>


<div id="ccm-permission-access-types">
<?
$accessTypes = $permissionKey->getSupportedAccessTypes();
Loader::element('permission/access_list', array('permissionKey' => $permissionKey, 'accessTypes' => $accessTypes)); ?>
</div>

<? if ($permissionKey->hasCustomOptionsForm()) { ?>
<div id="ccm-permission-custom-options" style="display: none">
<form id="ccm-permissions-custom-options-form" onsubmit="return ccm_submitPermissionCustomOptionsForm()" method="post" action="<?=$permissionKey->getPermissionKeyToolsURL()?>">

<? if ($permissionKey->getPackageID() > 0) { ?>
	<? Loader::packageElement('permission/keys/' . $permissionKey->getPermissionKeyHandle(), $permissionKey->getPackageHandle(), array('permissionKey' => $permissionKey)); ?>
<? } else { ?>
	<? Loader::element('permission/keys/' . $permissionKey->getPermissionKeyHandle(), array('permissionKey' => $permissionKey)); ?>
<? } ?>

</form>
</div>

<? } ?>

<? if ($permissionKey->canPermissionKeyTriggerWorkflow()) { ?>
	<div id="ccm-permission-workflow" style="display: none">
		<form id="ccm-permissions-workflow-form" onsubmit="return false" method="post" action="<?=$permissionKey->getPermissionKeyToolsURL()?>">
		
		</form>
	</div>
<? } ?>

</div>

<script type="text/javascript">
$(function() {

	$('#ccm-permission-detail-tabs a').unbind().click(function() {
		$('#ccm-permission-detail-tabs li').removeClass('active');
		$(this).parent().addClass('active');
		$('#ccm-permission-access-types').hide();
		$('#ccm-permission-custom-options').hide();
		$('#ccm-permission-workflow').hide();
		var tab = $(this).attr('data-tab');
		$('#ccm-permission-' + tab).show();
		$("#ccm-permission-detail").closest('.ui-dialog-content').jqdialog('option', 'buttons', false);
		switch(tab) {
			case 'custom-options':
				$("#ccm-permission-detail").closest('.ui-dialog-content').parent().append('<div class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix ccm-ui"><input type="submit" class="btn primary ccm-button-right" onclick="$(\'#ccm-permissions-custom-options-form\').submit()" value="<?=t('Save')?>" /></div>');
				break;
			case 'workflow':
				$("#ccm-permission-detail").closest('.ui-dialog-content').parent().append('<div class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix ccm-ui"><input type="submit" class="btn primary ccm-button-right" onclick="$(\'#ccm-permissions-workflow-form\').submit()" value="<?=t('Save')?>" /></div>');
				break;
		}
		return false;
	});


});
</script>
