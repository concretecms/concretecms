<?
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>

<div id="ccm-marketplace-wrapper">

<header class="ccm-marketplace">
    <form action="<?=$controller->action('view')?>" method="get">
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
    </form>
</header>


<header class="ccm-marketplace-list">
    <h1><?=$heading?></h1>
    <div class="ccm-marketplace-sort">
        <nav>
        <li><a href="" class="active"><?=t('Recent')?></a></li>
        <li><a href="" class=""><?=t('Price')?></a></li>
        <li><a href="" class=""><?=t('Rating')?></a></li>
        <li><a href="" class=""><?=t('Skill Level')?></a></li>
        </nav>
    </div>
</header>


<? print $innerContent; ?>

</div>

<? $this->inc('elements/footer.php');
