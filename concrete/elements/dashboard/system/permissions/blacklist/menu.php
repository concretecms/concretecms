<?php
use Concrete\Core\Permission\IPService;

defined('C5_EXECUTE') or die('Access Denied.');

// Arguments
/* @var int|null $type */
?>
<div class="ccm-dashboard-header-buttons">
    <div class="btn-group">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?= t('View') ?>
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li<?= ($type === null) ? ' class="active"' : '' ?>><a href="<?= URL::to('/dashboard/system/permissions/blacklist')?>"><?= t('Options') ?></a></li>
            <li class="divider"></li>
            <li<?= ($type === IPService::IPRANGETYPE_BLACKLIST_AUTOMATIC) ? ' class="active"' : '' ?>><a href="<?= URL::to('/dashboard/system/permissions/blacklist/range', 'view', IPService::IPRANGETYPE_BLACKLIST_AUTOMATIC) ?>"><?= t('Blacklisted IP addresses (automatic)') ?></a></li>
            <li<?= ($type === IPService::IPRANGETYPE_BLACKLIST_MANUAL) ? ' class="active"' : '' ?>><a href="<?= URL::to('/dashboard/system/permissions/blacklist/range', 'view', IPService::IPRANGETYPE_BLACKLIST_MANUAL) ?>"><?= t('Blacklisted IP addresses (manual)') ?></a></li>
            <li<?= ($type === IPService::IPRANGETYPE_WHITELIST_MANUAL) ? ' class="active"' : '' ?>><a href="<?= URL::to('/dashboard/system/permissions/blacklist/range', 'view', IPService::IPRANGETYPE_WHITELIST_MANUAL) ?>"><?= t('Whitelisted IP addresses') ?></a></li>
            <?php
            if ($type !== null) {
                $token = Core::make('token');
                /* @var Concrete\Core\Validation\CSRF\Token $token */
                ?>
                <li class="divider"></li>
                <li><a href="<?= URL::to('/dashboard/system/permissions/blacklist/range', 'export', $type, 0, $token->generate("iprange/export/range/{$type}/0")) ?>"><?= t('Export as CSV') ?></a></li>
                <?php
                if ($type === IPService::IPRANGETYPE_BLACKLIST_AUTOMATIC) {
                    ?>
                    <li><a href="<?= URL::to('/dashboard/system/permissions/blacklist/range', 'export', $type, 1, $token->generate("iprange/export/range/{$type}/1")) ?>"><?= t('Export as CSV (including expired)') ?></a></li>
                    <?php
                }
            }
            ?>
        </ul>
    </div>
</div>
