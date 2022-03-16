<?php
defined('C5_EXECUTE') or die('Access Denied.');

use \Concrete\Core\Area\SubArea;

/* @var Area $a */

// simple file that controls the adding of blocks.

// $blockTypes is an array using the btID as the key and btHandle as the value.
// It is defined within Area->_getAreaAddBlocks(), which then calls a
// function in Content to include the file

// note, we're also passed an area & collection object from the original function

$arHandle = $a->getAreaHandle();
$c = $a->getAreaCollectionObject();
$cID = $c->getCollectionID();
$u = Core::make(Concrete\Core\User\User::class);
$ap = new Permissions($a);
$cp = new Permissions($c);
$class = 'ccm-area-footer';

?>
    </div>
    <div class="<?=$class?> ccm-ui">
        <div class="ccm-area-footer-handle" data-area-menu-handle="<?=$a->getAreaID()?>" id="area-menu-footer-<?=$a->getAreaID()?>"><span><i class="fas fa-share-alt"></i> <?=$a->getAreaDisplayName()?></span></div>
        <div class="popover fade" data-area-menu="area-menu-a<?=$a->getAreaID()?>">
            <div class="popover-arrow"></div>
            <div class="popover-inner">
                <div class="dropdown-menu">
                    <?php
                    $showAreaDesign = ($ap->canEditAreaDesign() && Config::get('concrete.design.enable_custom') == true);
                    $showAreaLayouts = ($ap->canAddLayoutToArea() && Config::get('concrete.design.enable_layouts') == true && (!$a->isGlobalArea()));
                    $canEditAreaPermissions = ($ap->canEditAreaPermissions() && Config::get('concrete.permissions.model') != 'simple' && (!$a->isGlobalArea()));
                    $showAddBlock = (bool) $ap->canAddBlocks();
                    if ($showAddBlock) {
                       ?><a href='#' class="dropdown-item" data-menu-action="area-add-block"><?= t('Add Block') ?></a><?php
                    }
                    if ($showAreaDesign || $showAreaLayouts) {
                        if ($showAreaDesign) {
                            ?><a class="dropdown-item"  data-menu-action="edit-area-design" href="#"><?=t("Edit Area Design")?></a><?php
                        }
                        if ($showAreaLayouts) {
                            $areabt = BlockType::getByHandle(BLOCK_HANDLE_LAYOUT_PROXY);
                            $areaLayoutBT = BlockType::getByHandle('core_area_layout');
                            ?><a class="dropdown-item" dialog-title="<?=t('Add Layout')?>" data-block-type-handle="<?= $areabt->getBlockTypeHandle() ?>" data-area-grid-maximum-columns="<?=$a->getAreaGridMaximumColumns()?>" data-menu-action="add-inline" href="#" data-block-type-id="<?=$areabt->getBlockTypeID()?>"><?=t("Add Layout")?></a><?php
                        }
                        if ($canEditAreaPermissions) {
                            ?><div class="dropdown-divider"></div><?php
                        }
                    }
                    if ($canEditAreaPermissions) {
                        ?><a dialog-title="<?=t('Area Permissions')?>" class="dropdown-item dialog-launch" dialog-modal="false" dialog-width="425" dialog-height="430" id="menuAreaStyle<?=$a->getAreaID()?>" href="<?= URL::to('/ccm/system/dialogs/area/edit/permissions') ?>?cID=<?=$c->getCollectionID()?>&arHandle=<?=urlencode($a->getAreaHandle())?>"><?=t("Permissions")?></a><?php
                    }
                    if ($a instanceof SubArea) {
                        $bx = $a->getSubAreaBlockObject();
                        if ($bx->getBlockTypeHandle() == BLOCK_HANDLE_LAYOUT_PROXY) {
                            $pk = PermissionKey::getByHandle('manage_layout_presets');
                            $ax = $a->getSubAreaParentPermissionsObject();
                            $axp = new Permissions($ax);
                            if ($axp->canAddLayout()) {
                                if (is_object($bx) && !$bx->isError()) {
                                    ?>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="javascript:void(0)"
                                           data-container-layout-block-id="<?= $bx->getBlockID() ?>"
                                           data-menu-action="edit-container-layout"
                                           data-area-grid-maximum-columns="<?= $a->getAreaGridMaximumColumns() ?>"><?= t("Edit Container Layout") ?></a>
                                    <?php if ($showAreaDesign) { ?>
                                        <a class="dropdown-item" href="javascript:void(0)"
                                               data-container-layout-block-id="<?= $bx->getBlockID() ?>"
                                               data-menu-action="edit-container-layout-style"><?= t("Edit Layout Design") ?></a>
                                    <?php } ?>
                                    <?php
                                    if ($pk->validate()) {
                                        $btc = $bx->getController();
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
                            }
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
