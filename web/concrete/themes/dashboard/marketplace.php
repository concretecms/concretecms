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
    	<?=$form->select('marketplaceRemoteItemSetID', $sets, $selectedSet, array('style' => 'width: 150px'))?>
        <div class="ccm-marketplace-search-input">
            <i class="fa fa-search"></i>
            <input type="search" name="keywords" value="<?=$keywords?>" />
        </div>
        <a href="" class="ccm-marketplace-search-advanced"><?=t('Advanced Search')?></a>
    </div>
</header>

<? print $innerContent; ?>

<? $this->inc('elements/footer.php');
