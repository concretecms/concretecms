	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('File Storage Locations'), false, 'span10 offset3', false)?>

	<form method="post" class="form-stacked" id="file-access-storage" action="<?=$this->url('/dashboard/system/maintenance/file_storage_locations', 'save')?>">
	<div class="ccm-pane-body">
			<?=$validation_token->output('file_storage');?>
			<h4><?=t('Standard File Location')?></h4>
			<p><?=t('Enter the directory where files will be stored on this server by default.')?></p>
			<?=$form->text('DIR_FILES_UPLOADED', DIR_FILES_UPLOADED, array('rows'=>'2','style' => 'width:530px'))?>

			
			<h4><?=t('Alternate Storage Directory')?></h4>
			<p><?=t('Enter the name and path of an optional, additional location for file storage.')?></p>
			
			<label for="alternate_storage_directory_name"><strong><?=t('Location Name')?></strong></label>
			<?=$form->text('fslName', $fslName, array('style' => 'width:530px'))?>
			<br/><br/>
			<label for="alternate_storage_directory_name"><strong><?=t('Path')?></strong></label>
			<?=$form->text('fslDirectory', $fslDirectory, array('rows' => '2', 'style' => 'width:530px'))?>
	</div>
	<div class="ccm-pane-footer">
			<?php		
				$b1 = $concrete_interface->submit(t('Save'), 'file-storage', 'right', 'primary');
				print $b1;
			?>		
	</div>
	</form>

	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>
