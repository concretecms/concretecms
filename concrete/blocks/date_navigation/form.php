<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Form\Service\Widget\PageSelector;
use Concrete\Core\Support\Facade\Application;

/** @var Concrete\Core\Page\Type\Type[] $pagetypes */

$app = Application::getFacadeApplication();
/** @var Concrete\Core\Form\Service\Form $form */
/** @var Concrete\Core\Form\Service\Widget\PageSelector $pageSelector */
$pageSelector = $app->make(PageSelector::class);

$pageTypeList = [
    0 => '** ' . t('All') . ' **',
];

foreach ($pagetypes as $ct) {
    $pageTypeList[$ct->getPageTypeID()] = $ct->getPageTypeDisplayName();
}

?>

<fieldset>
    <legend>
        <?php echo t('Filtering') ?>
    </legend>

    <div class='form-group'>
        <?php echo $form->label('title', t('By Parent Page')); ?>

        <div class="form-check">
            <?php echo $form->checkbox('filterByParent', '1', (isset($cParentID) && (int) $cParentID > 0)); ?>
            <?php echo $form->label('filterByParent', t('Filter by Parent Page'), ['class' => 'form-check-label']) ?>
        </div>

        <div id="ccm-block-related-pages-parent-page">
            <?php echo $pageSelector->selectPage('cParentID', $cParentID ?? null); ?>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->label('ptID', t('By Page Type')); ?>
        <?php echo $form->select('ptID', $pageTypeList, $ptID ?? null); ?>
    </div>
</fieldset>

<fieldset>
    <legend>
        <?php echo t('Results') ?>
    </legend>

    <div class="form-group">
        <div class="form-check">
            <?php echo $form->checkbox('redirectToResults', '1', (isset($cTargetID) && (int) $cTargetID > 0)); ?>
            <?php echo $form->label('redirectToResults', t('Redirect to Different Page on Click'), ['class' => 'form-check-label']) ?>
        </div>

        <div id="ccm-block-related-pages-search-page">
            <?php  echo $pageSelector->selectPage('cTargetID', $cTargetID ?? null); ?>
        </div>
    </div>
</fieldset>

<fieldset>
    <legend>
        <?php echo t('Formatting') ?>
    </legend>

    <div class="form-group">
        <?php echo $form->label('title', t('Title')); ?>
	    <div class="input-group">
		    <?php echo $form->text('title', $title ?? null); ?>
			<?php echo $form->select('titleFormat', \Concrete\Core\Block\BlockController::$btTitleFormats, $titleFormat ?? 'h5', ['style' => 'width:105px;flex-grow:0;', 'class' => 'form-select']); ?>
		</div>
	</div>
</fieldset>

<script type="text/javascript">
    $(function () {
        $("input[name=filterByParent]").on('change', function () {
            if ($(this).is(":checked")) {
                $('#ccm-block-related-pages-parent-page').show();
            } else {
                $('#ccm-block-related-pages-parent-page').hide();
            }
        }).trigger('change');
        $("input[name=redirectToResults]").on('change', function () {
            if ($(this).is(":checked")) {
                $('#ccm-block-related-pages-search-page').show();
            } else {
                $('#ccm-block-related-pages-search-page').hide();
            }
        }).trigger('change');
    });
</script>
