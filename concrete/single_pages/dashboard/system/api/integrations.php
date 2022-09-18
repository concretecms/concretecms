<?php defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var \Concrete\Core\Search\Pagination\Pagination $pagination
 */
if ($pagination->getTotalResults() > 0) { ?>

    <table class="ccm-search-results-table">
        <thead>
        <tr>
            <th><?=t('ID')?></th>
            <th><?=t('Name')?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($pagination->getCurrentPageResults() as $client) { ?>
        <tr data-details-url="<?=$view->action('view_client', $client->getIdentifier())?>">
            <td class="text-nowrap"><?=$client->getIdentifier()?></td>
            <td class="ccm-search-results-name w-100"><?=$client->getName()?></td>
        </tr>
        <?php } ?>
        </tbody>
    </table>

    <?php if ($pagination->getTotalPages() > 1) { ?>
        <?php echo $pagination->renderView('dashboard'); ?>
    <?php } ?>

<?php } else { ?>

    <p><?=t('No API integrations found.')?></p>

<?php } ?>
