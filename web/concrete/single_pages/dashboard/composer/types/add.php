<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Add Composer'), false, 'span8 offset2', false)?>
<form class="form-horizontal" method="post" action="<?=$this->action('submit')?>">
<?=Loader::helper('validation/token')->output('add_composer')?>

<div class="ccm-pane-body">
	<div class="control-group">
		<?=$form->label('cmpName', t('Composer Name'))?>
		<div class="controls">
			<?=$form->text('cmpName', array('class' => 'span5'))?>
		</div>
	</div>

	<div class="control-group">
		<?=$form->label('ctID', t('Page Type'))?>
		<div class="controls">
			<?=$form->select('ctID', $types, array('class' => 'span5'))?>
		</div>
	</div>

	<div class="control-group">
		<?=$form->label('cmpTargetTypeID', t('Publish Method'))?>
		<div class="controls">
			<? for ($i = 0; $i < count($targetTypes); $i++) {
				$t = $targetTypes[$i];
				?>
				<label class="radio"><?=$form->radio('cmpTargetTypeID', $t->getComposerTargetTypeID(), $i == 0)?> <span><?=$t->getComposerTargetTypeName()?></label>
			<? } ?>
		</div>
	</div>

	<? foreach($targetTypes as $t) { 
		if ($t->hasOptionsForm()) {
		?>

		<div style="display: none" data-composer-target-type-id="<?=$t->getComposerTargetTypeID()?>">
			<? $t->includeOptionsForm();?>
		</div>

	<? }

	} ?>

<script type="text/javascript">
$(function() {
	$('input[name=cmpTargetTypeID]').on('click', function() {
		$('div[data-composer-target-type-id]').hide();
		var cmpTargetTypeID = $('input[name=cmpTargetTypeID]:checked').val();
		$('div[data-composer-target-type-id=' + cmpTargetTypeID + ']').show();
	});
	$('input[name=cmpTargetTypeID]:checked').trigger('click');
});
</script>

</div>
<div class="ccm-pane-footer">
	<button class="pull-right btn btn-primary" type="submit"><?=t('Add')?></button>
</div>
</form>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>