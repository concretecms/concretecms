<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php if (($this->controller->getTask() == 'submit' || $this->controller->getTask() == 'edit') && is_object($pagetype)) { ?>

<form class="form-horizontal" method="post" action="<?=$view->action('submit', $pagetype->getPageTypeID())?>">
<div class="ccm-pane-body">
<?=Loader::element('page_types/form/base', array('pagetype' => $pagetype));?>
</div>
<div class="ccm-dashboard-form-actions-wrapper">
<div class="ccm-dashboard-form-actions">
	<a href="<?=$view->url('/dashboard/pages/types')?>" class="btn btn-default pull-left"><?=t('Cancel')?></a>
	<button class="pull-right btn btn-primary" type="submit"><?=t('Save')?></button>
</div>
</div>

</form>

<?php } else {
	$pk = PermissionKey::getByHandle('access_page_type_permissions');
	 ?>

    <div class="ccm-dashboard-header-buttons btn-group">
        <a href="<?=$view->url('/dashboard/pages/types/organize')?>" class="btn btn-default"><?=t('Order &amp; Group')?></a>
        <a href="<?=$view->url('/dashboard/pages/types/add')?>" class="btn btn-primary"><?=t('Add Page Type')?></a>
    </div>


    <?php if (count($pagetypes) > 0) { ?>

	<table class="table table-striped">
	<thead>
		<tr>
			<th><?=t('Name')?></th>
            <th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($pagetypes as $cm) {
            $cmp = new Permissions($cm);?>
		<tr>
			<td class="page-type-name"><?=$cm->getPageTypeDisplayName()?></td>
			<td class="page-type-tasks">
                <?php if ($cmp->canEditPageType()) { ?>
    				<a href="<?=$view->action('edit', $cm->getPageTypeID())?>" class="btn btn-default btn-xs"><?=t('Basic Details')?></a>
	    			<a href="<?=$view->url('/dashboard/pages/types/form', $cm->getPageTypeID())?>" class="btn btn-default btn-xs"><?=t('Edit Form')?></a>
		    		<a href="<?=$view->url('/dashboard/pages/types/output', $cm->getPageTypeID())?>" class="btn btn-default btn-xs"><?=t('Output')?></a>
                    <a href="<?=$view->url('/dashboard/pages/types/attributes', $cm->getPageTypeID())?>" class="btn btn-default btn-xs"><?=t('Attributes')?></a>
                <?php } ?>
                <?php if ($cmp->canEditPageTypePermissions()) { ?>
					<a href="<?=$view->url('/dashboard/pages/types/permissions', $cm->getPageTypeID())?>" class="btn btn-default btn-xs"><?=t('Permissions')?></a>
				<?php } ?>
                <a href="#" data-duplicate="<?=$cm->getPageTypeID()?>" class="btn btn-default btn-xs"><?=t('Copy')?></a>
                <div style="display: none">
                    <div data-duplicate-dialog="<?=$cm->getPageTypeID()?>" class="ccm-ui">
                        <form class="form-stacked" data-duplicate-form="<?=$cm->getPageTypeID()?>" action="<?=$view->action('duplicate', $cm->getPageTypeID())?>" method="post">
                            <div class="form-group">
                                <label class="control-label"><?=t('Name')?></label>
                                <input type="text" name="ptName" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?=t('Handle')?></label>
                                <input type="text" name="ptHandle" class="form-control">
                            </div>
                            <?=Loader::helper('validation/token')->output('duplicate_page_type')?>
                        </form>
                        <div class="dialog-buttons">
                            <button onclick="jQuery.fn.dialog.closeTop()" class="btn btn-default pull-left"><?=t('Cancel')?></button>
                            <button onclick="$('form[data-duplicate-form=<?=$cm->getPageTypeID()?>]').submit()" class="btn btn-primary pull-right"><?=t('Copy')?></button>
                        </div>
                    </div>
                </div>

                <?php if ($cmp->canDeletePageType()) { ?>
    				<a href="#" data-delete="<?=$cm->getPageTypeID()?>" class="btn btn-default btn-xs btn-danger"><?=t('Delete')?></a>
                <?php } ?>
				<div style="display: none">
					<div data-delete-dialog="<?=$cm->getPageTypeID()?>" class="ccm-ui">
						<form data-delete-form="<?=$cm->getPageTypeID()?>" action="<?=$view->action('delete', $cm->getPageTypeID())?>" method="post">
						<?=t("Delete this page type? This cannot be undone.")?>
						<?=Loader::helper('validation/token')->output('delete_page_type')?>
						</form>
					</div>
				</div>
			</td>
		</tr>
		<?php } ?>
	</tbody>
	</table>

	<?php } else { ?>
		<p><?=t('You have not created any page types yet.')?></p>
		<a href="<?=$view->url('/dashboard/pages/types/add')?>" class="btn btn-primary"><?=t('Add Page Type')?></a>
	<?php } ?>

	<style type="text/css">
	td.page-type-name {
		width: 100%;
	}

	td.page-type-tasks {
		text-align: right !important;
		white-space: nowrap;
	}
	</style>

	<script type="text/javascript">
	$(function() {
		$('a[data-delete]').on('click', function() {
			var ptID = $(this).attr('data-delete');
			$('div[data-delete-dialog=' + ptID + ']').dialog({
				modal: true,
				width: 320,
				dialogClass: 'ccm-ui',
				title: '<?=t("Delete Page Type")?>',
				height: 320,
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
							$('form[data-delete-form=' + ptID + ']').submit();
						}
					}
				]
			});
		});
        $('a[data-duplicate]').on('click', function() {
            var ptID = $(this).attr('data-duplicate');
            jQuery.fn.dialog.open({
                element: 'div[data-duplicate-dialog=' + ptID + ']',
                modal: true,
                width: 320,
                title: '<?=t("Copy Page Type")?>',
                height: 280
            });
        });
    });
	</script>

<?php } ?>