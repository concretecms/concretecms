<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<p class="lead"><?=$site->getSiteName()?></p>

<nav class="navbar navbar-default">
    <div class="container-fluid">
        <ul class="nav navbar-nav">
            <li <?php if ($active == 'details') { ?>class="active" <?php } ?>>
                <a href="<?=URL::to('/dashboard/system/multisite/sites', 'view_site', $site->getSiteID())?>"><?=t('Details')?></a>
            </li>
            <li <?php if ($active == 'domains') { ?>class="active" <?php } ?>>
                <a href="<?=URL::to('/dashboard/system/multisite/sites', 'view_domains', $site->getSiteID())?>"><?=t('Domains')?></a>
            </li>
        </ul>
    </div>
</nav>


