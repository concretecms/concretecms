<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="ccm-block-top-navigation-bar">
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?=$home->getCollectionLink()?>">
                <?php if ($logo && ($includeBrandLogo && $includeBrandText)) { ?>
                    <img src="<?=$logo->getURL()?>" class="d-inline-block align-text-center">
                    <?=$brandingText?>
                <?php } ?>
                <?php if ($logo && ($includeBrandLogo && !$includeBrandText)) { ?>
                    <img src="<?=$logo->getURL()?>">
                <?php } ?>
                <?php if (!$includeBrandLogo && $includeBrandText) { ?>
                    <?=$brandingText?>
                <?php } ?>
            </a>

            <?php if ($includeNavigation) { ?>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#top-navigationn-bar-<?=$bID?>" aria-controls="#top-navigationn-bar-<?=$bID?>" aria-expanded="false" aria-label="<?=t('Toggle Navigation')?>">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="top-navigationn-bar-<?=$bID?>">
                    <ul class="navbar-nav">
                        <?php foreach ($navigation->getItems() as $item) {
                            /**
                             * @var $item \Concrete\Core\Navigation\Item\PageItem
                             */
                            if (count($item->getChildren()) > 0) { ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" data-concrete-toggle="dropdown" href="<?=$item->getUrl()?>">
                                        <?=$item->getName()?>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <?php foreach ($item->getChildren() as $dropdownChild) { ?>
                                            <li><a class="dropdown-item" href="<?=$dropdownChild->getUrl()?>"><?=$dropdownChild->getName()?></a></li>
                                        <?php } ?>
                                    </ul>
                                </li>
                            <?php } else { ?>
                                <li class="nav-item"><a class="nav-link" href="<?=$item->getUrl()?>"><?=$item->getName()?></a></li>
                            <?php } ?>
                        <?php } ?>
                    </ul>
                </div>
            <?php } ?>
            <?php if ($includeSearchInput) { ?>
                <form class="d-flex" method="get" action="<?=$searchAction?>">
                    <div class="input-group">
                        <input class="form-control border-end-0 border" type="search" name="query" placeholder="<?=t('Search')?>" aria-label="<?=t('Search')?>">
                        <span class="input-group-append">
                            <button class="btn bg-white border-start-0 border" type="button">
                                <i class="fas fa-search text-secondary"></i>
                            </button>
                        </span>
                    </div>
                </form>
            <?php } ?>
        </div>
    </nav>
</div>
