<?php defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var \Concrete\Core\Search\Pagination\Pagination $pagination
 */
if ($pagination->getTotalResults() > 0) { ?>

    <table class="ccm-search-results-table">
        <thead>
        <tr>
            <th><?=t('Name')?></th>
            <th><?=t('Date Started')?></th>
            <th><?=t('Date Completed')?></th>
            <th class="text-center"><?=t('Findings')?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($pagination->getCurrentPageResults() as $result) { ?>
            <tr data-details-url="<?=URL::to('/dashboard/reports/health/details', $result->getID())?>">
                <td class="ccm-search-results-name w-50"><?=$result->getName()?></td>
                <td class="text-nowrap"><?=$result->getDateStarted('F d, Y g:i a')?></td>
                <td class="text-nowrap"><?=$result->getDateCompleted('F d, Y g:i a') ?? '<span class="text-muted">' . t('Running...') . '</span>'?></td>
                <td class="text-nowrap text-center"><?=$result->getTotalFindings()?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <?php if ($pagination->getTotalPages() > 1) { ?>
        <?php echo $pagination->renderView('dashboard'); ?>
    <?php } ?>

<?php } else { ?>

    <p><?=t('No health report results found.')?></p>

<?php } ?>
