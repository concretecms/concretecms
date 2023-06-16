<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Controller\SinglePage\Dashboard\Welcome\Health;

/**
 * @var Concrete\Core\Localization\Service\Date $dateService
 * @var Concrete\Core\Search\Pagination\Pagination|null $results
 * @var Concrete\Core\Entity\Automation\Task[] $reports
 * @var string $productionStatus
 * @var string $productionStatusClass
 */

?>

<div class="ccm-dashboard-desktop-content">

    <?php View::element('dashboard/welcome', ['showCustomizeButton' => false]); ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card <?=$productionStatusClass?> mb-3">
                    <div class="card-header d-flex align-items-center">
                        <b><?=t('Production Status')?></b>
                        <a href="<?=URL::to('/dashboard/system/basics/production_mode')?>" class="ms-auto btn btn-sm btn-dark"><?=t('Change Production Mode')?></a>

                    </div>
                    <div class="card-body">
                        <?php if ($productionStatus === Health::SITE_MODE_DEVELOPMENT) { ?>
                            <?=t('Your site is currently registered as a <b>development site</b>. This setting is for non-public sites. Please ensure this site is only use for local development and testing.')?>
                        <?php } ?>
                        <?php if ($productionStatus === Health::SITE_MODE_STAGING) { ?>
                            <?=t('Your site is currently registered as a <b>staging site</b>. This setting is private or semi-public sites. Please ensure this site is not serving live traffic or publicly accessible.')?>
                        <?php } ?>
                        <?php if ($productionStatus === Health::SITE_MODE_PRODUCTION_NO_TEST) { ?>
                            <?=t('Your site is currently registered as a <b>production site</b>, but has not run the "Check Site Production Status" health report. Production sites are meant to serve public traffic, and as such they should be tested for optimal performance and security configuration. Please run this report as soon as possible.')?>
                        <?php } ?>
                        <?php if ($productionStatus === Health::SITE_MODE_PRODUCTION_FAILING) { ?>
                            <?=t('Your site is currently registered as a <b>production site</b>, but it has failed its latest "Check Site Production Status" report! Please make the recommended changes and re-test as soon as possible.')?>
                        <?php } ?>
                        <?php if ($productionStatus === Health::SITE_MODE_PRODUCTION_PASSING) { ?>
                            <?=t('Your site is currently registered as a <b>production site</b>, and it has passed its latest "Check Site Production Status" report!')?>
                        <?php } ?>
                    </div>
                </div>
            </div>

        </div>
        <div class="row">
            <?php $view->inc('elements/result_messages.php'); ?>
        </div>
        <div data-vue-app="health">
            <div v-if="runningProcesses.length" class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <b><?=t('Currently Running')?></b>
                        </div>
                        <div class="card-body">
                            <running-process-list @complete-process="completeProcess" format="empty" :processes="runningProcesses"></running-process-list>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
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
                                        <?php if ($report->isRunning()) { ?>
                                            <button class="btn btn-sm btn-secondary" disabled="disabled" type="submit"><?=t('Running...')?></button>
                                        <?php } else { ?>
                                            <button class="btn btn-sm btn-secondary" type="submit" name="task" value="<?=$report->getID()?>"><?=t('Run Report')?></button>
                                        <?php } ?>
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
                    <div class="card-header d-flex">
                        <b><?=t('Latest Health Reports')?></b>
                        <a href="javascript:void(0)" onclick="window.location.reload()" class="ms-auto ccm-hover-icon"><i class="fa fa-sync"></i></a>
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
                                        <td class="text-nowrap"><?= h($dateService->formatDateTime($result->getDateCompleted())) ?></td>
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


</div>
<?php View::element('dashboard/background_image'); ?>

<script type="text/javascript">
    $(function() {
        Concrete.Vue.activateContext('backend', function (Vue, config) {
            new Vue({
                el: 'div[data-vue-app=health]',
                components: config.components,
                data: {
                    'runningProcesses': <?=json_encode($runningReportProcesses)?>,
                },
                methods: {
                    completeProcess(process) {
                        this.runningProcesses.forEach((runningProcess, i) => {
                            if (runningProcess.id == process.id) {
                                setTimeout(() => window.location.reload(), 1000)
                            }
                        })
                    }
                },
            })
        })
    });
</script>