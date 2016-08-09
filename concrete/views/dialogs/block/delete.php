<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="ccm-ui">

	<form method="post" data-dialog-form="delete-block" action="<?=$submitAction?>">

		<div class="dialog-buttons">
		<button class="btn btn-default pull-left" data-dialog-action="cancel"><?=t('Cancel')?></button>
		<button type="button" data-dialog-action="submit" class="btn btn-danger pull-right"><?=t('Delete')?></button>
		</div>

		<p class="lead"><?=t('Are you sure you wish to this block?')?></p>

		<?php if ($isMasterCollection) { ?>

			<div class="alert alert-danger"><?php echo t('Warning! This block is contained in the page type defaults. Any blocks aliased from this block in the site will be deleted. <strong>This includes blocks that have since been edited in the site.</strong>') ?></div>
		<?php
} else {
    ?>

		<p><?=t('Deleted blocks can usually be found approving a previous version of the page.')?></p>

<?php } ?>

	</form>

	<script type="text/javascript">
	$(function() {
        ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess.blockDelete');
		ConcreteEvent.subscribe('AjaxFormSubmitSuccess.blockDelete', function(e, data) {
			if (data.form == 'delete-block') {
				var editor = Concrete.getEditMode();
				var area = editor.getAreaByID(parseInt(data.response.aID));
				var block = area.getBlockByID(parseInt(data.response.bID));

				ConcreteEvent.fire('EditModeBlockDeleteComplete', {
					block: block
				});

			}
		});
	});
	</script>

</div>
