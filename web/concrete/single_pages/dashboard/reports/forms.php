<?php

defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="ccm-dashboard-content-full">
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
                ?>
                <tr data-details-url="<?=$view->url('/dashboard/reports/forms', 'view_details', $node->getTreeNodeID())?>"
                    class="<?=$formatter->getSearchResultsClass()?>">
                    <td class="ccm-search-results-icon"><?=$formatter->getIconElement()?></td>
                    <td class="ccm-search-results-name"><?=$node->getTreeNodeDisplayName()?></td>
                    <td></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    $(function() {
        $('tr[data-details-url]').each(function() {
            $(this).hover(
                function() {
                    $(this).addClass('ccm-search-select-hover');
                },
                function() {
                    $(this).removeClass('ccm-search-select-hover');
                }
            )
            .on('click', function() {
                window.location.href = $(this).data('details-url');
            });
        });
    });
</script>