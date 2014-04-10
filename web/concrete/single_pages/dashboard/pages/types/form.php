<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper($pagetype->getPageTypeName(), false, false, false)?>

<div style="display: none">
	<div id="ccm-page-type-composer-add-set">
		<form method="post" class="form-stacked" action="<?=$view->action('add_set', $pagetype->getPageTypeID())?>">
			<?=Loader::helper('validation/token')->output('add_set')?>
			<div class="control-group">
				<?=$form->label('ptComposerFormLayoutSetName', t('Set Name'))?>
				<div class="controls">
					<?=$form->text('ptComposerFormLayoutSetName')?>
				</div>
			</div>
		</form>
		<div class="dialog-buttons">
			<button class="btn btn-default pull-left" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
			<button class="btn btn-primary pull-right" onclick="$('#ccm-page-type-composer-add-set form').submit()"><?=t('Add Set')?></button>
		</div>
	</div>
</div>

<div class="ccm-pane-options">
<div class="ccm-pane-options-permanent-search">
	<a href="#" data-dialog="add_set" class="btn btn-default"><?=t('Add Set')?></a>
</div>
</div>
<div class="ccm-pane-body ccm-pane-body-footer">

<? if (count($sets) > 0) {

	foreach($sets as $set) { ?>

		<div class="ccm-page-type-composer-form-layout-control-set" data-page-type-composer-form-layout-control-set-id="<?=$set->getPageTypeComposerFormLayoutSetID()?>">
			<div class="ccm-page-type-composer-item-control-bar">
				<ul class="ccm-page-type-composer-item-controls">
					<li><a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/page_types/composer/form/add_control?ptComposerFormLayoutSetID=<?=$set->getPageTypeComposerFormLayoutSetID()?>" dialog-title="<?=t('Add Form Control')?>" dialog-width="640" dialog-height="400" data-command="add-form-set-control"><i class="glyphicon glyphicon-plus-sign"></i></a></li>
					<li><a href="#" data-command="move_set" style="cursor: move"><i class="glyphicon glyphicon-move"></i></a></li>
					<li><a href="#" data-edit-set="<?=$set->getPageTypeComposerFormLayoutSetID()?>"><i class="glyphicon glyphicon-pencil"></i></a></li>
					<li><a href="#" data-delete-set="<?=$set->getPageTypeComposerFormLayoutSetID()?>"><i class="glyphicon glyphicon-trash"></i></a></li>
				</ul>
				<div class="ccm-page-type-composer-form-layout-control-set-name" ><? if ($set->getPageTypeComposerFormLayoutSetName()) { ?><?=$set->getPageTypeComposerFormLayoutSetName()?><? } else { ?><?=t('(No Name)')?><? } ?></div>
				<div style="display: none">
					<div data-delete-set-dialog="<?=$set->getPageTypeComposerFormLayoutSetID()?>">
						<form data-delete-set-form="<?=$set->getPageTypeComposerFormLayoutSetID()?>" action="<?=$view->action('delete_set', $set->getPageTypeComposerFormLayoutSetID())?>" method="post">
						<?=t("Delete this form layout set? This cannot be undone.")?>
						<?=Loader::helper('validation/token')->output('delete_set')?>
						</form>
					</div>
				</div>

				<div style="display: none">
					<div data-edit-set-dialog="<?=$set->getPageTypeComposerFormLayoutSetID()?>">
						<form data-edit-set-form="<?=$set->getPageTypeComposerFormLayoutSetID()?>" action="<?=$view->action('update_set', $set->getPageTypeComposerFormLayoutSetID())?>" method="post">
						<div class="control-group">
							<?=$form->label('ptComposerFormLayoutSetName', t('Set Name'))?>
							<div class="controls">
								<?=$form->text('ptComposerFormLayoutSetName', $set->getPageTypeComposerFormLayoutSetName())?>
							</div>
						</div>
						<?=Loader::helper('validation/token')->output('update_set')?>
						</form>
					</div>
				</div>

			</div>
			<div class="ccm-page-type-composer-form-layout-control-set-inner">
				<? $controls = PageTypeComposerFormLayoutSetControl::getList($set);
				foreach($controls as $cnt) { ?>
					<?=Loader::element('page_types/composer/form/layout_set/control', array('control' => $cnt));?>
				<? } ?>
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
		jQuery.fn.dialog.open({
			element: '#ccm-page-type-composer-add-set',
			modal: true,
			width: 320,
			title: '<?=t("Add Control Set")?>',
			height: 120
		});
	});
	$('a[data-delete-set]').on('click', function() {
		var ptComposerFormLayoutSetID = $(this).attr('data-delete-set');
		$('div[data-delete-set-dialog=' + ptComposerFormLayoutSetID + ']').dialog({
			modal: true,
			width: 320,
			dialogClass: 'ccm-ui',
			title: '<?=t("Delete Set ")?>',
			height: 200, 
			buttons: [
				{
					'text': '<?=t("Cancel")?>',
					'class': 'btn btn-default pull-left',
					'click': function() {
						$(this).dialog('close');
					}
				},
				{
					'text': '<?=t("Delete")?>',
					'class': 'btn pull-right btn-danger',
					'click': function() {
						$('form[data-delete-set-form=' + ptComposerFormLayoutSetID + ']').submit();
					}
				}
			]
		});
	});
	$('a[data-edit-set]').on('click', function() {
		var ptComposerFormLayoutSetID = $(this).attr('data-edit-set');
		$('div[data-edit-set-dialog=' + ptComposerFormLayoutSetID + ']').dialog({
			modal: true,
			width: 320,
			dialogClass: 'ccm-ui',
			title: '<?=t("Update Set ")?>',
			height: 235, 
			buttons: [
				{
					'text': '<?=t("Cancel")?>',
					'class': 'btn btn-default pull-left',
					'click': function() {
						$(this).dialog('close');
					}
				},
				{
					'text': '<?=t("Update")?>',
					'class': 'btn pull-right btn-primary',
					'click': function() {
						$('form[data-edit-set-form=' + ptComposerFormLayoutSetID + ']').submit();
					}
				}
			]
		});
	});
	$('div.ccm-pane-body').sortable({
		handle: 'a[data-command=move_set]',
		items: '.ccm-page-type-composer-form-layout-control-set',
		cursor: 'move',
		axis: 'y', 
		stop: function() {
			var formData = [{
				'name': 'token',
				'value': '<?=Loader::helper("validation/token")->generate("update_set_display_order")?>'
			}, {
				'name': 'ptID',
				'value': <?=$pagetype->getPageTypeID()?>
			}];
			$('.ccm-page-type-composer-form-layout-control-set').each(function() {
				formData.push({'name': 'ptComposerFormLayoutSetID[]', 'value': $(this).attr('data-page-type-composer-form-layout-control-set-id')});
			});
			$.ajax({
				type: 'post',
				data: formData,
				url: '<?=$view->action("update_set_display_order")?>',
				success: function() {

				}
			});
		}
	});
	$('a[data-command=add-form-set-control]').dialog();
	$('a[data-command=edit-form-set-control]').dialog();

	$('.ccm-page-type-composer-form-layout-control-set-inner').sortable({
		handle: 'a[data-command=move-set-control]',
		items: '.ccm-page-type-composer-form-layout-control-set-control',
		cursor: 'move',
		axis: 'y', 
		stop: function() {
			var formData = [{
				'name': 'token',
				'value': '<?=Loader::helper("validation/token")->generate("update_set_control_display_order")?>'
			}, {
				'name': 'ptComposerFormLayoutSetID',
				'value': $(this).parent().attr('data-page-type-composer-form-layout-control-set-id')
			}];

			$(this).find('.ccm-page-type-composer-form-layout-control-set-control').each(function() {
				formData.push({'name': 'ptComposerFormLayoutSetControlID[]', 'value': $(this).attr('data-page-type-composer-form-layout-control-set-control-id')});
			});

			$.ajax({
				type: 'post',
				data: formData,
				url: '<?=$view->action("update_set_control_display_order")?>',
				success: function() {}
			});

		}
	});

	$('div.ccm-page-type-composer-form-layout-control-set-inner').on('click', 'a[data-delete-set-control]', function() {
		var ptComposerFormLayoutSetControlID = $(this).attr('data-delete-set-control');
		$('div[data-delete-set-control-dialog=' + ptComposerFormLayoutSetControlID + ']').dialog({
			modal: true,
			width: 320,
			dialogClass: 'ccm-ui',
			title: '<?=t("Delete Control ")?>',
			height: 200, 
			buttons: [
				{
					'text': '<?=t("Cancel")?>',
					'class': 'btn btn-default pull-left',
					'click': function() {
						$(this).dialog('close');
					}
				},
				{
					'text': '<?=t("Delete")?>',
					'class': 'btn pull-right btn-danger',
					'click': function() {
						jQuery.fn.dialog.showLoader();
						var formData = [{
							'name': 'token',
							'value': '<?=Loader::helper("validation/token")->generate("delete_set_control")?>'
						}, {
							'name': 'ptComposerFormLayoutSetControlID',
							'value': ptComposerFormLayoutSetControlID
						}];

						$.ajax({
							type: 'post',
							data: formData,
							url: '<?=$view->action("delete_set_control")?>',
							success: function() {
								jQuery.fn.dialog.hideLoader();
								jQuery.fn.dialog.closeAll();
								$('div[data-page-type-composer-form-layout-control-set-control-id=' + ptComposerFormLayoutSetControlID + ']').remove();								
							}
						});

					}
				}
			]
		});
		return false;
	});

});
</script>

