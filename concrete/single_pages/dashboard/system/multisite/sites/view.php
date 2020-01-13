<?php defined('C5_EXECUTE') or die("Access Denied."); ?>


<div class="ccm-dashboard-content-full">
    <div class="ccm-dashboard-header-buttons">
        <a class="btn btn-primary" href="<?=URL::to('/dashboard/system/multisite/sites', 'add')?>"><?=t('Add Site')?></a>
    </div>

    <div class="table-responsive">
        <table class="ccm-search-results-table">
            <thead>
            <tr>
                <th><span><?=t('Name')?></span></th>
            </tr>
            </thead>
            <tbody>

            <?php foreach($sites as $site) {
                ?>
                <tr data-details-url="<?=URL::to('/dashboard/system/multisite/sites', 'view_site', $site->getSiteID())?>">
                    <td class="ccm-search-results-name"><?=$site->getSiteName()?></td>
                </tr>
            <?php } ?>

            </tbody>
        </table>
    </div>

</div>
