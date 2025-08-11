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
        <div class="ccm-area-footer-handle" data-area-menu-handle="<?=$a->getAreaID()?>" id="area-menu-footer-<?=$a->getAreaID()?>"><i class="far fa-square"></i> <span><?=$a->getAreaDisplayName()?></span></div>
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
                    } ?>
                </div>
            </div>
        </div>
    </div>
</div>
