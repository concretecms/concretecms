<?php

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $results \Concrete\Core\Search\Pagination\Pagination|null
 * @var $reports \Concrete\Core\Entity\Automation\Task[]
 */
?>

<div class="ccm-dashboard-desktop-content h-100">

    <?php View::element('dashboard/welcome', ['showCustomizeButton' => false]); ?>

    <div class="container-fluid">
        <div class="row">
            <?php $view->inc('elements/result_messages.php'); ?>
            <div class="col-lg-6">

                <div class="card h-100">
                    <div class="card-header">
                        <b><?=t('Run a Report')?></b>
                    </div>
                    <div class="card-body">
                        <?php if (isset($reports) && count($reports) > 0) { ?>

                            <form method="post" action="<?=$view->action('run_report')?>">
                                <?=$token->output('run_report')?>

                            <?php
                            $i = 0;
                            foreach ($reports as $report) {
                                $reportController = $report->getController();
                                ?>

                                <div class="row <?php if (($i + 1) < count($reports)){ ?>mb-3 border-primary border-opacity-25 border-bottom<?php } ?>">
                                    <div class="col-lg-9">
                                        <h4><?=$reportController->getName()?></h4>
                                        <p><?=$reportController->getDescription()?></p>
                                    </div>
                                    <div class="col-lg-3 text-end mb-3 mb-lg-0 d-flex align-items-center justify-content-center">
                                        <button class="btn btn-sm btn-secondary" type="submit" name="task" value="<?=$report->getID()?>"><?=t('Run Report')?></button>
                                    </div>
                                </div>

                            <?php
                                $i++;
                            } ?>

                            </form>

                        <?php } else { ?>

                            <div class="d-flex align-items-center h-100">
                                <div class="card-text text-center lead text-muted m-auto">
                                    <?=t('No health reports found.')?>
                                </div>
                            </div>

                        <?php } ?>
                    </div>
                    <div class="card-footer text-center text-muted">
                        <small>
                        <?=t('For advanced options and scheduling, run reports from the <a href="%s">Tasks Dashboard Page</a>.', URL::to('/dashboard/system/automation/tasks'))?>
                        </small>
                    </div>
                </div>

            </div>
            <div class="col-lg-6">

                <div class="card h-100">
                    <div class="card-header">
                        <b><?=t('Latest Health Reports')?></b>
                    </div>
                    <div class="card-body">
                        <?php if (isset($results) && $results->getTotalResults() > 0) { ?>

                            <table class="ccm-search-results-table">
                                <thead>
                                <tr>
                                    <th><?=t('Name')?></th>
                                    <th><?=t('Date Completed')?></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($results->getCurrentPageResults() as $result) { ?>
                                    <tr data-details-url="<?=URL::to('/dashboard/reports/health/details', $result->getID())?>">
                                        <td class="ccm-search-results-name w-50"><?=$result->getName()?></td>
                                        <td class="text-nowrap"><?=$result->getDateCompleted('F d, Y g:i a')?></td>
                                        <td class="text-nowrap text-center">
                                            <?php
                                            $grade = $result->getGrade();
                                            if ($grade) {
                                                $gradeFormatter = $grade->getFormatter();
                                                echo $gradeFormatter->getResultsListIcon();?>

                                            <?php }
                                            ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>

                        <?php } else { ?>

                            <div class="d-flex align-items-center h-100">
                                <div class="card-text text-center lead text-muted m-auto">
                                    <div class="mb-3 mt-3"><i class="opacity-50 fa fa-notes-medical" style="font-size: 5rem"></i></div>
                                    <?=t('No health results found.')?>
                                </div>
                            </div>

                        <?php } ?>
                    </div>
                    <?php if (isset($results) && $results->getTotalResults() > 0) { ?>
                        <div class="card-footer text-center">
                            <a href="<?=URL::to('/dashboard/reports/health')?>" class="btn btn-secondary"><?=t('View All Report Results')?></a>
                        </div>
                    <?php } ?>
                </div>


            </div>
        </div>
    </div>

    <?php View::element('dashboard/background_image'); ?>

</div>
