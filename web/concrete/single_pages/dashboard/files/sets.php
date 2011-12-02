<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<? $ih = Loader::helper('concrete/interface'); ?>
<? if ($this->controller->getTask() == 'view_detail') { ?>

	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('File Set'))?>
	<div class="clearfix">
	<ul class="tabs">
		<li class="active"><a href="javascript:void(0)" onclick="$('.tabs').find('li.active').removeClass('active');$(this).parent().addClass('active');$('.ccm-tab').hide();$('#ccm-tab-details').show()" ><?=t('Details')?></a></li>
		<li><a href="javascript:void(0)" onclick="$('.tabs').find('li.active').removeClass('active');$(this).parent().addClass('active');$('.ccm-tab').hide();$('#ccm-tab-files').show()"><?=t("Files in Set")?></a></li>
	</ul>
	</div>

	<div id="ccm-tab-details" class="ccm-tab">
		<form method="post" id="file_sets_edit" action="<?=$this->url('/dashboard/files/sets', 'file_sets_edit')?>">
			<?=$validation_token->output('file_sets_edit');?>

		<?
		$u=new User();

		$delConfirmJS = t('Are you sure you want to permanently remove this file set?');
		?>
		
		<script type="text/javascript">
		deleteFileSet = function() {
			if (confirm('<?=$delConfirmJS?>')) { 
				location.href = "<?=$this->url('/dashboard/files/sets', 'delete', $fs->getFileSetID(), Loader::helper('validation/token')->generate('delete_file_set'))?>";				
			}
		}
		</script>

		<? print $ih->button_js(t('Delete Set'), "deleteFileSet()", 'right','error');?>

		<div class="clearfix">
		<?=$form->label('file_set_name', t('Name'))?>
		<div class="input">
			<?=$form->text('file_set_name',$fs->fsName);?>	
		</div>
		</div>

		<? if (PERMISSIONS_MODEL != 'simple') { ?>
		<div class="clearfix">
		<?=$form->label('fsOverrideGlobalPermissions', t('Custom Permissions'))?>
		<div class="input">
		<ul class="inputs-list">
			<li><label><?=$form->checkbox('fsOverrideGlobalPermissions', 1, $fs->overrideGlobalPermissions())?> <span><?=t('Enable custom permissions for this file set.')?></span></label></li>
		</ul>
		</div>
		</div>

		<div id="ccm-file-set-permissions-wrapper" <? if (!$fs->overrideGlobalPermissions()) { ?> style="display: none" <? } ?>>
		<a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/user_group_selector" id="ug-selector" dialog-width="90%" dialog-title="<?=t('Choose User/Group')?>"  dialog-height="70%" class="ccm-button-right dialog-launch btn"><?=t('Add Group or User')?></a>
		<p><?=t('Add users or groups to determine access to the file manager. These permissions affect only this set.');?></p>

		<div class="ccm-spacer">&nbsp;</div><br/>

		<div id="ccm-file-permissions-entities-wrapper" class="ccm-permissions-entities-wrapper">			
		<div id="ccm-file-permissions-entity-base" class="ccm-permissions-entity-base">
		
			<? print $ph->getFileAccessRow('SET'); ?>
			
			
		</div>
		
		
		<? 
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
			
			<? print $ph->getFileAccessRow('SET','gID_' . $g->getGroupID(), $g->getGroupName(), $g->getFileSearchLevel(), $g->getFileReadLevel(), $g->getFileWriteLevel(), $g->getFileAdminLevel(), $g->getFileAddLevel(), $g->getAllowedFileExtensions()); ?>
		
		<? } ?>
		<? foreach($uArray as $ui) { ?>
			
			<? print $ph->getFileAccessRow('SET','uID_' . $ui->getUserID(), $ui->getUserName(), $ui->getFileSearchLevel(), $ui->getFileReadLevel(), $ui->getFileWriteLevel(), $ui->getFileAdminLevel(), $ui->getFileAddLevel(), $ui->getAllowedFileExtensions()); ?>
		
		<? } ?>
		</div>
		
		
		<div class="ccm-spacer">&nbsp;</div>
		
		</div>
		<? } ?>
		

		<?php
			echo $form->hidden('fsID',$fs->getFileSetID());
		?>
		<div class="actions">
		<input type="submit" value="<?=t('Update Set')?>" class="btn primary" />
		</div>
		
		</form>
	</div>
	<div style="display: none" class="ccm-tab" id="ccm-tab-files">
		<?
		Loader::model("file_list");
		$fl = new FileList();
		$fl->filterBySet($fs);
		$fl->sortByFileSetDisplayOrder();
		$files = $fl->get();
		if (count($files) > 0) { ?>
		
		<form id="ccm-file-set-save-sort-order" method="post" action="<?=$this->url('/dashboard/files/sets', 'save_sort_order')?>">
			<?=$form->hidden('fsDisplayOrder', '')?>
			<?=$form->hidden('fsID', $fs->getFileSetID())?>
		</form>
		
		<?=$ih->button_js(t('Save Display Order'), 'ccm_saveFileSetDisplayOrder()')?>
		
		
		<p><?=t('Click and drag to reorder the files in this set. New files added to this set will automatically be appended to the end.')?></p>
		<div class="ccm-spacer">&nbsp;</div>
		
		<ul class="ccm-file-set-file-list">
		
		<?

		foreach($files as $f) { ?>
			
		<li id="fID_<?=$f->getFileID()?>">
			<div>
				<?=$f->getThumbnail(1)?>				
				<span style="word-wrap: break-word"><?=$f->getTitle()?></span>
			</div>
		</li>
			
		<? } ?>

			
		</ul>
		<? } else { ?>
			<p><?=t('There are no files in this set.')?></p>
		<? } ?>
	</div>
	
	
	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper()?>
	
	
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
		
	});
	
	</script>
	
	<style type="text/css">
	.ccm-file-set-file-list:hover {cursor: move}
	</style>

	<script type="text/javascript">
		
		$(function() {	
			ccm_triggerSelectUser = function(uID, uName) {
				ccm_alSelectPermissionsEntity('uID', uID, uName);
			}
			
			ccm_triggerSelectGroup = function (gID, gName) {
				ccm_alSelectPermissionsEntity('gID', gID, gName);
			}

			$("#ug-selector").dialog();	
			ccm_alActivateFilePermissionsSelector();	
			
			$("#fsOverrideGlobalPermissions").click(function() {
				if ($(this).prop('checked')) {
					$('#ccm-file-set-permissions-wrapper').show();
				} else { 
					$('#ccm-file-set-permissions-wrapper').hide();
				}
			});
		});
