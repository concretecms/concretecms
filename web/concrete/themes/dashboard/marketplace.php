<?
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>

<header class="ccm-marketplace">
    <div class="ccm-marketplace-types">
        <nav>
        <li><a href="<?=URL::to('/dashboard/extend/themes')?>" <? if ($type == 'themes') { ?>class="active"<? } ?>><?=t('Themes')?></a></li>
        <li><a href="<?=URL::to('/dashboard/extend/addons')?>" <? if ($type == 'addons') { ?>class="active"<? } ?>><?=t('Add-Ons')?></a></li>
        </nav>
    </div>
    <div class="ccm-marketplace-search">

    </div>
</header>

<? print $innerContent; ?>

<? $this->inc('elements/footer.php');
