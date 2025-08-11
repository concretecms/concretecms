<?php
defined('C5_EXECUTE') or die('Access Denied.');
use Concrete\Core\Page\Type\PublishTarget\Type\Type as PageTypePublishTargetType;
use Symfony\Component\HttpFoundation\Request;

$form = app('helper/form');
$templates = [];
$pagetemplates = PageTemplate::getList();
foreach ($pagetemplates as $pt) {
    $templates[$pt->getPageTemplateID()] = $pt->getPageTemplateDisplayName();
}

$themes = [''=>t('Active Theme')];
$siteThemes = Concrete\Core\Page\Theme\Theme::getList();
foreach ($siteThemes as $theme) {
    $themes[$theme->getThemeID()] = $theme->getThemeName();
}

$targetTypes = PageTypePublishTargetType::getList();

$ptName = '';
$ptHandle = '';
$ptPageTemplateID = [];
$ptAllowedPageTemplates = 'A';
$ptDefaultPageTemplateID = 0;
$ptDefaultThemeID = 0;
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
    $ptDefaultThemeID = $pagetype->getPageTypeDefaultThemeID();
    $ptAllowedPageTemplates = $pagetype->getPageTypeAllowedPageTemplates();
    $ptIsFrequentlyAdded = $pagetype->isPageTypeFrequentlyAdded();
    $selectedtemplates = $pagetype->getPageTypeSelectedPageTemplateObjects();
    foreach ($selectedtemplates as $pt) {
        $ptPageTemplateID[] = $pt->getPageTemplateID();
    }
} else {
    $pagetype = null;
}

$request = Request::createFromGlobals();
if ($request->isMethod(Request::METHOD_POST)) {
    $ptPageTemplateID = [];
    foreach ((array) $request->request->get('ptPageTemplateID') as $_ptPageTemplateID) {
        $ptPageTemplateID[] = h($_ptPageTemplateID);
    }
}
?>

<?= app('helper/validation/token')->output($token) ?>

<input type="hidden" name="siteTypeID" value="<?= $siteType->getSiteTypeID() ?>">

	<div class="mb-3">
		<?= $form->label('ptName', t('Page Type Name')) ?>
    	<?= $form->text('ptName', $ptName) ?>
	</div>

	<div class="mb-3">
		<?= $form->label('ptHandle', t('Page Type Handle')) ?>
		<?= $form->text('ptHandle', $ptHandle) ?>
	</div>

	<div class="mb-3">
		<?= $form->label('ptDefaultPageTemplateID', t('Default Page Template')) ?>
		<?= $form->select('ptDefaultPageTemplateID', $templates, $ptDefaultPageTemplateID) ?>
	</div>

    <div class="mb-3">
        <?= $form->label('ptDefaultThemeID', t('Default Theme')) ?>
        <?= $form->select('ptDefaultThemeID', $themes, $ptDefaultThemeID) ?>
    </div>

	<div class="mb-3">
		<?= $form->label('ptLaunchInComposer', t('Launch in Composer?')) ?>
		<?= $form->select('ptLaunchInComposer', ['0' => t('No'), '1' => t('Yes')], $ptLaunchInComposer) ?>
	</div>

    <div class="mb-3">
        <?= $form->label('ptIsFrequentlyAdded', t('Is this page type frequently added?')); ?>
        <?= $form->select('ptIsFrequentlyAdded', ['0' => t('No'), '1' => t('Yes')], $ptIsFrequentlyAdded) ?>
        <div class="help-block"><?=t('Frequently added page types are always visible in the Pages panel.') ?></div>
    </div>

	<div class="mb-3">
		<?= $form->label('ptAllowedPageTemplates', t('Allowed Page Templates')); ?>
		<?= $form->select('ptAllowedPageTemplates', ['A' => t('All'), 'C' => t('Selected Page Templates'), 'X' => t('Everything But Selected')], $ptAllowedPageTemplates) ?>
	</div>

	<div class="mb-3" data-form-row="page-templates">
		<?= $form->label('ptPageTemplateID', t('Page Templates')) ?>
        <div data-vue="cms">
            <concrete-select
                    :multiple="true"
                    name="ptPageTemplateID[]"
                    :options='<?=json_encode($templates)?>'
                    :value='<?=json_encode($ptPageTemplateID)?>'>

            </concrete-select>
        </div>
    </div>

	<div class="mb-3">
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
                <?=$form->label('ptPublishTargetTypeID' . ($i + 1), $t->getPageTypePublishTargetTypeDisplayName(), ['class' => 'form-check-label'])?>
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
