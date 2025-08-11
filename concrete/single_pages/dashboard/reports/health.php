<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Localization\Service\Date $dateService
 * @var Concrete\Core\Search\Pagination\Pagination $pagination
 * @var string $newReportPageUrl
 */

if ($newReportPageUrl !== '') {
    ?>
    <div class="ccm-dashboard-header-buttons btn-group">
        <a href="<?= h($newReportPageUrl) ?>" class="btn btn-sm btn-primary"><?= t('New Report') ?></a>
    </div>
    <?php
}

if ($pagination->getTotalResults() > 0) {
    ?>
    <table class="ccm-search-results-table">
        <thead>
        <tr>
            <th><?= t('Name') ?></th>
            <th><?= t('Date Started') ?></th>
            <th><?= t('Date Completed') ?></th>
            <th class="text-center"><?= t('Findings') ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($pagination->getCurrentPageResults() as $result) {
            ?>
            <tr data-details-url="<?= URL::to('/dashboard/reports/health/details', $result->getID()) ?>">
                <td class="ccm-search-results-name w-50"><?= $result->getName() ?></td>
                <td class="text-nowrap"><?= h($dateService->formatDateTime($result->getDateStarted())) ?></td>
                <td class="text-nowrap"><?= h($dateService->formatDateTime($result->getDateCompleted())) ?: '<span class="text-muted">' . t('Running...') . '</span>' ?></td>
                <td class="text-nowrap text-center"><?= $result->getTotalFindings() ?></td>
                <td class="text-nowrap text-center">
                    <?php
                    $grade = $result->getGrade();
                    if ($grade) {
                        $gradeFormatter = $grade->getFormatter();
                        echo $gradeFormatter->getResultsListIcon();
                    }
                    ?>
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
    <?php
    if ($pagination->getTotalPages() > 1) {
        echo $pagination->renderView('dashboard');
    }
} else {
    ?>
    <p><?= t('No health report results found.') ?></p>
    <?php
}
