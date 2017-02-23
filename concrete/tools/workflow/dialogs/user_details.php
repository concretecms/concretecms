<?php
defined('C5_EXECUTE') or die("Access Denied.");

$ui = UserInfo::getByID($_REQUEST['uID']);
if (!is_object($ui)) {
    die(t("Invalid user provided."));
}
$u = User::getByUserID($_REQUEST['uID']);
$uName = $ui->getUserName();
$uEmail = $ui->getUserEmail();

$service = Core::make('Concrete\Core\Attribute\Category\CategoryService');
$categoryEntity = $service->getByHandle('user');
$category = $categoryEntity->getController();
$setManager = $category->getSetManager();
$attributeSets = $setManager->getAttributeSets();
$unassigned = $setManager->getUnassignedAttributeKeys();

$userGroup = $u->getUserGroups();
?>

<div class="ccm-ui">

    <h3><?=t('Basic Details')?></h3>
    <br>

    <div class="row">
        <div class="col-md-8">
            <p><strong><?=t(Username)?></strong></p>
        </div>

        <div class="col-md-4">
            <p><?=$uName?></p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <p><strong><?=t(Email)?></strong></p>
        </div>

        <div class="col-md-4">
            <p><a href="mailto:<?=$uEmail?>"><?=$uEmail?></a></p>
        </div>
    </div>

    <!-- user group starts -->
    <?php 	if(count($userGroups) > 0) { ?>
        <h3><?=t('Groups')?></h3>
        <br>
    <?php } ?>
    <!-- user group ends -->

    <!-- user attribut starts -->
    <?php if(count($attributeSets) > 0) : ?>
        <h3><?php echo t('User Attributes')?></h3>
        <br>
        <?php foreach ($attributeSets as $set) : ?>
            <h4><?php echo $set->getAttributeSetDisplayName()?></h4>
            <?php foreach ($set->getAttributeKeys() as $ak) : ?>
                <div class="row">
                    <div class="col-md-8">
                        <p><strong><?php echo t($ak->getAttributeKeyName()) ?></strong></p>
                    </div>

                    <div class="col-md-4">
                        <p><?php echo $ui->getAttribute($ak, 'displaySanitized', 'display') ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if (count($unassigned)) :
        if (count($attributeSets)) {?>
            <h4><?php echo t('Other')?></h4>
        <?php } ?>
        <?php foreach ($unassigned as $ak) : ?>
            <div class="row">
                <div class="col-md-8">
                    <p><strong><?php echo t($ak->getAttributeKeyName()) ?></strong></p>
                </div>

                <div class="col-md-4">
                    <p><?php echo $ui->getAttribute($ak, 'displaySanitized', 'display') ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- // user attribut end -->

    <div class="dialog-buttons">
        <?php $ih = Core::make('helper/concrete/ui'); ?>
        <?=$ih->button_js(t('Close'), 'jQuery.fn.dialog.closeTop()', 'left', 'btn')?>
        <?=$ih->button(t('Edit'), URL::to('/dashboard/users/search/view', $u->getUserID()), 'right', 'btn btn-primary')?>
    </div>
</div><!-- // div ccm-ui end -->