<?php
defined('C5_EXECUTE') or die('Access Denied.');
use Concrete\Core\Page\Type\PublishTarget\Type\Type as PageTypePublishTargetType;

$form = app('helper/form');
$templates = [];
$pagetemplates = PageTemplate::getList();
foreach ($pagetemplates as $pt) {
    $templates[$pt->getPageTemplateID()] = $pt->getPageTemplateDisplayName();
}
$targetTypes = PageTypePublishTargetType::getList();

$ptName = '';
$ptHandle = '';
$ptPageTemplateID = [];
$ptAllowedPageTemplates = 'A';
$ptDefaultPageTemplateID = 0;
$ptLaunchInComposer = 0;
$ptIsFrequentlyAdded = 1;
$token = 'add_page_type';
if (isset($pagetype) && is_object($pagetype)) {
    $token = 'update_page_type';
    $siteType = $pagetype->getSiteTypeObject();
    $ptName = $pagetype->getPageTypeName();
    $ptHandle = $pagetype->getPageTypeHandle();
    $ptLaunchInComposer = $pagetype->doesPageTypeLaunchInComposer();
    $ptDefaultPageTemplateID = $pagetype->getPageTypeDefaultPageTemplateID();
    $ptAllowedPageTemplates = $pagetype->getPageTypeAllowedPageTemplates();
    $ptIsFrequentlyAdded = $pagetype->isPageTypeFrequentlyAdded();
    $selectedtemplates = $pagetype->getPageTypeSelectedPageTemplateObjects();
    foreach ($selectedtemplates as $pt) {
        $ptPageTemplateID[] = $pt->getPageTemplateID();
    }
} else {
    $pagetype = null;
}
?>

<?= app('helper/validation/token')->output($token) ?>

<input type="hidden" name="siteTypeID" value="<?= $siteType->getSiteTypeID() ?>">

	<div class="form-group">
		<?= $form->label('ptName', t('Page Type Name')) ?>
    	<?= $form->text('ptName', $ptName) ?>
	</div>

	<div class="form-group">
		<?= $form->label('ptHandle', t('Page Type Handle')) ?>
		<?= $form->text('ptHandle', $ptHandle) ?>
	</div>

	<div class="form-group">
		<?= $form->label('ptDefaultPageTemplateID', t('Default Page Template')) ?>
		<?= $form->select('ptDefaultPageTemplateID', $templates, $ptDefaultPageTemplateID) ?>
	</div>

	<div class="form-group">
		<?= $form->label('ptLaunchInComposer', t('Launch in Composer?')) ?>
		<?= $form->select('ptLaunchInComposer', ['0' => t('No'), '1' => t('Yes')], $ptLaunchInComposer) ?>
	</div>

    <div class="form-group">
        <?= $form->label('ptIsFrequentlyAdded', t('Is this page type frequently added?')); ?>
        <?= $form->select('ptIsFrequentlyAdded', ['0' => t('No'), '1' => t('Yes')], $ptIsFrequentlyAdded) ?>
        <div class="help-block"><?=t('Frequently added page types are always visible in the Pages panel.') ?></div>
    </div>

	<div class="form-group">
		<?= $form->label('ptAllowedPageTemplates', t('Allowed Page Templates')); ?>
		<?= $form->select('ptAllowedPageTemplates', ['A' => t('All'), 'C' => t('Selected Page Templates'), 'X' => t('Everything But Selected')], $ptAllowedPageTemplates) ?>
	</div>

	<div class="form-group" data-form-row="page-templates">
		<?= $form->label('ptPageTemplateID', t('Page Templates')) ?>
        <div style="width: 100%">
    		<?= $form->selectMultiple('ptPageTemplateID', $templates, $ptPageTemplateID, ['style' => 'width: 100%']) ?>
        </div>
    </div>

	<div class="form-group">
		<?= $form->label('ptPublishTargetTypeID', t('Publish Method')) ?>
        <?php
            for ($i = 0; $i < count($targetTypes); $i++) {
                $t = $targetTypes[$i];
                if (!is_object($pagetype)) {
                    $selected = ($i === 0);
                } else {
                    $selected = $pagetype->getPageTypePublishTargetTypeID();
                }
        ?>
			<div class="form-check">
				<?= $form->radio('ptPublishTargetTypeID', $t->getPageTypePublishTargetTypeID(), $selected) ?>
                <?=$form->label('ptPublishTargetTypeID' . ($i + 1), $t->getPageTypePublishTargetTypeDisplayName())?>
			</div>
        <?php
            }
        ?>
	</div>

	<?php foreach ($targetTypes as $t) {
        if ($t->hasOptionsForm()) {
            ?>

		<div style="display: none" data-page-type-publish-target-type-id="<?= $t->getPageTypePublishTargetTypeID() ?>">
			<?php $t->includeOptionsForm($pagetype, $siteType) ?>
		</div>

	<?php
        }
    } ?>

<script type="text/javascript">
$(function() {
    $('#ptPageTemplateID').removeClass('form-control').selectpicker({
        width: '100%'
    });

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
