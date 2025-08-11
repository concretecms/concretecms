<?php
defined('C5_EXECUTE') or die("Access Denied.");

use \Concrete\Core\Page\Type\Composer\FormLayoutSet as PageTypeComposerFormLayoutSet;
use \Concrete\Core\Page\Type\Composer\FormLayoutSetControl as PageTypeComposerFormLayoutSetControl;

$fieldsets = PageTypeComposerFormLayoutSet::getList($pagetype);
$cmp = new Permissions($pagetype);
// $targetPage comes from renderComposerOutputForm($page, $targetPage); only
// set in dialog page.

$targetParentPageID = 0;
if (is_object($targetPage)) {
    $targetParentPageID = $targetPage->getCollectionID();
}

?>

<div class="ccm-ui">

    <div class="alert alert-info" style="display: none" id="ccm-page-type-composer-form-save-status"></div>

    <input type="hidden" name="ptID" value="<?= $pagetype->getPageTypeID() ?>"/>

    <?php foreach ($fieldsets as $cfl) {
        $collapseType = $cfl->getPageTypeComposerFormLayoutSetCollapseType();
        ?>
        <fieldset class="pt-3 pb-3">
            <?php if ($cfl->getPageTypeComposerFormLayoutSetDisplayName()) {
                ?>
                <legend class="mb-3">
                    <?php if ($collapseType != 'never') { ?>
                    <a href="#composerset<?= $cfl->getPageTypeComposerFormLayoutSetID(); ?>" class="d-block composersettoggle <?= ($collapseType == 'collapsed' ? 'collapsed' : '') ?>" data-bs-toggle="collapse" role="button" aria-expanded="<?= ($collapseType == 'collapsed' ? 'false' : 'true') ?>" aria-controls="composerset<?= $cfl->getPageTypeComposerFormLayoutSetID(); ?>">
                        <?php } ?>
                        <?= $cfl->getPageTypeComposerFormLayoutSetDisplayName() ?>
                        <?php if ($collapseType != 'never') { ?>
                        <span class="fas fa-angle-right float-end setcollapsed"></span><span class="fas fa-angle-down float-end setexpanded"></span></a>
                <?php } ?>
                </legend>
                <?php
            }
            ?>
            <?php if ($cfl->getPageTypeComposerFormLayoutSetDisplayDescription()) {
                ?>
                <div class="form-text mb-4"><?= $cfl->getPageTypeComposerFormLayoutSetDisplayDescription() ?></div>
                <?php
            }
            ?>
            <?php $controls = PageTypeComposerFormLayoutSetControl::getList($cfl);
            ?>
            <div id="composerset<?= $cfl->getPageTypeComposerFormLayoutSetID(); ?>" class="<?= ($collapseType == 'collapsed' ? 'collapse' : 'show') ?>">
                <?php
                foreach ($controls as $con) {
                    if (is_object($page)) { // we are loading content in
                        $con->setPageObject($page);
                    }
                    $con->setTargetParentPageID($targetParentPageID);
                    ?>
                    <?php $con->render();
                    ?>
                    <?php
                }
                ?>
            </div>
        </fieldset>

        <?php
    } ?>

</div>

<style>
    .ccm-ui .composersettoggle .setcollapsed, .ccm-ui .composersettoggle.collapsed .setexpanded {
        display: none;
    }

    .ccm-ui .composersettoggle.collapsed .setcollapsed, .ccm-ui .composersettoggle .setexpanded {
        display: inline-block;
    }
</style>
