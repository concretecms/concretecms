<?php
defined('C5_EXECUTE') or die('Access Denied.');

$this->inc('elements/header.php'); ?>

<div id="ccm-marketplace-wrapper">

    <header class="ccm-marketplace">

        <?php if ($controller->getTask() == 'view_detail') { ?>
            <div class="ccm-marketplace-nav">
                <nav>
                    <li><a href="<?=$controller->action('view')?>"><i class="fas fa-chevron-left"></i> <?=t('Back')?></a></li>
                </nav>
            </div>
        <?php } else { ?>
            <form action="<?=$controller->action('view')?>" method="get">
                <input type="hidden" name="ccm_order_by" value="<?=$sort?>" />
                <div class="ccm-marketplace-nav">
                    <nav>
                        <?php
                        $extensionTypes = [
                            'themes' => 'Themes',
                            'addons' => 'Add-Ons'
                        ];
                        foreach ($extensionTypes as $extensionType => $label) { ?>
                            <li>
                                <a href="<?=URL::to("/dashboard/extend/$extensionType")?>"<?php if ($type == $extensionType) { ?> class="active"<?php } ?>>
                                    <?=t("$label")?>
                                </a>
                            </li>
                        <?php
                        } ?>                            
                    </nav>
                </div>
                <div class="ccm-marketplace-search">
                    <?=$form->select('marketplaceRemoteItemSetID', $sets, ($selectedSet) ?? '', array('style' => 'width: 150px'))?>
                    <div class="ccm-marketplace-search-input">
                        <i class="fas fa-search"></i>
                        <input type="search" name="keywords" value="<?=($keywords) ?? ''?>" />
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm"><?=t('Search')?></button>
                </div>
            </form>
        <?php } ?>

    </header>

    <?php if ($controller->getTask() != 'view_detail') { ?>
        <header class="ccm-marketplace-list">
            <div class="ccm-marketplace-sort">
                <nav>
                    <?php
                    $sortTypes = [
                        'popularity'  => 'Most Popular',
                        'recent'      => 'Recent',
                        'price'       => 'Price',
                        'rating'      => 'Rating',
                        'skill_level' => 'Skill Level'
                    ];
                    foreach ($sortTypes as $type => $label) { ?>
                        <li>
                            <a href="<?=$list->getSortByURL($type)?>"<?php if ($sort == $type) { ?> class="active"<?php } ?>>
                                <?=t("$label")?>
                            </a>
                        </li>
                    <?php } ?>
                </nav>
            </div>
        </header>

    <?php } ?>


    <?php echo $innerContent; ?>

</div>


<?php $this->inc('elements/footer.php');
