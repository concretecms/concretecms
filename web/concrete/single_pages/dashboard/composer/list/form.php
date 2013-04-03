<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper($composer->getComposerName(), false, false, false)?>

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
			<div class="ccm-composer-item-control-bar">
				<ul class="ccm-composer-item-controls">
					<li><a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/composer/form/add_control?cmpFormLayoutSetID=<?=$set->getComposerFormLayoutSetID()?>" dialog-title="<?=t('Add Form Control')?>" dialog-width="640" dialog-height="400" data-command="add-form-set-control"><i class="icon-plus-sign"></i></a></li>
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
				<? $controls = ComposerFormLayoutSetControl::getList($set);
				foreach($controls as $cnt) { ?>
					<?=Loader::element('composer/form/layout_set/control', array('control' => $cnt));?>
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
	$('a[data-command=add-form-set-control]').dialog();
	$('a[data-command=edit-form-set-control]').dialog();

	$('.ccm-composer-form-layout-control-set-inner').sortable({
		handle: 'a[data-command=move-set-control]',
		items: '.ccm-composer-form-layout-control-set-control',
		cursor: 'move',
		axis: 'y', 
		stop: function() {
			var formData = [{
				'name': 'token',
				'value': '<?=Loader::helper("validation/token")->generate("update_set_control_display_order")?>'
			}, {
				'name': 'cmpFormLayoutSetID',
				'value': $(this).parent().attr('data-composer-form-layout-control-set-id')
			}];

			$(this).find('.ccm-composer-form-layout-control-set-control').each(function() {
				formData.push({'name': 'cmpFormLayoutSetControlID[]', 'value': $(this).attr('data-composer-form-layout-control-set-control-id')});
			});

			$.ajax({
				type: 'post',
				data: formData,
				url: '<?=$this->action("update_set_control_display_order")?>',
				success: function() {}
			});

		}
	});

	$('div.ccm-composer-form-layout-control-set-inner').on('click', 'a[data-delete-set-control]', function() {
		var cmpFormLayoutSetControlID = $(this).attr('data-delete-set-control');
		$('div[data-delete-set-control-dialog=' + cmpFormLayoutSetControlID + ']').dialog({
			modal: true,
			width: 320,
			dialogClass: 'ccm-ui',
			title: '<?=t("Delete Control ")?>',
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
						jQuery.fn.dialog.showLoader();
						var formData = [{
							'name': 'token',
							'value': '<?=Loader::helper("validation/token")->generate("delete_set_control")?>'
						}, {
							'name': 'cmpFormLayoutSetControlID',
							'value': cmpFormLayoutSetControlID
						}];

						$.ajax({
							type: 'post',
							data: formData,
							url: '<?=$this->action("delete_set_control")?>',
							success: function() {
								jQuery.fn.dialog.hideLoader();
								jQuery.fn.dialog.closeAll();
								$('div[data-composer-form-layout-control-set-control-id=' + cmpFormLayoutSetControlID + ']').remove();								
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

div.ccm-composer-form-layout-control-set {
	margin-top: 20px;
}

div.ccm-composer-form-layout-control-set:last-child {
	margin-bottom: 20px;
}

div.ccm-composer-item-control-bar {
	position: relative;
}

div.ccm-composer-form-layout-control-set-control div.ccm-composer-item-control-bar {
	background-color: #fafafa;
	border-bottom: 1px solid #dedede;
	padding: 4px 10px 4px 10px;
}

div.ccm-composer-form-layout-control-set-control:last-child div.ccm-composer-item-control-bar {
	border-bottom: 0px;
}


div.ccm-composer-form-layout-control-set-inner {
	border: 1px solid #eee;
}

div.ccm-composer-form-layout-control-set-name {
	border-left: 1px solid #eee;
	border-right: 1px solid #eee;
	border-top: 1px solid #eee;
	background-color: #f1f1f1;
	padding: 4px 4px 4px 8px;
	color: #888;
	border-top-left-radius: 4px;
	border-top-right-radius: 4px;
}

ul.ccm-composer-item-controls {
	position: absolute;
	right: 8px;
	top: 7px;
}

ul.ccm-composer-item-controls a {
	color: #333;
}

ul.ccm-composer-item-controls a i {
	position: relative;
}

ul.ccm-composer-item-controls a:hover {
	text-decoration: none;
}

div.ccm-composer-item-control-bar:hover ul.ccm-composer-item-controls li {
	display: inline-block;
}

ul.ccm-composer-item-controls li {
	list-style-type: none;
	display: none;
}



</style>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>