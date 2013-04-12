<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<? if ($this->controller->getTask() == 'edit' && is_object($composer)) { ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Edit Composer'), false, 'span8 offset2', false)?>
<form class="form-horizontal" method="post" action="<?=$this->action('submit', $composer->getComposerID())?>">
<div class="ccm-pane-body">
<?=Loader::element('composer/form/base', array('composer' => $composer));?>
</div>
<div class="ccm-pane-footer">
	<a href="<?=$this->url('/dashboard/composer/list')?>" class="btn pull-left"><?=t('Cancel')?></a>
	<button class="pull-right btn btn-primary" type="submit"><?=t('Save')?></button>
</div>
</form>


<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>


<? } else {
	$pk = PermissionKey::getByHandle('access_composer');
	 ?>

	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Composers'))?>

	<? if (count($composers) > 0) { ?>

	<table class="table table-striped">
	<thead>
		<tr>
			<th><?=t('Name')?></th>
			<td class="composer-tasks">
				<a href="<?=$this->url('/dashboard/composer/list/add')?>" class="btn btn-small btn-primary pull-right"><?=t('Add Composer')?></a>
			</td>
		</tr>
	</thead>
	<tbody>
		<? foreach($composers as $cm) { 
			$pk->setPermissionObject($cm);?>
		<tr>
			<td class="composer-name"><?=$cm->getComposerName()?></td>
			<td class="composer-tasks">
				<a href="<?=$this->action('edit', $cm->getComposerID())?>" class="btn btn-mini"><?=t('Basic Details')?></a>
				<a href="<?=$this->url('/dashboard/composer/list/form', $cm->getComposerID())?>" class="btn btn-mini"><?=t('Edit Form')?></a>
				<a href="<?=$this->url('/dashboard/composer/list/output', $cm->getComposerID())?>" class="btn btn-mini"><?=t('Output')?></a>
				<a href="javascript:void(0)" data-cmpID="<?=$cm->getComposerID()?>" dialog-title="<?=$pk->getPermissionKeyName()?>" data-pkID="<?=$pk->getPermissionKeyID()?>" data-paID="<?=$pk->getPermissionAccessID()?>" onclick="ccm_permissionLaunchDialog(this)" class="btn btn-mini"><?=t('Permissions')?></a>
				<a href="#" data-delete="<?=$cm->getComposerID()?>" class="btn btn-mini btn-danger"><?=t('Delete')?></a>

				<div style="display: none">
					<div data-delete-dialog="<?=$cm->getComposerID()?>">
						<form data-delete-form="<?=$cm->getComposerID()?>" action="<?=$this->action('delete', $cm->getComposerID())?>" method="post">
						<?=t("Delete this composer? This cannot be undone.")?>
						<?=Loader::helper('validation/token')->output('delete_composer')?>
						</form>
					</div>
				</div>
			</td>
		</tr>
		<? } ?>
	</tbody>
	</table>

	<script type="text/javascript">
	ccm_permissionLaunchDialog = function(link) {
		var dupe = $(link).attr('data-duplicate');
		if (dupe != 1) {
			dupe = 0;
		}

		jQuery.fn.dialog.open({
			title: $(link).attr('dialog-title'),
			href: '<?=REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/dialogs/composer?duplicate=' + dupe + '&cmpID=' + $(link).attr('data-cmpID') + '&pkID=' + $(link).attr('data-pkID') + '&paID=' + $(link).attr('data-paID'),
			modal: false,
			width: 500,
			height: 380
		});		
	}

	ccm_submitPermissionsDetailFormPost = function() {
		window.location.reload();
	}

	</script>

	<? } else { ?>
		<p><?=t('You have not created any composers yet.')?></p>
		<a href="<?=$this->url('/dashboard/composer/list/add')?>" class="btn btn-primary"><?=t('Add Composer')?></a>
	<? } ?>

	</div>

	<style type="text/css">
	td.composer-name {
		width: 100%;
	}

	td.composer-tasks {
		text-align: right !important;
		white-space: nowrap;
	}
	</style>

	<script type="text/javascript">
	$(function() {
		$('.composer-tasks a').tooltip();
		$('a[data-delete]').on('click', function() {
			var cmpID = $(this).attr('data-delete');
			$('div[data-delete-dialog=' + cmpID + ']').dialog({
				modal: true,
				width: 320,
				dialogClass: 'ccm-ui',
				title: '<?=t("Delete Composer")?>',
				height: 200, 
				buttons: [
					{
						'text': '<?=t("Cancel")?>',
						'class': 'btn pull-left',
						'click': function() {
							$(this).dialog('close');
						}
					},
					{
						'text': '<?=t("Delete")?>',
						'class': 'btn pull-right btn-danger',
						'click': function() {
							$('form[data-delete-form=' + cmpID + ']').submit();
						}
					}
				]
			});
		});
	});
	</script>
	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>

<? } ?>