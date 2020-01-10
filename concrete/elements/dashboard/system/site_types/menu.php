<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<p class="lead"><?=$type->getSiteTypeName()?></p>

<nav class="navbar navbar-default">
    <div class="container-fluid">
        <ul class="nav navbar-nav">
            <li <?php if ($active == 'details') { ?>class="active" <?php } ?>>
                <a href="<?=URL::to('/dashboard/system/multisite/types', 'view_type', $type->getSiteTypeID())?>"><?=t('Details')?></a>
            </li>
            <li <?php if ($active == 'edit') { ?>class="active" <?php } ?>>
                <a href="<?=URL::to('/dashboard/system/multisite/types/', 'edit', $type->getSiteTypeID())?>"><?=t('Edit')?></a>
            </li>
            <li <?php if ($active == 'skeleton') { ?>class="active" <?php } ?>>
                <a href="<?=URL::to('/dashboard/system/multisite/types', 'view_skeleton', $type->getSiteTypeID())?>"><?=t('Skeleton')?></a>
            </li>
            <li <?php if ($active == 'groups') { ?>class="active" <?php } ?>>
                <a href="<?=URL::to('/dashboard/system/multisite/types', 'view_groups', $type->getSiteTypeID())?>"><?=t('Default Groups')?></a>
            </li>
            <li <?php if ($active == 'attributes') { ?>class="active" <?php } ?>>
                <a href="<?=URL::to('/dashboard/system/multisite/types', 'view_attributes', $type->getSiteTypeID())?>"><?=t('Attributes')?></a>
            </li>
        </ul>
    </div>
</nav>