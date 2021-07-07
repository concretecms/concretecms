<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var $skins \Concrete\Core\StyleCustomizer\Skin\SkinInterface[]
 */
?>

<section>
    <header><h5><?= t('Skins') ?></h5></header>
    <menu>
        <?php
        foreach ($skins as $skin) { ?>
            <li>
                <a href="#" data-launch-sub-panel-url="<?= URL::to('/ccm/system/panels/theme/customize/skin', $theme->getThemeID(), $skin->getIdentifier(), $previewPage->getCollectionID()) ?>"
                   <?php if ($previewPage) {?>
                        data-panel-detail-url="<?=URL::to('/ccm/system/panels/details/theme/preview', $theme->getThemeID(), $skin->getIdentifier(), $previewPage->getCollectionID())?>"
                   <?php } ?>
                   data-launch-panel-detail="customize-skin-<?=$skin->getIdentifier()?>"
                   data-panel-transition="fade">
                    <?=$skin->getName()?>
                </a>
            </li>
        <?php } ?>
    </menu>
</section>
