	<h1><span><?=t('Allowed File Types')?></span></h1>
	<div class="ccm-dashboard-inner">
		<form method="post" id="file-access-extensions" action="<?=$this->url('/dashboard/files/access', 'file_access_extensions')?>">
			<?=$validation_token->output('file_access_extensions');?>
			<p>
			<?=t('Only files with the following extensions will be allowed.
			Separate extensions with commas. Periods and spaces will be
			ignored.')?>
			</p>
			<?=$form->textarea('file-access-file-types',$file_access_file_types,array('rows'=>'3','style'=>'width:99%'));?>
			<?php		
				$b1 = $concrete_interface->submit(t('Save'), 'file-access-extensions');
				print $concrete_interface->buttons($b1);
			?>		
		</form>
	</div>
	
	<h1><span><?=t('Permissions')?></span></h1>
	<div class="ccm-dashboard-inner">
		<form method="post" id="file-access-extensions" action="<?=$this->url('/dashboard/files/access', 'file_access_extensions')?>">
			<?=$validation_token->output('file_permissions');?>

			<p>
			<?=t('Add users or groups to the grid below to determine access to the file manager.');?>
			</p>
			
			<div class="ccm-file-permissions-entity">
			
			<h2>Admin Group</h2>

			<table border="0" cellspacing="0" cellpadding="0" id="ccm-file-permissions-grid">
			<tr>
				<th><?=t('View')?></th>
				<td><?=$form->radio('canView[]', 'ALL', true)?> <?=('All')?></td>
				<td><?=$form->radio('canView[]', 'MINE')?> <?=('Mine')?></td>
				<td><?=$form->radio('canView[]', 'NONE')?> <?=('None')?></td>
				</td>
			</tr>
			<tr>
				<th><?=t('Edit')?></th>
				<td><?=$form->radio('canEdit[]', 'ALL', true)?> <?=('All')?></td>
				<td><?=$form->radio('canEdit[]', 'MINE')?> <?=('Mine')?></td>
				<td><?=$form->radio('canEdit[]', 'NONE')?> <?=('None')?></td>
				</td>
			</tr>
			<tr>
				<th><?=t('Admin')?></th>
				<td><?=$form->radio('canAdmin[]', 'ALL', true)?> <?=('All')?></td>
				<td><?=$form->radio('canAdmin[]', 'MINE')?> <?=('Mine')?></td>
				<td><?=$form->radio('canAdmin[]', 'NONE')?> <?=('None')?></td>
				</td>
			</tr>
			<tr>
				<th><?=t('Add')?></th>
				<td><?=$form->radio('canAdd[]', 'ALL', true)?> <?=('All')?></td>
				<td><?=$form->radio('canAdd[]', 'NONE')?> <?=('None')?></td>
				<td><?=$form->radio('canAdd[]', 'CUSTOM')?> <?=('Custom')?></td>
			</tr>
			<tr>
				<th>&nbsp;</th>
				<td colspan="3"><br/><strong><?=t('Allowed File Types')?></strong>
				
				<div style="border: 1px solid #ddd; padding: 4px; height: 120px">
				<?
				$extensions = $concrete_file->getAllowedFileExtensions();
				?>
				</div>
				
				</td>
			</tr>
			</table>
			
			</div>
			

				<a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/user_group_selector" id="ug-selector" dialog-width="600" dialog-title="<?=t('Choose User/Group')?>"  dialog-height="400" class="dialog-launch"><?=t('Add Group or User')?></a>
			
			
			

		</form>
	</div>
	
	<script type="text/javascript">
	
	
	$(function() {	
		$("#ug-selector").dialog();	
		ccm_setupGridStriping('ccm-file-permissions-grid');
	});
	
	</script>