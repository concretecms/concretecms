<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?
$c = Page::getByPath('/dashboard/pages/types/form');
$cp = new Permissions($c);
$ih = Loader::helper('concrete/ui');
$control = PageTypeComposerFormLayoutSetControl::getByID($_REQUEST['ptComposerFormLayoutSetControlID']);
if (!is_object($control)) {
	die(t('Invalid control'));
}
$form = Loader::helper('form');

$object = $control->getPageTypeComposerControlObject();
$customTemplates = $object->getPageTypeComposerControlCustomTemplates();
$templates = array('' => t('** None'));
foreach($customTemplates as $template) {
	$templates[$template->getPageTypeComposerControlCustomTemplateFilename()] = $template->getPageTypeComposerControlCustomTemplateName();
}

if ($cp->canViewPage()) { 

	if ($_POST['task'] == 'edit' && Loader::helper('validation/token')->validate('update_set_control')) {
		$control->updateFormLayoutSetControlCustomLabel($_POST['ptComposerFormLayoutSetControlCustomLabel']);
		$control->updateFormLayoutSetControlCustomTemplate($_POST['ptComposerFormLayoutSetControlCustomTemplate']);
		if ($object->pageTypeComposerFormControlSupportsValidation()) {
			$control->updateFormLayoutSetControlRequired($_POST['ptComposerFormLayoutSetControlRequired']);
		}
		Loader::element('page_types/composer/form/layout_set/control', array('control' => $control));
		exit;
	}

	?>

	<div class="ccm-ui">
		<form data-edit-set-form-control="<?=$control->getPageTypeComposerFormLayoutSetControlID()?>" action="#" method="post">
		<div class="control-group">
			<?=$form->label('ptComposerFormLayoutSetControlCustomLabel', t('Custom Label'))?>
			<div class="controls">
				<?=$form->text('ptComposerFormLayoutSetControlCustomLabel', $control->getPageTypeComposerFormLayoutSetControlCustomLabel())?>
			</div>
		</div>
		<div class="control-group">
			<?=$form->label('ptComposerFormLayoutSetControlCustomTemplate', t('Custom Template'))?>
			<div class="controls">
				<?=$form->select('ptComposerFormLayoutSetControlCustomTemplate', $templates, $control->getPageTypeComposerFormLayoutSetControlCustomTemplate())?>
			</div>
		</div>

		<? if ($object->pageTypeComposerFormControlSupportsValidation()) { ?>
		<div class="control-group">
			<?=$form->label('ptComposerFormLayoutSetControlRequired', t('Required'))?>
			<div class="controls">
				<label class="checkbox"><?=$form->checkbox('ptComposerFormLayoutSetControlRequired', 1, $control->isPageTypeComposerFormLayoutSetControlRequired())?> <label><?=t('Yes, require this form element')?></label></label>
			</div>
		</div>
		<? } ?>

		<?=Loader::helper('validation/token')->output('update_set_control')?>
		</form>
		<div class="dialog-buttons">
			<button class="btn" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
			<button class="btn btn-primary pull-right" data-submit-set-form="<?=$control->getPageTypeComposerFormLayoutSetControlID()?>"><?=t('Save')?></button>
		</div>

	</div>


<script type="text/javascript">
$(function() {
	$('form[data-edit-set-form-control]').on('submit', function() {
		var ptComposerFormLayoutSetControlID = $(this).attr('data-edit-set-form-control');
		var formData = $('form[data-edit-set-form-control=' + ptComposerFormLayoutSetControlID + ']').serializeArray();
		formData.push({
			'name': 'ptComposerFormLayoutSetControlID',
			'value': ptComposerFormLayoutSetControlID
		}, {
			'name': 'task',
			'value': 'edit'
		});
		jQuery.fn.dialog.showLoader();
		$.ajax({
			type: 'post',
			data: formData,
			url: '<?=REL_DIR_FILES_TOOLS_REQUIRED?>/page_types/composer/form/edit_control',
			success: function(html) {
				jQuery.fn.dialog.hideLoader();
				jQuery.fn.dialog.closeTop();
				$('div[data-page-type-composer-form-layout-control-set-control-id=<?=$control->getPageTypeComposerFormLayoutSetControlID()?>]').html(html);
				$('a[data-command=edit-form-set-control]').dialog();
			}
		});		
		return false;
	});
	$('button[data-submit-set-form]').on('click', function() {
		var ptComposerFormLayoutSetControlID = $(this).attr('data-submit-set-form');
		$('form[data-edit-set-form-control=' + ptComposerFormLayoutSetControlID + ']').trigger('submit');
	});
});
</script>


<?

}