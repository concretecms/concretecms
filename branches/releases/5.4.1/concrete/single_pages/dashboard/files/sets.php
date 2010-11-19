<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php  $ih = Loader::helper('concrete/interface'); ?>
<?php  if ($this->controller->getTask() == 'view_detail') { ?>
	<h1><span><?php echo t('Edit Set Details')?></span></h1>
	<div class="ccm-dashboard-inner">
		<form method="post" id="file_sets_edit" action="<?php echo $this->url('/dashboard/files/sets', 'file_sets_edit')?>">
			<?php echo $validation_token->output('file_sets_edit');?>
			<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
				<tbody>
					<tr>
						<td class="subheader"><?php echo t('Name')?></td>
					</tr>
					<tr>
						<td><?php echo $form->text('file_set_name',$fs->fsName,array('style'=>'width:99%'));?></td>
					</tr>
					<?php  if (PERMISSIONS_MODEL != 'simple') { ?>
					<tr>
						<td class="subheader"><?php echo t('Custom Permissions')?></td>
					</tr>
					<tr>
						<td>
						
						<?php  if ($fs->getFileSetType() == FileSet::TYPE_PRIVATE) { ?>
							<?php echo t('File set permissions are unavailable for private sets.')?>
						<?php  } else { ?>
						
						<?php echo $form->checkbox('fsOverrideGlobalPermissions', 1, $fs->overrideGlobalPermissions())?>
						<?php echo t('Enable custom permissions for this file set.')?>
						
						<div id="ccm-file-set-permissions-wrapper" <?php  if (!$fs->overrideGlobalPermissions()) { ?> style="display: none" <?php  } ?>>
						<a href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/user_group_selector" id="ug-selector" dialog-width="90%" dialog-title="<?php echo t('Choose User/Group')?>"  dialog-height="70%" class="ccm-button-right dialog-launch"><span><em><?php echo t('Add Group or User')?></em></span></a>
			
						<p>
						<?php echo t('Add users or groups to determine access to the file manager. These permissions affect only this set.');?>
						</p>
						<div class="ccm-spacer">&nbsp;</div><br/>

						<div id="ccm-file-permissions-entities-wrapper" class="ccm-permissions-entities-wrapper">			
						<div id="ccm-file-permissions-entity-base" class="ccm-permissions-entity-base">
						
							<?php  print $ph->getFileAccessRow('SET'); ?>
							
							
						</div>
						
						
						<?php  
						if ($fs->overrideGlobalPermissions()) {
							$gl = new GroupList($fs);
							$ul = new UserInfoList($fs);
						}else {
							$gfs = FileSet::getGlobal();
							$gl = new GroupList($gfs);
							$ul = new UserInfoList($gfs);
						
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
						
						<?php  } ?>
						
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
				echo $form->hidden('fsID',$fs->getFileSetID());
			?>
		</form>
	</div>
	
	<h1><span><?php echo t('Files')?></span></h1>
	<div class="ccm-dashboard-inner">
		<?php 
		Loader::model("file_list");
		$fl = new FileList();
		$fl->filterBySet($fs);
		$fl->sortByFileSetDisplayOrder();
		$files = $fl->get();
		if (count($files) > 0) { ?>
		
		<form id="ccm-file-set-save-sort-order" method="post" action="<?php echo $this->url('/dashboard/files/sets', 'save_sort_order')?>">
			<?php echo $form->hidden('fsDisplayOrder', '')?>
			<?php echo $form->hidden('fsID', $fs->getFileSetID())?>
		</form>
		
		<?php echo $ih->button_js(t('Save Display Order'), 'ccm_saveFileSetDisplayOrder()')?>
		
		
		<p><?php echo t('Click and drag to reorder the files in this set. New files added to this set will automatically be appended to the end.')?></p>
		<div class="ccm-spacer">&nbsp;</div>
		
		<ul class="ccm-file-set-file-list">
		
		<?php 

		foreach($files as $f) { ?>
			
		<li id="fID_<?php echo $f->getFileID()?>">
			<div>
				<?php echo $f->getThumbnail(1)?>				
				<span style="word-wrap: break-word"><?php echo $f->getTitle()?></span>
			</div>
		</li>
			
		<?php  } ?>

			
		</ul>
		<?php  } else { ?>
			<p><?php echo t('There are no files in this set.')?></p>
		<?php  } ?>
	</div>
	
	<script type="text/javascript">
	
	ccm_saveFileSetDisplayOrder = function() {
		var fslist = $('.ccm-file-set-file-list').sortable('serialize');
		$('form#ccm-file-set-save-sort-order input[name=fsDisplayOrder]').val(fslist);
		$('form#ccm-file-set-save-sort-order').submit();
	}
	
	$(function() {
		$(".ccm-file-set-file-list").sortable({
			cursor: 'move',
			opacity: 0.5
		});
		
		//var ualist = $(this).sortable('serialize');
/*				
				$.post('<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/dashboard/user_attributes_update.php', ualist, function(r) {
	
				});
				*/
	
	});
	
	</script>
	
	<style type="text/css">
	.ccm-file-set-file-list:hover {cursor: move}
	</style>

	<h1><span><?php echo t('Delete File Set')?></span></h1>
	
	<div class="ccm-dashboard-inner">
		<?php 
		$u=new User();

		$delConfirmJS = t('Are you sure you want to permanently remove this file set?');
		?>
		
		<script type="text/javascript">
		deleteFileSet = function() {
			if (confirm('<?php echo $delConfirmJS?>')) { 
				location.href = "<?php echo $this->url('/dashboard/files/sets', 'delete', $fs->getFileSetID(), Loader::helper('validation/token')->generate('delete_file_set'))?>";				
			}
		}
		</script>

		<?php  print $ih->button_js(t('Delete Set'), "deleteFileSet()", 'left');?>

		<div class="ccm-spacer"></div>
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
	<h1><span><?php echo t('File Sets')?></span></h1>
	<div class="ccm-dashboard-inner">

		<div class="ccm-search-bar">
		
		<form id="ccm-file-set-search" method="get" action="<?php echo $this->url('/dashboard/files/sets')?>">
		<div id="ccm-group-search-fields">

		<strong><?php echo t('Type')?></strong>
		
		<input type="radio" id="fsTypePublic" name="fsType" value="<?php echo FileSet::TYPE_PUBLIC?>" <?php  if ($fsType != FileSet::TYPE_PRIVATE) { ?> checked <?php  } ?> onclick="$('#ccm-file-set-search').submit()" />
		<label for="fsTypePublic"><?php echo t('Public Sets')?></label>
		&nbsp;&nbsp;&nbsp;
		<input type="radio" id="fsTypePublic" name="fsType" value="<?php echo FileSet::TYPE_PRIVATE?>" <?php  if ($fsType == FileSet::TYPE_PRIVATE) { ?> checked <?php  } ?> onclick="$('#ccm-file-set-search').submit()"  />
		<label for="fsTypePublic"><?php echo t('My Sets')?></label>
		
		<span style="margin-left: 40px">&nbsp;</span>
		
		<strong><?php echo t('Keywords')?></strong>
		
		<input type="text" id="ccm-group-search-keywords" name="fsKeywords" value="<?php echo Loader::helper('text')->entities($_REQUEST['fsKeywords'])?>" class="ccm-text" style="width: 100px" />
		<input type="submit" value="<?php echo t('Search')?>" />
		<input type="hidden" name="group_submit_search" value="1" />
		</div>
		</form>

		</div>
		
		<?php  if (count($fileSets) > 0) { 
			$fsl->displaySummary();
			
		foreach ($fileSets as $fs) { ?>
		
			<div class="ccm-group">
				<a class="ccm-group-inner" href="<?php echo $this->url('/dashboard/files/sets/', 'view_detail', $fs->getFileSetID())?>" style="background-image: url(<?php echo ASSETS_URL_IMAGES?>/icons/group.png)"><?php echo $fs->getFileSetName()?></a>
			</div>
		
		
		<?php  }
		
			$fsl->displayPaging();
		
		} else { ?>
		
			<p><?php echo t('No file sets found.')?></p>
		
		<?php  } ?>
	
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