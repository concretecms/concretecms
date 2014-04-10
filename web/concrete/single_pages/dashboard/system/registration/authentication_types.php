<?php defined('C5_EXECUTE') or die("Access Denied.");
$label = $editmode ? t('Edit %s Authentication Type',$at->getAuthenticationTypeName()) : t('Authentication Types');
echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper($label, t('Manage Authentication Types'), false, false);
$h = Loader::helper('concrete/ui');
?>
<style>
i.handle {
	cursor:move;
}
tbody tr {
	cursor:pointer;
}
</style>
<?php 
if ($editmode) {
	?>
	<form class='form-horizontal' method='post' action='<?=$view->action('save',$at->getAuthenticationTypeID())?>'>
	<?php
}
?>
<div class='ccm-pane-body'>
	<?php
	if (!$editmode) {
		?>
		<fieldset>
			<h2><?=t('Authentication Types')?></h2>
			<table class='table'>
				<thead>
					<tr>
						<th><?=t('ID')?></th>
						<th><?=t('Display Name')?></th>
						<th><?=t('Handle')?></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach($ats as $at) {
						?>
						<tr data-authID="<?=$at->getAuthenticationTypeID()?>" class='<?=$at->isEnabled()?'success':'error'?>'>
							<td><?=$at->getAuthenticationTypeID()?></td>
							<td><?=$at->getAuthenticationTypeName()?></td>
							<td><?=$at->getAuthenticationTypeHandle()?></td>
							<td style='text-align:right'><i class='handle icon-resize-vertical'></i></td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
		</fieldset>
		<script type="text/javascript">
		(function($,location){
			"use Strict";
			$(function(){
				var sortableTable = $('table.table tbody');
				sortableTable.sortable({
					handle:'i.handle',
					helper:function(e,ui){
						ui.children().each(function() {
							var me = $(this);
							me.width(me.width());
						});
						return ui;
					},
					stop:function(e,ui){
						var order = [];
						sortableTable.children().each(function() {
							var me = $(this);
							order.push(me.attr('data-authID'));
						});
						$.post('<?=$view->action('reorder')?>',{order:order});
					}
				});
				$('tbody tr').click(function(){
					var me = $(this);
					location.href = "<?=$view->action('edit')?>"+me.attr('data-authID');
				});
			});
		})(jQuery,window.location);
		</script>
		<?php
	} else {
		?>
		<fieldset>
			<legend>Edit <?=$at->getAuthenticationTypeName()?> Authentication</legend>
			<br>
			<?=$at->renderTypeForm()?>
		</fieldset>
		<?php
	}
	?>
</div>
<?php
if ($editmode) {
	?>
	<div class='ccm-pane-footer'>
		<a href='<?=$view->action('')?>' class='btn pull-left'><?=t('Cancel')?></a>
		<span class='pull-right'>
			<a href='<?=$view->action($at->isEnabled()?'disable':'enable',$at->getAuthenticationTypeID())?>' class='btn btn-<?=$at->isEnabled()?'danger':'success'?>'>
				<?=$at->isEnabled()?t('Disable'):t('Enable')?></a>
			<button class='btn btn-primary'><?=t('Save')?></button>
		</span>
	</div>
	<?php
}
?>