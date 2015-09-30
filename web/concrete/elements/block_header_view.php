<?
defined('C5_EXECUTE') or die("Access Denied.");

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

if ($showMenu) { ?>
    <div data-container="block">
<?php } ?>

<?php if (is_object($css) && $b->getBlockTypeHandle() == BLOCK_HANDLE_LAYOUT_PROXY) { ?>
    <?php // in this instance, the css container comes OUTSIDE any theme container ?>
    <div class="<?=$css->getContainerClass() ?>" >
<?php } ?>

<?
if (
    $pt->supportsGridFramework()
    && $a->isGridContainerEnabled()
    && !$b->ignorePageThemeGridFrameworkContainer()
) {
    $gf = $pt->getThemeGridFrameworkObject();
    print $gf->getPageThemeGridFrameworkContainerStartHTML();
    print $gf->getPageThemeGridFrameworkRowStartHTML();
    printf('<div class="%s">', $gf->getPageThemeGridFrameworkColumnClassesForSpan(
        min($a->getAreaGridMaximumColumns(), $gf->getPageThemeGridFrameworkNumColumns())
    ));
}

if ($showMenu) {
    $arHandle = $a->getAreaHandle();

    $btw = BlockType::getByID($b->getBlockTypeID());
    if ($btw->getBlockTypeHandle() == BLOCK_HANDLE_LAYOUT_PROXY) {
        $class = 'ccm-block-edit-layout ccm-block-edit';
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
    $btOriginal = $btw;
    $bID = $b->getBlockID();
    $aID = $a->getAreaID();
    $heightPlus = 20;
    $btHandle = $btw->getBlockTypeHandle();
    if ($btw->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) {
        $_bi = $b->getInstance();
        $_bo = Block::getByID($_bi->getOriginalBlockID());
        $btOriginal = BlockType::getByHandle($_bo->getBlockTypeHandle());
        $btHandle = $btOriginal->getBlockTypeHandle();
        $heightPlus = 80;
        if ($btOriginal->supportsInlineEdit()) {
            $editInline = true;
        }
    }
    $canDesign = ($p->canEditBlockDesign() && Config::get('concrete.design.enable_custom') == true);
    $canModifyGroups = ($p->canEditBlockPermissions() && Config::get('concrete.permissions.model') != 'simple' && (!$a->isGlobalArea()));
    $canEditName = $p->canEditBlockName();
    $canEditCacheSettings = $p->canEditBlockCacheSettings();
    $canEditCustomTemplate = $p->canEditBlockCustomTemplate();
    $canScheduleGuestAccess = (Config::get('concrete.permissions.model') != 'simple' && $p->canGuestsViewThisBlock() && $p->canScheduleGuestAccess() && (!$a->isGlobalArea()));
    $canAliasBlockOut = ($c->isMasterCollection() && !$a->isGlobalArea());
    if ($canAliasBlockOut) {
        $ct = PageType::getByID($c->getPageTypeID());
    }

    $isAlias = $b->isAlias();
    $u = new User();
    $numChildren = (!$isAlias) ? $b->getNumChildren() : 0;
    if ($isAlias) {
        $deleteMessage = t('Do you want to delete this block?');
    } else if ($numChildren) {
        $deleteMessage = t('Do you want to delete this block? This item is an original. If you delete it, you will delete all blocks aliased to it');
    } else {
        $deleteMessage = t('Do you want to delete this block?');
    }

    ?>

    <div
        data-cID="<?=$c->getCollectionID()?>"
        data-area-id="<?=$a->getAreaID()?>"
        data-block-id="<?=$b->getBlockID()?>"
        data-block-type-wraps="<?= intval(!$b->ignorePageThemeGridFrameworkContainer(), 10) ?>"
        class="<?=$class?>"
        data-block-type-handle="<?=$btHandle?>"
        data-launch-block-menu="block-menu-b<?=$b->getBlockID()?>-<?=$a->getAreaID()?>"
        data-dragging-avatar="<?=h('<p><img src="' . Loader::helper('concrete/urls')->getBlockTypeIconURL($btw) . '" /><span>' . t($btw->getBlockTypeName()) . '</span></p>')?>"
        <?php if ($btw->getBlockTypeHandle() == BLOCK_HANDLE_LAYOUT_PROXY) { ?> data-block-menu-handle="none"<?php } ?>
        >

    <?php if (is_object($css) && $b->getBlockTypeHandle() != BLOCK_HANDLE_LAYOUT_PROXY) { ?>
    <div class="<?=$css->getContainerClass() ?>" >
    <?php } ?>

        <ul class="ccm-edit-mode-inline-commands ccm-ui">
            <?php if ($p->canEditBlock() && $btw->getBlockTypeHandle() != BLOCK_HANDLE_LAYOUT_PROXY) {  ?>
                <li><a data-inline-command="move-block" href="#"><i class="fa fa-arrows"></i></a></li>
            <?php } ?>
        </ul>

        <div class="ccm-ui">

            <div class="popover fade ccm-edit-mode-block-menu" data-block-menu="block-menu-b<?=$b->getBlockID()?>-<?=$a->getAreaID()?>">
                <div class="arrow"></div>
                <div class="popover-inner">
                    <ul class="dropdown-menu">

                        <?php if ($btOriginal->getBlockTypeHandle() == BLOCK_HANDLE_STACK_PROXY) {
                            if (is_object($_bo)) {
                                $bi = $_bo->getInstance();
                            } else {
                                $bi = $b->getInstance();
                            }
                            $stack = Stack::getByID($bi->stID);
                            if (is_object($stack)) {
                                $sp = new Permissions($stack);
                                if ($sp->canWrite()) { ?>

                                    <li><a href="<?=View::url('/dashboard/blocks/stacks', 'view_details', $stack->getCollectionID())?>"><?=t("Manage Stack Contents")?></a></li>

                                <?php }
                            }
                        } else if ($p->canEditBlock() && $b->isEditable()) { ?>

                            <?php if ($editInline) { ?>

                                <?php if ($b->getBlockTypeHandle() == BLOCK_HANDLE_LAYOUT_PROXY) { ?>
                                    <li><a href="javascript:void(0)" data-menu-action="edit_inline" data-area-enable-grid-container="<?=$a->isGridContainerEnabled()?>" data-area-grid-maximum-columns="<?=$a->getAreaGridMaximumColumns()?>"><?=t("Edit Layout")?></a></li>
                                <?php } else { ?>
                                    <li><a href="javascript:void(0)" data-menu-action="edit_inline" data-area-enable-grid-container="<?=$a->isGridContainerEnabled()?>" data-area-grid-maximum-columns="<?=$a->getAreaGridMaximumColumns()?>"><?=t("Edit Block")?></a></li>
                                <?php } ?>
                            <?php } else { ?>
                                <li><a data-menu-action="block_dialog" data-menu-href="<?=URL::to('/ccm/system/dialogs/block/edit')?>" dialog-title="<?=t('Edit %s', t($btOriginal->getBlockTypeName()))?>" dialog-modal="false" dialog-width="<?=$btOriginal->getBlockTypeInterfaceWidth()?>" dialog-height="<?=$btOriginal->getBlockTypeInterfaceHeight() + $heightPlus?>" ><?=t("Edit Block")?></a></li>
                            <?php } ?>

                        <?php } ?>

                        <?php if ($b->getBlockTypeHandle() != BLOCK_HANDLE_LAYOUT_PROXY && $b->getBlockTypeHandle() != BLOCK_HANDLE_PAGE_TYPE_OUTPUT_PROXY) { ?>
                            <li><a href="javascript:void(0)" data-menu-action="block_scrapbook"><?=t("Copy to Clipboard")?></a></li>
                        <?php } ?>


                        <?php if ($p->canDeleteBlock()) {  ?>
                            <li><a href="javascript:void(0)" data-menu-action="delete_block" data-menu-delete-message="<?=$deleteMessage?>"><?=t("Delete")?></a></li>
                        <?php } ?>

                        <?php if ($b->getBlockTypeHandle() != BLOCK_HANDLE_LAYOUT_PROXY) { ?>

                            <?php if ($canDesign || $canEditCustomTemplate || $canEditBlockName || $canEditCacheSettings) { ?>
                                <li class="divider"></li>

                                <?php if ($canDesign || $canEditCustomTemplate) { ?>
                                    <li><a href="#" data-menu-action="block_design"><?=t("Design &amp; Custom Template")?></a></li>
                                <?php } ?>
                                <?php if ($b->getBlockTypeHandle() != BLOCK_HANDLE_PAGE_TYPE_OUTPUT_PROXY && ($canEditBlockName || $canEditCacheSettings)) { ?>
                                    <li><a dialog-title="<?=t('Advanced Block Settings')?>" dialog-modal="false" dialog-width="500" dialog-height="320" data-menu-action="block_dialog" data-menu-href="<?=URL::to('/ccm/system/dialogs/block/cache')?>" ><?=t("Advanced")?></a></li>
                                <?php } ?>
                            <?php } ?>

                            <?php if ($b->getBlockTypeHandle() != BLOCK_HANDLE_PAGE_TYPE_OUTPUT_PROXY && ($canModifyGroups || $canScheduleGuestAccess || $canAliasBlockOut)) { ?>
                                <li class="divider"></li>
                                <?php if ($canModifyGroups) { ?>
                                    <li><a dialog-title="<?=t('Block Permissions')?>" dialog-modal="false" dialog-width="350" dialog-height="450" data-menu-action="block_dialog" data-menu-href="<?=URL::to('/ccm/system/dialogs/block/permissions/list')?>" ><?=t("Permissions")?></a></li>
                                <?php } ?>
                                <?php if ($canScheduleGuestAccess) { ?>
                                    <li><a dialog-title="<?=t('Schedule Guest Access')?>" dialog-modal="false" dialog-width="500" dialog-height="320" data-menu-action="block_dialog" data-menu-href="<?=URL::to('/ccm/system/dialogs/block/permissions/guest_access')?>" ><?=t("Schedule Guest Access")?></a></li>
                                <?php } ?>
                                <?php if ($canAliasBlockOut) { ?>
                                    <li><a dialog-title="<?=t('Setup on Child Pages')?>" dialog-modal="false" dialog-width="550" dialog-height="450" data-menu-action="block_dialog" data-menu-href="<?=URL::to('/ccm/system/dialogs/block/aliasing')?>" ><?=t("Setup on Child Pages")?></a></li>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>

                    </ul>
                </div>
            </div>

        </div>

<?php } else { ?>
    <?php if (is_object($css) && $b->getBlockTypeHandle() != BLOCK_HANDLE_LAYOUT_PROXY) { ?>
    <div class="<?=$css->getContainerClass() ?>" >
    <?php } ?>
<?php } ?>
