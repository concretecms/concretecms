<div class="ccm-module" style="width: 400px">

<?
	
	Loader::model('file_set');
	$fs = FileSet::getGlobal();
	$gl = new GroupList($fs);
	$ul = new UserInfoList($fs);
	$uArray = $ul->getUserInfoList();
	?>
	
	<h1><span><?=t('Permissions')?></span></h1>
	<div class="ccm-dashboard-inner">
		<form method="post" id="file-access-permissions" action="<?=$this->url('/dashboard/files/access', 'save_global_permissions')?>">
			<?=$validation_token->output('file_permissions');?>

			<a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/user_group_selector" id="ug-selector" dialog-width="600" dialog-title="<?=t('Choose User/Group')?>"  dialog-height="400" class="ccm-button-right dialog-launch"><span><em><?=t('Add Group or User')?></em></span></a>

			<p>
			<?=t('Add users or groups to determine access to the file manager.');?>
			</p>
			
			<div class="ccm-spacer">&nbsp;</div><br/>
			
			<div id="ccm-file-permissions-entities-wrapper">			
			<div id="ccm-file-permissions-entity-base">
			
				<? print $this->controller->getFileAccessRow('GLOBAL'); ?>
				
				
			</div>
			
			
			<? $gArray = $gl->getGroupList();
			foreach($gArray as $g) { ?>
				
				<? print $this->controller->getFileAccessRow('GLOBAL', 'gID_' . $g->getGroupID(), $g->getGroupName(), $g->getFileSearchLevel(), $g->getFileReadLevel(), $g->getFileWriteLevel(), $g->getFileAdminLevel(), $g->getFileAddLevel(), $g->getAllowedFileExtensions()); ?>
			
			<? } ?>
			<? foreach($uArray as $ui) { ?>
				
				<? print print $this->controller->getFileAccessRow('GLOBAL', 'uID_' . $ui->getUserID(), $ui->getUserName(), $ui->getFileSearchLevel(), $ui->getFileReadLevel(), $ui->getFileWriteLevel(), $ui->getFileAdminLevel(), $ui->getFileAddLevel(), $ui->getAllowedFileExtensions()); ?>
			
			<? } ?>
			</div>
			
			
			<div class="ccm-spacer">&nbsp;</div>
			
			
			<? print $concrete_interface->submit(t('Save'), 'file-access-permissions'); ?>

			<div class="ccm-spacer">&nbsp;</div>
		</form>
	</div>
	
</div>

<div class="ccm-module" style="width: 300px">

	<h1><span><?=t('Allowed File Types')?></span></h1>
	<div class="ccm-dashboard-inner">
		<form method="post" id="file-access-extensions" action="<?=$this->url('/dashboard/files/access', 'file_access_extensions')?>">
			<?=$validation_token->output('file_access_extensions');?>
			<p>
			<?=t('Only files with the following extensions will be allowed.
			Separate extensions with commas. Periods and spaces will be
			ignored.')?>
			</p>
			<?=$form->textarea('file-access-file-types',$file_access_file_types,array('rows'=>'20','style'=>'width:100%'));?>
			<?php		
				$b1 = $concrete_interface->submit(t('Save'), 'file-access-extensions');
				print $concrete_interface->buttons($b1);
			?>		
		</form>
	</div>
</div>



	<script type="text/javascript">
	ccm_triggerSelectUser = function(uID, uName) {
		ccm_alSelectPermissionsEntity('uID', uID, uName);
	}
	
	ccm_triggerSelectGroup = function (gID, gName) {
		ccm_alSelectPermissionsEntity('gID', gID, gName);
	}
	
	$(function() {	
		$("#ug-selector").dialog();	
		ccm_alActivateFilePermissionsSelector();	
	});
	
	</script>