<?php
defined('C5_EXECUTE') or die("Access Denied.");
$app = \Core::make('app');

use Concrete\Core\Permission\Key\Key;

if ($a->isGlobalArea()) {
    $c = Page::getCurrentPage();
    $cID = $c->getCollectionID();
} else {
    $cID = $b->getBlockCollectionID();
    $c = $b->getBlockCollectionObject();
}

$p = new Permissions($b);
$showMenu = false;
if ($a->showControls() && $p->canViewEditInterface() && $view->showControls()) {
    $showMenu = true;
}

$css = $b->getCustomStyle();
$pt = $c->getCollectionThemeObject();

if ($showMenu) {
    ?>
    <div data-container="block">
<?php
} ?>

<?php if (is_object($css) && $b->getBlockTypeHandle() == BLOCK_HANDLE_LAYOUT_PROXY) {
    ?>
    <?php // in this instance, the css container comes OUTSIDE any theme container ?>
    <div class="<?php echo $css->getContainerClass(); ?>"
    <?php if ($css->getCustomStyleID()) { ?>
    id="<?php echo $css->getCustomStyleID(); ?>"
    <?php } ?>
    <?php if ($css->getCustomStyleElementAttribute()) { ?>
    <?php echo $css->getCustomStyleElementAttribute(); ?>
    <?php } ?>
    >
<?php
} ?>

<?php
if (
    $pt->supportsGridFramework()
    && $a->isGridContainerEnabled()
    && !$b->ignorePageThemeGridFrameworkContainer()
) {
    $gf = $pt->getThemeGridFrameworkObject();
    echo $gf->getPageThemeGridFrameworkContainerStartHTML();
    echo $gf->getPageThemeGridFrameworkRowStartHTML();
    printf('<div class="%s">', $gf->getPageThemeGridFrameworkColumnClassesForSpan(
        min($a->getAreaGridMaximumColumns(), $gf->getPageThemeGridFrameworkNumColumns())
    ));
}

