<?php defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var $connection ?\Concrete\Core\Marketplace\ConnectionInterface
 */
?>
<div class="ccm-block-desktop-featured-theme">
    <div class="ccm-block-desktop-featured-theme-inner" style="display: flex">

        <img src="<?=ASSETS_URL_IMAGES?>/marketplace_upgrade_theme.png" style="height: 250px; border-right: 0"/>

        <div class="ccm-block-desktop-featured-theme-description">
            <h6><?=t('Featured Theme')?></h6/>

            <?php if ($connection) { ?>
                <h3><?=t('Browse Themes')?></h3>
                <p><?=t("Get access to hundreds of Concrete CMS themes for your site from the Concrete marketplace.")?></p>
                <a target="_blank" href="https://market.concretecms.com/themes/" class="btn btn-info"><?=t('Browse Themes')?></a>

            <?php } else { ?>

                <h3><?=t('Connect to the Marketplace')?></h3>
                <p><?=t("Connect your site to the Concrete CMS marketplace to browse themes.")?></p>
                <a target="_blank" href="<?=URL::to('/dashboard/system/basics/marketplace')?>" class="btn btn-info"><?=t('Connect Site')?></a>

            <?php } ?>
        </div>

    </div>
</div>