</script>	
<?php } else { ?>


	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('File Sets'), false, 'span16', false)?>
	<div class="ccm-pane-options">
	<div class="ccm-pane-options-permanent-search">
		
		<form id="ccm-file-set-search" method="get" action="<?=$this->url('/dashboard/files/sets')?>">
		<div class="span4">
		<?=$form->label('fsType', t('Type'))?>
		<div class="input">
		<select id="fsType" name="fsType" class="span3">
		<option value="<?=FileSet::TYPE_PUBLIC?>" <? if ($fsType != FileSet::TYPE_PRIVATE) { ?> selected <? } ?>><?=t('Public Sets')?></option>
		<option value="<?=FileSet::TYPE_PRIVATE?>" <? if ($fsType == FileSet::TYPE_PRIVATE) { ?> selected <? } ?>><?=t('My Sets')?></option>
		</select>
		</div>
		</div>

		<div class="span5">
		<?=$form->label('fsKeywords', t('Keywords'))?>
		<div class="input">
		<input type="text" id="fsKeywords" name="fsKeywords" value="<?=Loader::helper('text')->entities($_REQUEST['fsKeywords'])?>" class="span3" />
		</div>
		</div>
				
		<input type="submit" class="btn" value="<?=t('Search')?>" />
		<input type="hidden" name="group_submit_search" value="1" />
		</form>

	</div>
	</div>
	<div class="ccm-pane-body <? if (!$fsl->requiresPaging()) { ?> ccm-pane-body-footer <? } ?> ">
	
		<? if (count($fileSets) > 0) { 
			
		foreach ($fileSets as $fs) { ?>
		
			<div class="ccm-group">
				<a class="ccm-group-inner" href="<?=$this->url('/dashboard/files/sets/', 'view_detail', $fs->getFileSetID())?>" style="background-image: url(<?=ASSETS_URL_IMAGES?>/icons/group.png)"><?=$fs->getFileSetName()?></a>
			</div>
		
		
		<? }
		
		
		} else { ?>
		
			<p><?=t('No file sets found.')?></p>
		
		<? } ?>
	
	</div>
	<? if ($fsl->requiresPaging()) { ?>
		<div class="ccm-pane-footer">
		<? $fsl->displayPagingV2(); ?>
		</div>
	<? } ?>
	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper($fsl->requiresPaging())?>
	
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
			if(confirm("<?=t('Are you sure you want to delete this file set?')?>")){
				$("#file-sets-edit-or-delete").get(0).submit();
			}
		}
		
		
	</script>
<?php } ?>	