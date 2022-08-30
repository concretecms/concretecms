<?php
defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var \Pagerfanta\Pagerfanta $pagination
 * @var \Concrete\Core\Health\Grade\GradeInterface $grade
 */

?>


<div class="ccm-dashboard-header-buttons">
    <div class="btn-group">
        <a href="<?=URL::to('/dashboard/reports/health'); ?>" class="btn btn-secondary"><?php echo t('Back to Reports'); ?></a>
        <a href="<?=URL::to('/dashboard/reports/health/details', 'export', $result->getId(), $token->generate('export')); ?>" class="btn btn-secondary"><?php echo t('Export Results as CSV'); ?></a>
        <button class="btn btn-danger" data-launch-modal="delete-result" data-modal-options='{"title": "<?=t('Delete')?>"}'><?=t("Delete")?></button>
    </div>
</div>

<div class="d-none">
    <div data-modal-content="delete-result">
        <form method="post" action="<?php echo $view->action('delete'); ?>">
            <?php echo Loader::helper('validation/token')->output('delete'); ?>
            <input type="hidden" name="resultID" value="<?php echo $result->getID(); ?>">
            <p><?=t('Are you sure you want to delete this report result? This cannot be undone.')?></p>
            <div class="dialog-buttons">
                <button class="btn btn-secondary float-start" data-bs-dismiss="modal"><?=t('Cancel')?></button>
                <button class="btn btn-danger float-end" onclick="$('div[data-modal-content=delete-result] form').submit()"><?=t('Delete')?></button>
            </div>
        </form>
    </div>
</div>

<?php

if (isset($pagination) && count($pagination)) { ?>

    <?php if (isset($grade)) {
        $gradeFormatter = $grade->getFormatter();
        ?>

        <div class="container">
            <div class="row">
                <div class="col-md-12">

                    <div class="mb-5 mt-5 text-center">

                        <?=$gradeFormatter->getBannerElement()->render()?>

                    </div>
                </div>
            </div>
        </div>

        <div style="padding-left: 37px">
            <h3><?=t('Findings')?></h3>
        </div>

    <?php } ?>

    <table class="ccm-search-results-table">
        <thead>
        <tr>
            <th></th>
            <th><?=t('Name')?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($pagination->getCurrentPageResults() as $result) {
            $formatter = $result->getFormatter();
            $details = $result->getDetails();
            ?>
            <tr>
                <td class="<?=$formatter->getFindingEntryTextClass()?>"><?=$formatter->getIcon()?></td>
                <td class="<?=$formatter->getFindingEntryTextClass()?> ccm-search-results-name w-100"><?=$result->getMessage()?></td>
                <td class="text-nowrap text-center <?=$formatter->getFindingEntryTextClass()?>">
                    <?php if ($details && $formatter->showDetails($details)) {
                        $detailsFormatter = $details->getFormatter();
                        echo $detailsFormatter->getFindingsListElement($details);
                        ?>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <?php if ($pagination->getNbPages() > 1) { ?>
        <?php echo $paginationView->render($pagination, function($page) {
            return '?p=' . $page;
        }); ?>
    <?php } ?>

<?php } else { ?>


<?php } ?>
