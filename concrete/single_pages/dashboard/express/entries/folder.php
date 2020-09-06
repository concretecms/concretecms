<?php

defined('C5_EXECUTE') or die("Access Denied.");

?>

<div class="table-responsive">
    <table class="ccm-search-results-table">
        <thead>
        <tr>
            <th></th>
            <th class=""><span><?=t('Name')?></span></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($nodes as $node) {
            $formatter = $node->getListFormatter();
            $np = new Permissions($node);
            if ($np->canViewExpressEntries()) {
                if ($node instanceof \Concrete\Core\Tree\Node\Type\ExpressEntryResults) {
                    $detailsURL = $view->action('results', $node->getEntity()->getID());
                } else {
                    $detailsURL = $view->action('view', $node->getTreeNodeID());
                }
            ?>
            <tr data-details-url="<?=$detailsURL?>"
                class="<?=$formatter->getSearchResultsClass()?>">
                <td class="ccm-search-results-icon"><?=$formatter->getIconElement()?></td>
                <td class="ccm-search-results-name"><?=$node->getTreeNodeDisplayName()?></td>
                <td></td>
            </tr>
        <?php }
        } ?>
        </tbody>
    </table>
</div>