if ($showMenu) {
    $arHandle = $a->getAreaHandle();

    $btw = BlockType::getByID($b->getBlockTypeID());
    if ($btw->getBlockTypeHandle() == BLOCK_HANDLE_LAYOUT_PROXY) {
        $class = 'ccm-block-edit-layout ccm-block-edit';
    } else if ($btw->getBlockTypeHandle() == BLOCK_HANDLE_CONTAINER_PROXY) {
        $class = 'ccm-block-edit-container ccm-block-edit';
    } else {
        $class = 'ccm-block-edit';
    }

    $class .= ($b->isAliasOfMasterCollection() || $b->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) ? " ccm-block-alias" : "";

    if ($b->getBlockTypeHandle() == BLOCK_HANDLE_STACK_PROXY) {
        $class .= ' ccm-block-stack ';
    }
    $editInline = false;
    if ($btw->supportsInlineEdit()) {
        $editInline = true;
    }
    $aID = $a->getAreaID();
    $btHandle = $btw->getBlockTypeHandle();
    if ($btw->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) {
        $_bi = $b->getInstance();
        $_bo = Block::getByID($_bi->getOriginalBlockID());
        $btOriginal = \Concrete\Core\Block\BlockType\BlockType::getByHandle($_bo->getBlockTypeHandle());
        $btHandle = $btOriginal->getBlockTypeHandle();
    }

    ?>
        <?php if ($btw->getBlockTypeHandle() == BLOCK_HANDLE_CONTAINER_PROXY) { ?>
        <div class="ccm-edit-mode-title-notch-wrapper ccm-ui">
            <ul class="ccm-edit-mode-title-notch ccm-edit-mode-title-notch-container">
                <li><?php
                    $containerBlockController = $b->getController();
                    if ($containerBlockController instanceof Concrete\Block\CoreContainer\Controller) {
                        $containerBlockContainerInstance = $containerBlockController->getContainerInstanceObject();
                        if ($containerBlockContainerInstance) {
                            $containerBlockContainer = $containerBlockContainerInstance->getContainer();
                            if ($containerBlockContainer) {
                                print $containerBlockContainer->getContainerIconImage();
                                print '<span>' . $containerBlockContainer->getContainerDisplayName() . '</span>';
                            }
                        }
                    } ?>
                </li>
                <?php if ($p->canEditBlock()) { ?>
                    <li><a class="ccm-edit-mode-inline-command-move" data-inline-command="move-block" href="#"><i class="fas fa-arrows-alt"></i></a></li>
                <?php } ?>
            </ul>

            <div class="popover fade" data-container-menu="<?=$b->getBlockID()?>">
                <div class="popover-arrow"></div>
                <div class="popover-inner">
                    <div class="dropdown-menu">
                        <?php
                        $showContainerDesign = ($p->canEditBlockDesign() && Config::get('concrete.design.enable_custom') == true);
                        if ($showContainerDesign) { ?>
                            <a data-container-block-id="<?= $b->getBlockID() ?>" class="dropdown-item" data-inline-command="edit-container-design" href="#"><?=t('Edit Container Design')?></a>
                        <?php } ?>
                        <?php if ($p->canDeleteBlock()) { ?>
                            <a class="dropdown-item" data-inline-command="delete-block" href="#"><?=t('Delete Container')?></a>
                        <?php } ?>
                    </div>
                </div>
            </div>

        </div>


        <?php } else if ($btw->getBlockTypeHandle() == BLOCK_HANDLE_LAYOUT_PROXY) { ?>

            <div class="ccm-edit-mode-title-notch-wrapper ccm-ui">
                <ul class="ccm-edit-mode-title-notch ccm-edit-mode-title-notch-layout">
                    <li><?php
                        $layoutBlockController = $b->getController();
                        if ($layoutBlockController instanceof Concrete\Block\CoreAreaLayout\Controller) {
                            $layoutBlockLayout = $layoutBlockController->getAreaLayoutObject();
                            if ($layoutBlockLayout) {
                                print '<i class="fa fa-columns"></i>';
                                print '<span>' . $layoutBlockLayout->getDisplayName() . '</span>';
                            }
                        } ?>
                    </li>
                </ul>

                <div class="popover fade" data-layout-menu="<?=$b->getBlockID()?>">
                    <div class="popover-arrow"></div>
                    <div class="popover-inner">
                        <div class="dropdown-menu">
                        <?php
                            $pk = Key::getByHandle('manage_layout_presets');
                            $axp = new Permissions($a);
                            $showLayoutDesign = ($axp->canEditAreaDesign() && Config::get('concrete.design.enable_custom') == true);
                            if ($axp->canAddLayout()) { ?>
                                <a class="dropdown-item" href="javascript:void(0)"
                                   data-container-layout-block-id="<?= $b->getBlockID() ?>"
                                   data-menu-action="edit-container-layout"
                                   data-area-grid-maximum-columns="<?= $a->getAreaGridMaximumColumns() ?>"><?= t("Edit Container Layout") ?></a>
                                <?php if ($showLayoutDesign) { ?>
                                    <a class="dropdown-item" href="javascript:void(0)"
                                       data-container-layout-block-id="<?= $b->getBlockID() ?>"
                                       data-menu-action="edit-container-layout-style"><?= t("Edit Layout Design") ?></a>
                                <?php } ?>
                                <?php
                                if ($pk->validate()) {
                                    $btc = $b->getController();
                                    $arLayout = $btc->getAreaLayoutObject();

                                    if ($arLayout instanceof \Concrete\Core\Area\Layout\PresetLayout) { ?>
                                        <a href="#" class="dropdown-item disabled"><?=t('Save Layout as Preset')?></a>
                                    <?php } else { ?>
                                        <a class="dropdown-item dialog-launch"
                                           href="<?= URL::to('/ccm/system/dialogs/area/layout/presets', $arLayout->getAreaLayoutID()) ?>"
                                           dialog-title="<?= t('Save Layout as Preset') ?>" dialog-width="360"
                                           dialog-height="300"
                                           dialog-modal="true"><?= t("Save Layout as Preset") ?></a>
                                    <?php } ?>
                                    <a class="dropdown-item dialog-launch"
                                       href="<?= URL::to('/ccm/system/dialogs/area/layout/presets/manage') ?>"
                                       dialog-title="<?= t('Manage Presets') ?>" dialog-width="360"
                                       dialog-height="240" dialog-modal="true"><?= t("Manage Presets") ?></a>

                                    <?php
                                }
                            }

                            ?>
                        </div>
                    </div>
                </div>

            </div>

        <?php } ?>

        <div
        data-cID="<?=$c->getCollectionID()?>"
        data-area-id="<?=$a->getAreaID()?>"
        data-block-id="<?=$b->getBlockID()?>"
        data-block-type-wraps="<?= intval(!$b->ignorePageThemeGridFrameworkContainer(), 10) ?>"
        class="<?=$class?>"
        data-block-type-handle="<?=$btHandle?>"
        data-launch-block-menu="block-menu-b<?=$b->getBlockID()?>-<?=$a->getAreaID()?>"
        data-dragging-avatar="<?=h('<p><img src="' . Loader::helper('concrete/urls')->getBlockTypeIconURL($btw) . '" /><span>' . t($btw->getBlockTypeName()) . '</span></p>')?>"
        <?php if (in_array($btw->getBlockTypeHandle(), [BLOCK_HANDLE_LAYOUT_PROXY, BLOCK_HANDLE_CONTAINER_PROXY])) { ?>
    data-block-menu-handle="none"<?php
}
    ?>
        >

    <?php if (is_object($css) && $b->getBlockTypeHandle() != BLOCK_HANDLE_LAYOUT_PROXY) {
    ?>
    <div class="<?php echo $css->getContainerClass(); ?>"
    <?php if ($css->getCustomStyleID()) { ?>
    id="<?php echo $css->getCustomStyleID(); ?>"
    <?php } ?>
    <?php if ($css->getCustomStyleElementAttribute()) { ?>
    <?php echo $css->getCustomStyleElementAttribute(); ?>
    <?php } ?>
    >
    <?php
}
    ?>


        <?php if ($p->canEditBlock() && (!in_array($btw->getBlockTypeHandle(), [BLOCK_HANDLE_LAYOUT_PROXY, BLOCK_HANDLE_CONTAINER_PROXY]))) { ?>
        <ul class="ccm-edit-mode-inline-commands ccm-ui">
            <li><a class="ccm-edit-mode-inline-command-move" data-inline-command="move-block" href="#"><i class="fas fa-arrows-alt"></i></a></li>
        </ul>
        <?php } ?>

        <div class="ccm-ui">
            <?php
            $factory = $app->make('Concrete\Core\Block\Menu\Manager');
            $menu = $factory->getMenu([$b, $c, $a]);
            print $factory->deliverMenu($menu);
            ?>
        </div>

<?php
} else {
    ?>
    <?php if (is_object($css) && $b->getBlockTypeHandle() != BLOCK_HANDLE_LAYOUT_PROXY) {
    ?>
    <div class="<?php echo $css->getContainerClass(); ?>"
    <?php if ($css->getCustomStyleID()) { ?>
    id="<?php echo $css->getCustomStyleID(); ?>"
    <?php } ?>
    <?php if ($css->getCustomStyleElementAttribute()) { ?>
    <?php echo $css->getCustomStyleElementAttribute(); ?>
    <?php } ?>
    >
    <?php
}
    ?>
<?php
} ?>
