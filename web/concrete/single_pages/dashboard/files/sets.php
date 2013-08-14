<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<? $ih = Loader::helper('concrete/interface'); ?>
<? if ($this->controller->getTask() == 'view_detail') { ?>


	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('File Set'), false, 'span10 offset1', false)?>
	<form method="post" class="form-horizontal" id="file_sets_edit" action="<?=$this->url('/dashboard/files/sets', 'file_sets_edit')?>" onsubmit="return ccm_saveFileSetDisplayOrder()">
		<?=$form->hidden('fsDisplayOrder', '')?>
		<?=$validation_token->output('file_sets_edit');?>

	<div class="ccm-pane-body">
	
	<div class="clearfix">
	<ul class="tabs">
		<li class="active"><a href="javascript:void(0)" onclick="$('.tabs').find('li.active').removeClass('active');$(this).parent().addClass('active');$('.ccm-tab').hide();$('#ccm-tab-details').show()" ><?=t('Details')?></a></li>
		<li><a href="javascript:void(0)" onclick="$('.tabs').find('li.active').removeClass('active');$(this).parent().addClass('active');$('.ccm-tab').hide();$('#ccm-tab-files').show()"><?=t("Files in Set")?></a></li>
	</ul>
	</div>

	<div id="ccm-tab-details" class="ccm-tab">

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

		<div class="control-group">
		<?=$form->label('file_set_name', t('Name'))?>
		<div class="controls">
			<?=$form->text('file_set_name',$fs->fsName, array('class' => 'span5'));?>	
		</div>
		</div>

		<? 
		$fsp = new Permissions($fs);

		if (PERMISSIONS_MODEL != 'simple') { 
		
		if ($fsp->canEditFileSetPermissions()) {

		?>
		
		<div class="control-group">
		<?=$form->label('fsOverrideGlobalPermissions', t('Custom Permissions'))?>
		<div class="controls">
			<label class="checkbox"><?=$form->checkbox('fsOverrideGlobalPermissions', 1, $fs->overrideGlobalPermissions())?> <span><?=t('Enable custom permissions for this file set.')?></span></label>
		</div>
		</div>
		
		

		<div id="ccm-permission-list-form" <? if (!$fs->overrideGlobalPermissions()) { ?> style="display: none" <? } ?>>

		<? Loader::element('permission/lists/file_set', array("fs" => $fs)); ?>
		
		</div>
		<? } 
		
		}
		?>
		

		<?php
			echo $form->hidden('fsID',$fs->getFileSetID());
		?>
		
		</div>

	<div style="display: none" class="ccm-tab" id="ccm-tab-files">
		<?
		Loader::model("file_list");
		$fl = new FileList();
		$fl->filterBySet($fs);
		$fl->sortByFileSetDisplayOrder();
		$files = $fl->get();
		if (count($files) > 0) { ?>
		
		
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
	</div>
	<div class="ccm-pane-footer">
		<input type="submit" value="<?=t('Save')?>" class="btn primary ccm-button-v2-right" />
		<? if ($fsp->canDeleteFileSet()) { ?>
			<? print $ih->button_js(t('Delete'), "deleteFileSet()", 'right','error');?>
		<? } ?>
	</div>

	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>

	</form>
	
	
	<script type="text/javascript">
	
	ccm_saveFileSetDisplayOrder = function() {
		var fslist = $('.ccm-file-set-file-list').sortable('serialize');
		$('input[name=fsDisplayOrder]').val(fslist);
		return true;
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

<?php } else { ?>


	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('File Sets'), false, 'span10 offset1', false)?>
	<div class="ccm-pane-options">
		
		<form id="ccm-file-set-search" method="get" action="<?=$this->url('/dashboard/files/sets')?>" class="form-horizontal">
		<div class="ccm-pane-options-permanent-search">

		<div class="span4">
		<?=$form->label('fsKeywords', t('Keywords'))?>
		<div class="controls">
		<input type="text" id="fsKeywords" name="fsKeywords" value="<?=Loader::helper('text')->entities($_REQUEST['fsKeywords'])?>" class="span3" />
		</div>
		</div>

		<div class="span4">
		<?=$form->label('fsType', t('Type'))?>
		<div class="controls">
		<select id="fsType" name="fsType" style="width: 130px">
		<option value="<?=FileSet::TYPE_PUBLIC?>" <? if ($fsType != FileSet::TYPE_PRIVATE) { ?> selected <? } ?>><?=t('Public Sets')?></option>
		<option value="<?=FileSet::TYPE_PRIVATE?>" <? if ($fsType == FileSet::TYPE_PRIVATE) { ?> selected <? } ?>><?=t('My Sets')?></option>
		</select>
		<input type="submit" class="btn" value="<?=t('Search')?>" />
		</div>
		</div>
				
		<input type="hidden" name="group_submit_search" value="1" />

	</div>
		</form>
	</div>
	<div class="ccm-pane-body <? if (!$fsl->requiresPaging()) { ?> ccm-pane-body-footer <? } ?> ">

		<a href="<?=View::url('/dashboard/files/add_set')?>" style="float: right; z-index: 5; position:relative;top:-5px" class="btn primary"><?=t("Add File Set")?></a>

		<?=$fsl->displaySummary()?>
	
		<? if (count($fileSets) > 0) { ?>
			
			<style type="text/css">
				div.ccm-paging-top {padding-bottom:10px;}
			</style>
		
		<? foreach ($fileSets as $fs) { ?>
		
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