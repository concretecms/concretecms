<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var $latestResult \Concrete\Core\Entity\Health\Report\Result|null
 */
if (isset($latestResult)) {
    $grade = $latestResult->getGrade();
    if ($grade) {
        $formatter = $grade->getFormatter();
    }
}
?>

<div class="card">
    <div class="card-header border-0">
        <b><?=t('Latest Site Health Result')?></b>
    </div>
    <div class="card-body text-center">
        <?php if ($latestResult) { ?>

            <h5><?=$latestResult->getName()?></h5>

            <?php if ($grade && $formatter) { ?>
                <div class="ms-auto me-auto w-50 mt-5">
                    <?php $formatter->getBannerElement()->render()?>
                </div>
            <?php } else { ?>

                <div class="ms-auto me-auto w-50 mt-5">
                    <div class="card">
                        <div class="card-header"><b><?=t('Total Findings')?></b></div>
                        <div class="card-body">
                            <h1 class="display-1"><?=$latestResult->getTotalFindings()?></h1>
                        </div>
                    </div>
                </div>

            <?php } ?>

            <div class="text-center mt-3">
                <a class="btn btn-secondary" href="<?=URL::to('/dashboard/reports/health', 'details', $latestResult->getId())?>"><?=t("View Details")?></a>
            </div>


        <?php } else { ?>

            <div class="d-flex align-items-center h-100">
                <div class="card-text text-center lead text-muted m-auto">
                    <div class="mb-3 mt-3"><i class="opacity-50 fa fa-notes-medical" style="font-size: 5rem"></i></div>
                    <?=t('No health results found.')?>
                </div>
            </div>


        <?php } ?>

    </div>
    <div class="card-footer border-0 d-flex">
        <a href="<?=URL::to('/dashboard/welcome/health')?>" class="btn btn-secondary mx-auto"><?=t('Check Site Health')?></a>
    </div>
</div>