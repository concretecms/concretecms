	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('File Storage Locations'), false, 'span10 offset3', false)?>

	<form method="post" class="form-stacked" id="file-access-storage" action="<?=$this->url('/dashboard/system/environment/file_storage_locations', 'save')?>">
	<div class="ccm-pane-body">
			<?=$validation_token->output('file_storage');?>
			<fieldset>
			<legend><?=t('Standard File Location')?></legend>
			<label for="DIR_FILES_UPLOADED"><strong><?=t('Path')?></strong></label>
			<?=$form->text('DIR_FILES_UPLOADED', DIR_FILES_UPLOADED, array('rows'=>'2','style' => 'width:530px'))?>
			</fieldset>
			<fieldset>
			<legend><?=t('Alternate Storage Directory')?></legend>
			
			<label for="fslName"><strong><?=t('Location Name')?></strong></label>
			<?=$form->text('fslName', $fslName, array('style' => 'width:530px'))?>
			<br/><br/>
			<label for="fslDirectory"><strong><?=t('Path')?></strong></label>
			<?=$form->text('fslDirectory', $fslDirectory, array('rows' => '2', 'style' => 'width:530px'))?>
			</fieldset>
	</div>
	<div class="ccm-pane-footer">
			<?php		
				$b1 = $concrete_interface->submit(t('Save'), 'file-storage', 'right', 'primary');
				print $b1;
			?>		
	</div>
	</form>

	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>
