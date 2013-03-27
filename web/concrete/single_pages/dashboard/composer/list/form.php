<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Composer Form'), false, false, false)?>

<div style="display: none">
	<div id="ccm-composer-add-set">
		<form method="post" class="form-stacked" action="<?=$this->action('add_set', $composer->getComposerID())?>">
			<?=Loader::helper('validation/token')->output('add_set')?>
			<div class="control-group">
				<?=$form->label('cmpFormLayoutSetName', t('Set Name'))?>
				<div class="controls">
					<?=$form->text('cmpFormLayoutSetName')?>
				</div>
			</div>
		</form>
	</div>
</div>

<div class="ccm-pane-options">
<div class="ccm-pane-options-permanent-search">
	<a href="#" data-dialog="add_set" class="btn"><?=t('Add Set')?></a>
</div>
</div>
<div class="ccm-pane-body ccm-pane-body-footer">

<? if (count($sets) > 0) {

	foreach($sets as $set) { ?>

		<div class="ccm-composer-form-layout-control-set" data-composer-form-layout-control-set-id="<?=$set->getComposerFormLayoutSetID()?>">
			<div class="ccm-composer-form-layout-control-set-bar">
				<ul class="ccm-composer-form-layout-control-set-controls">
					<li><a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/composer/form/add_control?cmpFormLayoutSetID=<?=$set->getComposerFormLayoutSetID()?>" dialog-title="<?=t('Add Form Control')?>" dialog-width="640" dialog-height="400" data-command="add_form_element"><i class="icon-plus-sign"></i></a></li>
					<li><a href="#" data-command="move_set" style="cursor: move"><i class="icon-move"></i></a></li>
					<li><a href="#" data-edit-set="<?=$set->getComposerFormLayoutSetID()?>"><i class="icon-pencil"></i></a></li>
					<li><a href="#" data-delete-set="<?=$set->getComposerFormLayoutSetID()?>"><i class="icon-trash"></i></a></li>
				</ul>
				<div class="ccm-composer-form-layout-control-set-name" ><? if ($set->getComposerFormLayoutSetName()) { ?><?=$set->getComposerFormLayoutSetName()?><? } else { ?><?=t('(No Name)')?><? } ?></div>
				<div style="display: none">
					<div data-delete-set-dialog="<?=$set->getComposerFormLayoutSetID()?>">
						<form data-delete-set-form="<?=$set->getComposerFormLayoutSetID()?>" action="<?=$this->action('delete_set', $set->getComposerFormLayoutSetID())?>" method="post">
						<?=t("Delete this form layout set? This cannot be undone.")?>
						<?=Loader::helper('validation/token')->output('delete_set')?>
						</form>
					</div>
				</div>

				<div style="display: none">
					<div data-edit-set-dialog="<?=$set->getComposerFormLayoutSetID()?>">
						<form data-edit-set-form="<?=$set->getComposerFormLayoutSetID()?>" action="<?=$this->action('update_set', $set->getComposerFormLayoutSetID())?>" method="post">
						<div class="control-group">
							<?=$form->label('cmpFormLayoutSetName', t('Set Name'))?>
							<div class="controls">
								<?=$form->text('cmpFormLayoutSetName', $set->getComposerFormLayoutSetName())?>
							</div>
						</div>
						<?=Loader::helper('validation/token')->output('update_set')?>
						</form>
					</div>
				</div>

			</div>
			<div class="ccm-composer-form-layout-control-set-inner">

			</div>
		</div>

	<? } ?>
<? } else { ?>
	<p><?=t('You have not added any composer form layout control sets.')?></p>
<? } ?>

</div>


<script type="text/javascript">
$(function() {
	$('a[data-dialog=add_set]').on('click', function() {
		$("#ccm-composer-add-set").dialog({
			modal: true,
			width: 320,
			dialogClass: 'ccm-ui',
			title: '<?=t("Add Control Set")?>',
			height: 235, 
			buttons: [
				{
					'text': '<?=t("Cancel")?>',
					'class': 'btn pull-left',
					'click': function() {
						$(this).dialog('close');
					}
				},
				{
					'text': '<?=t("Add Set")?>',
					'class': 'btn pull-right btn-primary',
					'click': function() {
						$('#ccm-composer-add-set form').submit();
					}
				}
			]
		});
	});
	$('a[data-delete-set]').on('click', function() {
		var cmpFormLayoutSetID = $(this).attr('data-delete-set');
		$('div[data-delete-set-dialog=' + cmpFormLayoutSetID + ']').dialog({
			modal: true,
			width: 320,
			dialogClass: 'ccm-ui',
			title: '<?=t("Delete Set ")?>',
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
						$('form[data-delete-set-form=' + cmpFormLayoutSetID + ']').submit();
					}
				}
			]
		});
	});
	$('a[data-edit-set]').on('click', function() {
		var cmpFormLayoutSetID = $(this).attr('data-edit-set');
		$('div[data-edit-set-dialog=' + cmpFormLayoutSetID + ']').dialog({
			modal: true,
			width: 320,
			dialogClass: 'ccm-ui',
			title: '<?=t("Update Set ")?>',
			height: 235, 
			buttons: [
				{
					'text': '<?=t("Cancel")?>',
					'class': 'btn pull-left',
					'click': function() {
						$(this).dialog('close');
					}
				},
				{
					'text': '<?=t("Update")?>',
					'class': 'btn pull-right btn-primary',
					'click': function() {
						$('form[data-edit-set-form=' + cmpFormLayoutSetID + ']').submit();
					}
				}
			]
		});
	});
	$('div.ccm-pane-body').sortable({
		handle: 'a[data-command=move_set]',
		items: '.ccm-composer-form-layout-control-set',
		cursor: 'move',
		axis: 'y', 
		stop: function() {
			var formData = [{
				'name': 'token',
				'value': '<?=Loader::helper("validation/token")->generate("update_set_display_order")?>'
			}, {
				'name': 'cmpID',
				'value': <?=$composer->getComposerID()?>
			}];
			$('.ccm-composer-form-layout-control-set').each(function() {
				formData.push({'name': 'cmpFormLayoutSetID[]', 'value': $(this).attr('data-composer-form-layout-control-set-id')});
			});
			$.ajax({
				type: 'post',
				data: formData,
				url: '<?=$this->action("update_set_display_order")?>',
				success: function() {

				}
			});
		}
	});
	$('a[data-command=add_form_element]').dialog();
});
</script>

<style type="text/css">

div.ccm-composer-form-layout-control-set {
	margin-top: 20px;
}

div.ccm-composer-form-layout-control-set:last-child {
	margin-bottom: 20px;
}

div.ccm-composer-form-layout-control-set-bar {
	position: relative;
}

div.ccm-composer-form-layout-control-set-inner {
	border: 1px solid #eee;
	padding: 10px;
	min-height: 50px;
}

div.ccm-composer-form-layout-control-set-name {
	border-left: 1px solid #eee;
	border-right: 1px solid #eee;
	border-top: 1px solid #eee;
	background-color: #fafafa;
	padding: 4px 4px 4px 8px;
	color: #888;
	border-top-left-radius: 4px;
	border-top-right-radius: 4px;
}

ul.ccm-composer-form-layout-control-set-controls {
	position: absolute;
	right: 8px;
	top: 5px;
}

div.ccm-composer-form-layout-control-set-bar:hover ul.ccm-composer-form-layout-control-set-controls li {
	display: inline-block;
}

ul.ccm-composer-form-layout-control-set-controls li {
	list-style-type: none;
	display: none;
}



</style>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>