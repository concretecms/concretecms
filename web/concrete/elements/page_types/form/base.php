<?
defined('C5_EXECUTE') or die("Access Denied.");
use \Concrete\Core\Page\Type\PublishTarget\Type\Type as PageTypePublishTargetType;
$form = Loader::helper('form');
$templates = array();
$pagetemplates = PageTemplate::getList();
foreach($pagetemplates as $pt) {
	$templates[$pt->getPageTemplateID()] = $pt->getPageTemplateName();
}
$targetTypes = PageTypePublishTargetType::getList();

$ptName = '';
$ptHandle = '';
$ptPageTemplateID = array();
$ptAllowedPageTemplates = 'A';
$ptDefaultPageTemplateID = 0;
$ptLaunchInComposer = 0;
$token = 'add_page_type';
if (is_object($pagetype)) {
	$token = 'update_page_type';
	$ptName = $pagetype->getPageTypeName();
	$ptHandle = $pagetype->getPageTypeHandle();
	$ptLaunchInComposer = $pagetype->doesPageTypeLaunchInComposer();
	$ptDefaultPageTemplateID = $pagetype->getPageTypeDefaultPageTemplateID();
	$ptAllowedPageTemplates = $pagetype->getPageTypeAllowedPageTemplates();
	$selectedtemplates = $pagetype->getPageTypeSelectedPageTemplateObjects();
	foreach($selectedtemplates as $pt) {
		$ptPageTemplateID[] = $pt->getPageTemplateID();
	}
}
?>

<?=Loader::helper('validation/token')->output($token)?>
	<div class="control-group">
		<?=$form->label('ptName', t('Page Type Name'))?>
		<div class="controls">
			<?=$form->text('ptName', $ptName, array('class' => 'span5'))?>
		</div>
	</div>

	<div class="control-group">
		<?=$form->label('ptHandle', t('Page Type Handle'))?>
		<div class="controls">
			<?=$form->text('ptHandle', $ptHandle, array('class' => 'span5'))?>
		</div>
	</div>

	<div class="control-group">
		<?=$form->label('ptPageTemplateID', t('Default Page Template'))?>
		<div class="controls">
			<?=$form->select('ptDefaultPageTemplateID', $templates, $ptDefaultPageTemplateID, array('class' => 'span5'))?>
		</div>
	</div>

	<div class="control-group">
		<?=$form->label('ptLaunchInComposer', t('Launch In Composer'))?>
		<div class="controls">
			<?=$form->select('ptLaunchInComposer', array('0' => t('No'), '1' => t('Yes')), $ptLaunchInComposer, array('class' => 'span5'))?>
		</div>
	</div>

	<div class="control-group">
		<?=$form->label('ptAllowedPageTemplates', t('Allowed Page Templates'))?>
		<div class="controls">
			<?=$form->select('ptAllowedPageTemplates', array('A' => t('All'), 'C' => t('Selected Page Templates'), 'X' => t('Everything But Selected')), $ptAllowedPageTemplates, array('class' => 'span3'))?>
		</div>
	</div>

	<div class="control-group" data-form-row="page-templates">
		<?=$form->label('ptPageTemplateID', t('Page Templates'))?>
		<div class="controls">
			<?=$form->selectMultiple('ptPageTemplateID', $templates, $ptPageTemplateID, array('class' => 'span5'))?>
		</div>
	</div>

	<div class="control-group">
		<?=$form->label('ptPublishTargetTypeID', t('Publish Method'))?>
		<div class="controls">
			<? for ($i = 0; $i < count($targetTypes); $i++) {
				$t = $targetTypes[$i];
				if (!is_object($pagetype)) {
					$selected = ($i == 0);
				} else {
					$selected = $pagetype->getPageTypePublishTargetTypeID();
				}
				?>
				<label class="radio"><?=$form->radio('ptPublishTargetTypeID', $t->getPageTypePublishTargetTypeID(), $selected)?> <span><?=$t->getPageTypePublishTargetTypeDisplayName()?></label>
			<? } ?>
		</div>
	</div>

	<? foreach($targetTypes as $t) { 
		if ($t->hasOptionsForm()) {
		?>

		<div style="display: none" data-page-type-publish-target-type-id="<?=$t->getPageTypePublishTargetTypeID()?>">
			<? $t->includeOptionsForm($pagetype);?>
		</div>

	<? }

	} ?>

<script type="text/javascript">
$(function() {
	$('#ptPageTemplateID').chosen();
	$('input[name=ptPublishTargetTypeID]').on('click', function() {
		$('div[data-page-type-publish-target-type-id]').hide();
		var ptPublishTargetTypeID = $('input[name=ptPublishTargetTypeID]:checked').val();
		$('div[data-page-type-publish-target-type-id=' + ptPublishTargetTypeID + ']').show();
	});
	$('input[name=ptPublishTargetTypeID]:checked').trigger('click');
	$('select[name=ptAllowedPageTemplates]').on('change', function() {
		if ($(this).val() == 'A') {
			$('div[data-form-row=page-templates]').hide();
		} else {
			$('div[data-form-row=page-templates]').show();
		}
	}).trigger('change');
});
</script>