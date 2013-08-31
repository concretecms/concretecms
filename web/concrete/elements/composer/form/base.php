<?
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
$templates = array();
$pagetemplates = PageTemplate::getList();
foreach($pagetemplates as $pt) {
	$templates[$pt->getPageTemplateID()] = $pt->getPageTemplateName();
}
$targetTypes = ComposerTargetType::getList();

$cmpName = '';
$cmpPageTemplateID = array();
$cmpAllowedPageTemplates = 'A';
$token = 'add_composer';
if (is_object($composer)) {
	$token = 'update_composer';
	$cmpName = $composer->getComposerName();
	$cmpAllowedPageTemplates = $composer->getComposerAllowedPageTemplates();
	$selectedtemplates = $composer->getComposerFormSelectedPageTemplateObjects();
	foreach($selectedtemplates as $pt) {
		$cmpPageTemplateID[] = $pt->getPageTemplateID();
	}
}
?>

<?=Loader::helper('validation/token')->output($token)?>
	<div class="control-group">
		<?=$form->label('cmpName', t('Composer Name'))?>
		<div class="controls">
			<?=$form->text('cmpName', $cmpName, array('class' => 'span5'))?>
		</div>
	</div>

	<div class="control-group">
		<?=$form->label('cmpPageTemplateID', t('Default Page Template'))?>
		<div class="controls">
			<?=$form->select('cmpDefaultPageTemplateID', $templates, $cmpDefaultPageTemplateID, array('class' => 'span5'))?>
		</div>
	</div>

	<div class="control-group">
		<?=$form->label('cmpAllowedPageTemplates', t('Allowed Page Templates'))?>
		<div class="controls">
			<?=$form->select('cmpAllowedPageTemplates', array('A' => t('All'), 'C' => t('Selected Page Templates'), 'X' => t('Everything But Selected')), $cmpAllowedPageTemplates, array('class' => 'span3'))?>
		</div>
	</div>

	<div class="control-group" data-form-row="page-templates">
		<?=$form->label('cmpPageTemplateID', t('Page Templates'))?>
		<div class="controls">
			<?=$form->selectMultiple('cmpPageTemplateID', $templates, $cmpPageTemplateID, array('class' => 'span5'))?>
		</div>
	</div>

	<div class="control-group">
		<?=$form->label('cmpTargetTypeID', t('Publish Method'))?>
		<div class="controls">
			<? for ($i = 0; $i < count($targetTypes); $i++) {
				$t = $targetTypes[$i];
				if (!is_object($composer)) {
					$selected = ($i == 0);
				} else {
					$selected = $composer->getComposerTargetTypeID();
				}
				?>
				<label class="radio"><?=$form->radio('cmpTargetTypeID', $t->getComposerTargetTypeID(), $selected)?> <span><?=$t->getComposerTargetTypeName()?></label>
			<? } ?>
		</div>
	</div>

	<? foreach($targetTypes as $t) { 
		if ($t->hasOptionsForm()) {
		?>

		<div style="display: none" data-composer-target-type-id="<?=$t->getComposerTargetTypeID()?>">
			<? $t->includeOptionsForm($composer);?>
		</div>

	<? }

	} ?>

<script type="text/javascript">
$(function() {
	$('#cmpPageTemplateID').chosen();
	$('input[name=cmpTargetTypeID]').on('click', function() {
		$('div[data-composer-target-type-id]').hide();
		var cmpTargetTypeID = $('input[name=cmpTargetTypeID]:checked').val();
		$('div[data-composer-target-type-id=' + cmpTargetTypeID + ']').show();
	});
	$('input[name=cmpTargetTypeID]:checked').trigger('click');
	$('select[name=cmpAllowedPageTemplates]').on('change', function() {
		if ($(this).val() == 'A') {
			$('div[data-form-row=page-templates]').hide();
		} else {
			$('div[data-form-row=page-templates]').show();
		}
	}).trigger('change');
});
</script>