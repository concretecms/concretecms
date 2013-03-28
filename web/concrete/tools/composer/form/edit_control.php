<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?
$c = Page::getByPath('/dashboard/composer/list/form');
$cp = new Permissions($c);
$ih = Loader::helper('concrete/interface');
$control = ComposerFormLayoutSetControl::getByID($_REQUEST['cmpFormLayoutSetControlID']);
if (!is_object($control)) {
	die(t('Invalid control'));
}
$form = Loader::helper('form');

$object = $control->getComposerControlObject();
$customTemplates = $object->getComposerControlCustomTemplates();
$templates = array('' => t('** None'));
foreach($customTemplates as $template) {
	$templates[$template->getComposerControlCustomTemplateFilename()] = $template->getComposerControlCustomTemplateName();
}

if ($cp->canViewPage()) { 

	if ($_POST['task'] == 'edit' && Loader::helper('validation/token')->validate('update_set_control')) {
		$control->updateFormLayoutSetControlCustomLabel($_POST['cmpFormLayoutSetControlCustomLabel']);
		$control->updateFormLayoutSetControlCustomTemplate($_POST['cmpFormLayoutSetControlCustomTemplate']);
		Loader::element('composer/form/layout_set/control', array('control' => $control));
		exit;
	}

	?>

	<div class="ccm-ui">
		<form data-edit-set-form-control="<?=$control->getComposerFormLayoutSetControlID()?>" action="#" method="post">
		<div class="control-group">
			<?=$form->label('cmpFormLayoutSetControlCustomLabel', t('Custom Label'))?>
			<div class="controls">
				<?=$form->text('cmpFormLayoutSetControlCustomLabel', $control->getComposerFormLayoutSetControlCustomLabel())?>
			</div>
		</div>
		<div class="control-group">
			<?=$form->label('cmpFormLayoutSetControlCustomTemplate', t('Custom Template'))?>
			<div class="controls">
				<?=$form->select('cmpFormLayoutSetControlCustomTemplate', $templates, $control->getComposerFormLayoutSetControlCustomTemplate())?>
			</div>
		</div>

		<?=Loader::helper('validation/token')->output('update_set_control')?>
		</form>
		<div class="dialog-buttons">
			<button class="btn" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
			<button class="btn btn-primary pull-right" data-submit-set-form="<?=$control->getComposerFormLayoutSetControlID()?>"><?=t('Save')?></button>
		</div>

	</div>


<script type="text/javascript">
$(function() {
	$('form[data-edit-set-form-control]').on('submit', function() {
		var cmpFormLayoutSetControlID = $(this).attr('data-edit-set-form-control');
		var formData = $('form[data-edit-set-form-control=' + cmpFormLayoutSetControlID + ']').serializeArray();
		formData.push({
			'name': 'cmpFormLayoutSetControlID',
			'value': cmpFormLayoutSetControlID
		}, {
			'name': 'task',
			'value': 'edit'
		});
		jQuery.fn.dialog.showLoader();
		$.ajax({
			type: 'post',
			data: formData,
			url: '<?=REL_DIR_FILES_TOOLS_REQUIRED?>/composer/form/edit_control',
			success: function(html) {
				jQuery.fn.dialog.hideLoader();
				jQuery.fn.dialog.closeTop();
				$('div[data-composer-form-layout-control-set-control-id=<?=$control->getComposerFormLayoutSetControlID()?>]').html(html);
				$('a[data-command=edit-form-set-control]').dialog();
			}
		});		
		return false;
	});
	$('button[data-submit-set-form]').on('click', function() {
		var cmpFormLayoutSetControlID = $(this).attr('data-submit-set-form');
		$('form[data-edit-set-form-control=' + cmpFormLayoutSetControlID + ']').trigger('submit');
	});
});
</script>


<?

}