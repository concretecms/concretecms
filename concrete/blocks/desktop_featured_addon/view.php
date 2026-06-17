<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="ccm-block-desktop-featured-addon">
	<div class="ccm-block-desktop-featured-addon-inner">

	<h6><?=t('Featured Add-On')?></h6/>

    <img src="<?=ASSETS_URL_IMAGES?>/marketplace_upgrade_addon.png" width="80" height="80" />

    <?php if ($connection) { ?>

        <h3><?=t('Browse Add-Ons')?></h3>
        <p><?=t("Get access to hundreds of Concrete CMS add-ons from the Concrete marketplace.")?></p>
        <a target="_blank" href="https://market.concretecms.com/addons/" class="btn btn-info"><?=t('Browse Add-Ons')?></a>

    <?php } else { ?>

        <h3><?=t('Connect to the Marketplace')?></h3>
        <p><?=t("Connect your site to the Concrete CMS marketplace to browse add-ons.")?></p>
        <a target="_blank" href="<?=URL::to('/dashboard/system/basics/marketplace')?>" class="btn btn-info"><?=t('Connect Site')?></a>

    <?php } ?>


	</div>
</div>