<style type="text/css">

div.ccm-page-type-composer-form-layout-control-set {
	margin-top: 20px;
}

div.ccm-page-type-composer-form-layout-control-set:last-child {
	margin-bottom: 20px;
}

div.ccm-page-type-composer-item-control-bar {
	position: relative;
}

div.ccm-page-type-composer-form-layout-control-set-control div.ccm-page-type-composer-item-control-bar {
	background-color: #fafafa;
	border-bottom: 1px solid #dedede;
	padding: 4px 10px 4px 10px;
}

div.ccm-page-type-composer-form-layout-control-set-control:last-child div.ccm-page-type-composer-item-control-bar {
	border-bottom: 0px;
}


div.ccm-page-type-composer-form-layout-control-set-inner {
	border: 1px solid #eee;
}

div.ccm-page-type-composer-form-layout-control-set-name {
	border-left: 1px solid #eee;
	border-right: 1px solid #eee;
	border-top: 1px solid #eee;
	background-color: #f1f1f1;
	padding: 4px 4px 4px 8px;
	color: #888;
	border-top-left-radius: 4px;
	border-top-right-radius: 4px;
}

ul.ccm-page-type-composer-item-controls {
	position: absolute;
	right: 8px;
	top: 7px;
}

ul.ccm-page-type-composer-item-controls a {
	color: #333;
}

ul.ccm-page-type-composer-item-controls a i {
	position: relative;
}

ul.ccm-page-type-composer-item-controls a:hover {
	text-decoration: none;
}

div.ccm-page-type-composer-item-control-bar:hover ul.ccm-page-type-composer-item-controls li {
	display: inline-block;
}

ul.ccm-page-type-composer-item-controls li {
	list-style-type: none;
	display: none;
}



</style>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>