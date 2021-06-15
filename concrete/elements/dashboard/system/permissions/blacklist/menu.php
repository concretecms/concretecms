<?php

use Concrete\Core\Permission\IpAccessControlService;

defined('C5_EXECUTE') or die('Access Denied.');

// Arguments
/* @var Concrete\Core\Entity\Permission\IpAccessControlCategory|null $category */
/* @var int|null $type */

$categoryID = $category->getIpAccessControlCategoryID();
?>
<div class="ccm-dashboard-header-buttons">
    <div class="btn-group">
        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?= t('View') ?>
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li<?= $type === null ? ' class="active"' : '' ?>><a href="<?= URL::to('/dashboard/system/permissions/blacklist/configure', 'view', $categoryID)?>"><?= t('Options') ?></a></li>
            <li class="dropdown-divider"></li>
            <li<?= $type === IpAccessControlService::IPRANGETYPE_BLACKLIST_AUTOMATIC ? ' class="active"' : '' ?>><a href="<?= URL::to('/dashboard/system/permissions/blacklist/range', 'view', IpAccessControlService::IPRANGETYPE_BLACKLIST_AUTOMATIC, $categoryID) ?>"><?= t('Blacklisted IP addresses (automatic)') ?></a></li>
            <li<?= $type === IpAccessControlService::IPRANGETYPE_BLACKLIST_MANUAL ? ' class="active"' : '' ?>><a href="<?= URL::to('/dashboard/system/permissions/blacklist/range', 'view', IpAccessControlService::IPRANGETYPE_BLACKLIST_MANUAL, $categoryID) ?>"><?= t('Blacklisted IP addresses (manual)') ?></a></li>
            <li<?= $type === IpAccessControlService::IPRANGETYPE_WHITELIST_MANUAL ? ' class="active"' : '' ?>><a href="<?= URL::to('/dashboard/system/permissions/blacklist/range', 'view', IpAccessControlService::IPRANGETYPE_WHITELIST_MANUAL, $categoryID) ?>"><?= t('Whitelisted IP addresses') ?></a></li>
            <?php
            if ($type !== null) {
                $token = Core::make('token');
                /* @var Concrete\Core\Validation\CSRF\Token $token */
                ?>
                <li class="dropdown-divider"></li>
                <li><a href="<?= URL::to('/dashboard/system/permissions/blacklist/range', 'export', $type, $categoryID, 0, $token->generate("iprange/export/range/{$type}/{$categoryID}/0")) ?>"><?= t('Export as CSV') ?></a></li>
                <?php
                if ($type === IpAccessControlService::IPRANGETYPE_BLACKLIST_AUTOMATIC) {
                    ?>
                    <li><a href="<?= URL::to('/dashboard/system/permissions/blacklist/range', 'export', $type, $categoryID, 1, $token->generate("iprange/export/range/{$type}/{$categoryID}/1")) ?>"><?= t('Export as CSV (including expired)') ?></a></li>
                    <?php
                }
            }
            ?>
        </ul>
    </div>
</div>
