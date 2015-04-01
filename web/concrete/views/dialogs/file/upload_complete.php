<?
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="ccm-ui" id="ccm-file-manager-upload-complete" data-container="editable-fields">
			<fieldset>
				<legend><?=t('Properties')?></legend>
				<? if (count($files) > 1) { ?>
					<p><?=t('Properties like name, description and tags are unavailable when uploading multiple files.')?></p>
				<? } else { ?>
					<? Loader::element('files/properties', array('fv' =>  $files[0]->getVersion(), 'mode' => 'bulk'))?>
				<? } ?>
			</fieldset>
			<fieldset>
				<legend><?=t('Sets')?>
					<button type="button" data-action="manage-file-sets" class="btn btn-xs pull-right btn-default"><?=t('Add/Remove Sets')?></button>
				</legend>

				<section>
					<? if (count($filesets) > 0) { ?>
						<? foreach($filesets as $set) { ?>
							<div><?=$set->getFileSetDisplayName()?></div>
						<? } ?>
					<? } else { ?>
						<p><?=t('None')?></p>
					<? } ?>
				</section>
			</fieldset>
			<fieldset>
				<legend><?=t('Custom Attributes')?></legend>
				<section>
					<?
					Loader::element('attribute/editable_list', array(
						'attributes' => $attributes,
						'objects' => $files,
						'saveAction' => $controller->action('update_attribute'),
						'clearAction' => $controller->action('clear_attribute'),
						'permissionsCallback' => function($ak, $permissionsArguments) {
							return true;
						}
					));?>
				</section>

			</fieldset>
		</div>
	</div>
</div>


<script type="text/javascript">
$(function() {
	$('div[data-container=editable-fields]').concreteEditableFieldContainer({
		data: [
			<? foreach($files as $f) { ?>
			{'name': 'fID[]', 'value': '<?=$f->getFileID()?>'},
			<? } ?>
		]
	});

	$('button[data-action=manage-file-sets]').on('click', function() {
		<?
		$data = '';
		for ($i = 0; $i < count($files); $i++) {
			$f = $files[$i];
			$data .= 'fID[]=' . $f->getFileID();
			if ($i + 1 < count($files)) {
				$data .= '&';
			}
		}
		?>
		$.fn.dialog.open({
			width: '500',
			height: '400',
			href: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/file/bulk/sets',
			modal: true,
			data: '<?=$data?>',
			title: ccmi18n_filemanager.sets
		});

	});
});
</script>