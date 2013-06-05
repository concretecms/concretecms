	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Allowed File Types'), false, 'span8 offset2', false)?>

	<form method="post" id="file-access-extensions" action="<?=$this->url('/dashboard/system/permissions/file_types', 'file_access_extensions')?>">
	<div class="ccm-pane-body">
			<?=$validation_token->output('file_access_extensions');?>
			<p>
			<?=t('Only files with the following extensions will be allowed. Separate extensions with commas. Periods and spaces will be ignored.')?>
			</p>
			<? if (UPLOAD_FILE_EXTENSIONS_CONFIGURABLE) { ?>
				<?=$form->textarea('file-access-file-types',$file_access_file_types,array('rows'=>'5','class' => 'span7'));?>

			<? } else { ?>
				<?=$file_access_file_types?>
			<? } ?>
	</div>
	<div class="ccm-pane-footer">
		<? print $concrete_interface->submit(t('Save'), 'file-access-extensions', 'right', 'primary'); ?>
	</div>
	</form>

	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>