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
$u = new User();
$ap = new Permissions($a);
$cp = new Permissions($c);
$class = 'ccm-area-footer';

?>
    </div>
    <div class="<?=$class?> ccm-ui">
        <div class="ccm-area-footer-handle" data-area-menu-handle="<?=$a->getAreaID()?>" id="area-menu-footer-<?=$a->getAreaID()?>"><span><i class="fa fa-share-alt"></i> <?=$a->getAreaDisplayName()?></span></div>
        <div class="popover fade" data-area-menu="area-menu-a<?=$a->getAreaID()?>">
            <div class="arrow"></div>
            <div class="popover-inner">
                <ul class="dropdown-menu">
                    <?php
                    $showAreaDesign = ($ap->canEditAreaDesign() && Config::get('concrete.design.enable_custom') == true);
                    $showAreaLayouts = ($ap->canAddLayoutToArea() && Config::get('concrete.design.enable_layouts') == true && (!$a->isGlobalArea()));
                    $canEditAreaPermissions = ($ap->canEditAreaPermissions() && Config::get('concrete.permissions.model') != 'simple' && (!$a->isGlobalArea()));
                    $showAddBlock = (bool) $ap->canAddBlocks();
                    if ($showAddBlock) {
                       ?><li><a href='#' data-menu-action="area-add-block"><?= t('Add Block') ?></a></li><?php
                    }
                    if ($showAreaDesign || $showAreaLayouts) {
                        if ($showAreaDesign) {
                            ?><li><a data-menu-action="edit-area-design" href="#"><?=t("Edit Area Design")?></a></li><?php
                        }
                        if ($showAreaLayouts) {
                            $areabt = BlockType::getByHandle(BLOCK_HANDLE_LAYOUT_PROXY);
                            $areaLayoutBT = BlockType::getByHandle('core_area_layout');
                            ?><li><a dialog-title="<?=t('Add Layout')?>" data-block-type-handle="<?= $areabt->getBlockTypeHandle() ?>" data-area-grid-maximum-columns="<?=$a->getAreaGridMaximumColumns()?>" data-menu-action="add-inline" href="#" data-block-type-id="<?=$areabt->getBlockTypeID()?>"><?=t("Add Layout")?></a></li><?php
                        }
                        if ($canEditAreaPermissions) {
                            ?><li class="divider"></li><?php
                        }
                    }
                    if ($canEditAreaPermissions) {
                        ?><li><a dialog-title="<?=t('Area Permissions')?>" class="dialog-launch" dialog-modal="false" dialog-width="425" dialog-height="430" id="menuAreaStyle<?=$a->getAreaID()?>" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_area_popup?cID=<?=$c->getCollectionID()?>&arHandle=<?=urlencode($a->getAreaHandle())?>&atask=groups"><?=t("Permissions")?></a></li><?php
                    }
                    if ($a instanceof SubArea) {
                        $pk = PermissionKey::getByHandle('manage_layout_presets');
                        $ax = $a->getSubAreaParentPermissionsObject();
                        $axp = new Permissions($ax);
                        if ($axp->canAddLayout()) {
                            $bx = $a->getSubAreaBlockObject();
                            if (is_object($bx) && !$bx->isError()) {
                                ?>
                                <li class="divider"></li>
                                <li><a href="javascript:void(0)" data-container-layout-block-id="<?=$bx->getBlockID()?>" data-menu-action="edit-container-layout" data-area-grid-maximum-columns="<?=$a->getAreaGridMaximumColumns()?>"><?=t("Edit Container Layout")?></a></li>
                                <?php if ($showAreaDesign) { ?>
                                    <li><a href="javascript:void(0)" data-container-layout-block-id="<?=$bx->getBlockID()?>" data-menu-action="edit-container-layout-style"><?=t("Edit Layout Design")?></a></li>
                                <?php } ?>
                                <?php
                                if ($pk->validate()) {
                                    $btc = $bx->getController();
                                    $arLayout = $btc->getAreaLayoutObject();
                                    ?>
                                    <li><a class="dialog-launch" href="<?=URL::to('/ccm/system/dialogs/area/layout/presets', $arLayout->getAreaLayoutID())?>" dialog-title="<?=t('Save Layout as Preset')?>" dialog-width="360" dialog-height="300" dialog-modal="true"><?=t("Save Layout as Preset")?></a></li>
                                    <li><a class="dialog-launch" href="<?=URL::to('/ccm/system/dialogs/area/layout/presets/manage')?>" dialog-title="<?=t('Manage Presets')?>" dialog-width="360" dialog-height="240" dialog-modal="true"><?=t("Manage Presets")?></a></li>
                                    <?php
                                }
                            }
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>
