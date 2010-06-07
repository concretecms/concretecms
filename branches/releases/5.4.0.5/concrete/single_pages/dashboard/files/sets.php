<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<?php  if($action == 'edit-form') { ?>
	<h1><span><?php echo t('Edit Public Set')?></span></h1>
	<div class="ccm-dashboard-inner">
		<form method="post" id="file_sets_edit" action="<?php echo $this->url('/dashboard/files/sets', 'file_sets_edit')?>">
			<?php echo $validation_token->output('file_sets_edit');?>
			<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
				<tbody>
					<tr>
						<td class="subheader"><?php echo t('Name')?></td>
					</tr>
					<tr>
						<td><?php echo $form->text('file_set_name',$file_set->fsName,array('style'=>'width:99%'));?></td>
					</tr>
					<?php  if (PERMISSIONS_MODEL != 'simple') { ?>
					<tr>
						<td class="subheader"><?php echo t('Custom Permissions')?></td>
					</tr>
					<tr>
						<td>
						
						<?php echo $form->checkbox('fsOverrideGlobalPermissions', 1, $file_set->overrideGlobalPermissions())?>
						<?php echo t('Enable custom permissions for this file set.')?>
						
						<div id="ccm-file-set-permissions-wrapper" <?php  if (!$file_set->overrideGlobalPermissions()) { ?> style="display: none" <?php  } ?>>
						<a href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/user_group_selector" id="ug-selector" dialog-width="90%" dialog-title="<?php echo t('Choose User/Group')?>"  dialog-height="70%" class="ccm-button-right dialog-launch"><span><em><?php echo t('Add Group or User')?></em></span></a>
			
						<p>
						<?php echo t('Add users or groups to determine access to the file manager. These permissions affect only this set.');?>
						</p>
						<div class="ccm-spacer">&nbsp;</div><br/>

						<div id="ccm-file-permissions-entities-wrapper">			
						<div id="ccm-file-permissions-entity-base">
						
							<?php  print $ph->getFileAccessRow('SET'); ?>
							
							
						</div>
						
						
						<?php  
						if ($file_set->overrideGlobalPermissions()) {
							$gl = new GroupList($file_set);
							$ul = new UserInfoList($file_set);
						}else {
							$fs = FileSet::getGlobal();
							$gl = new GroupList($fs);
							$ul = new UserInfoList($fs);
						
						}
						
						$gArray = $gl->getGroupList();
						$uArray = $ul->getUserInfoList();
						foreach($gArray as $g) { ?>
							
							<?php  print $ph->getFileAccessRow('SET','gID_' . $g->getGroupID(), $g->getGroupName(), $g->getFileSearchLevel(), $g->getFileReadLevel(), $g->getFileWriteLevel(), $g->getFileAdminLevel(), $g->getFileAddLevel(), $g->getAllowedFileExtensions()); ?>
						
						<?php  } ?>
						<?php  foreach($uArray as $ui) { ?>
							
							<?php  print $ph->getFileAccessRow('SET','uID_' . $ui->getUserID(), $ui->getUserName(), $ui->getFileSearchLevel(), $ui->getFileReadLevel(), $ui->getFileWriteLevel(), $ui->getFileAdminLevel(), $ui->getFileAddLevel(), $ui->getAllowedFileExtensions()); ?>
						
						<?php  } ?>
						</div>
						
						
						<div class="ccm-spacer">&nbsp;</div>
						
						</div>
						
						
						
						</td>
					</tr>
					<?php  } ?>
					<tr>
						<td class="header">
						<?php echo $concrete_interface->submit(t('Update'), 'file_sets_edit');?>
						<?php echo $concrete_interface->button(t('Cancel'), $this->url('/dashboard/files/sets'), 'left');?>						
						</td>
					</tr>
				</tbody>
			</table>
			<?php 
				echo $form->hidden('fsID',$file_set->fsID);
			?>
		</form>
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
			
			$("#fsOverrideGlobalPermissions").click(function() {
				if ($(this).attr('checked')) {
					$('#ccm-file-set-permissions-wrapper').show();
				} else { 
					$('#ccm-file-set-permissions-wrapper').hide();
				}
			});
		});
</script>	
<?php  } else { ?>
	<h1><span><?php echo t('Public File Sets')?></span></h1>
		<div class="ccm-dashboard-inner">
		<div style="margin:0px; padding:0px; width:100%; height:auto" >	
			<form method="post" id="file-sets-edit-or-delete" action="<?php echo $this->url('/dashboard/files/sets', 'file_sets_edit_or_delete')?>">	
				<?php echo $validation_token->output('file_sets_edit_or_delete');?>
				<table border="0" cellspacing="1" cellpadding="0" class="grid-list" width="600">
					<tr>
						<td class="subheader" width="100%"><?php echo t('Name')?></td>	
						<td class="subheader"><div style="width: 90px"></div></td>
						<td class="subheader"><div style="width: 60px"></div></td>
					</tr>
					<?php  foreach($file_sets as $set) { ?>
					<tr>
						<td><?php echo $set->fsName?></td>
						<td>
							<?php 
								$b1 = $concrete_interface->button_js(t('Edit'), 'editFileSet('.$set->fsID.')');
								print $concrete_interface->buttons($b1);
							?>									
						</td>
						<td>
							<?php 
								$b1 = $concrete_interface->button_js(t('Delete'), 'deleteFileSet('.$set->fsID.')');
								print $concrete_interface->buttons($b1);
							?>									
						</td>
					</tr>	
					<?php  } ?>
					<?php echo $form->hidden('fsID');?>
					<?php echo $form->hidden('file-sets-edit-or-delete-action');?>
				</table>
			</form>			
		</div>
	</div>
	
	<h1><span><?php echo t('Add Public Set')?></span></h1>
	<div class="ccm-dashboard-inner">
		<form method="post" id="file-sets-add" action="<?php echo $this->url('/dashboard/files/sets', 'file_sets_add')?>">
			<?php echo $validation_token->output('file_sets_add');?>
			<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
				<tbody>
					<tr>
						<td class="subheader"><?php echo t('Name')?></td>
					</tr>
					<tr>
						<td><?php echo $form->text('file_set_name','',array('style'=>'width:99%'));?></td>
					</tr>
					<tr>
						<td class="header">
						<?php 
							$b1 = $concrete_interface->submit(t('Add'), 'file-sets-add');
							print $concrete_interface->buttons($b1);
						?>					
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
	
	<script type="text/javascript">
		var editFileSet = function(fsID){	
			//set id
			$('#fsID').attr('value',fsID);		
			$('#file-sets-edit-or-delete-action').attr('value','edit-form');
			//submit form
			$("#file-sets-edit-or-delete").get(0).submit();		
		}
		
		var deleteFileSet = function(fsID){
			//set id
			$('#fsID').attr('value',fsID);		
			$('#file-sets-edit-or-delete-action').attr('value','delete');		
			if(confirm("<?php echo t('Are you sure you want to delete this file set?')?>")){
				$("#file-sets-edit-or-delete").get(0).submit();
			}
		}
		
		
	</script>
<?php  } ?>	