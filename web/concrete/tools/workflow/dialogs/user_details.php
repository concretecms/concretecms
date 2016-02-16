<?php
defined('C5_EXECUTE') or die("Access Denied.");

$ui = UserInfo::getByID($_REQUEST['uID']);
if (!is_object($ui)) {
    die(t("Invalid user provided."));
}
$u = User::getByUserID($_REQUEST['uID']);
$uName = $ui->getUserName();
$uEmail = $ui->getUserEmail();

$attributeList = UserAttributeKey::getList();
$userGroup = $u->getUserGroups();
?>

<div class="ccm-ui">

    <h3><?=t('Basic Details')?></h3>
    <br>

    <div class="row">
        <div class="col-md-8">
            <p><strong><?=t(Username)?></strong></p>
        </div>

        <div class="col-md-2">
            <p><?=$uName?></p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <p><strong><?=t(Email)?></strong></p>
        </div>

        <div class="col-md-2">
            <p><a href="mailto:<?=$uEmail?>"><?=$uEmail?></a></p>
        </div>
    </div>

    <!-- user group starts -->
    <? 	if(count($userGroups) > 0) { ?>
        <h3><?=t('Groups')?></h3>
        <br>
    <? } ?>
    <!-- user group ends -->

    <!-- user attribut starts -->
    <? 	if(count($attributeList) > 0) { ?>
        <h3><?=t('User Attributes')?></h3>
        <br>
        <?php	foreach ($attributeList as $ak) { ?>
            <div class="row">
                <div class="col-md-8">
                    <p><strong><?php echo t($ak->getAttributeKeyName()) ?></strong></p>
                </div>

                <div class="col-md-2">
                    <p><?php echo $ui->getAttribute($ak, 'displaySanitized', 'display') ?></p>
                </div>
            </div>
        <? }
    }?>
    <!-- // user attribut end -->

    <div class="dialog-buttons">
        <? $ih = Core::make('helper/concrete/ui'); ?>
        <?=$ih->button_js(t('Close'), 'jQuery.fn.dialog.closeTop()', 'left', 'btn')?>
        <?=$ih->button(t('Edit'), URL::to('/dashboard/users/search/view', $u->getUserID()), 'right', 'btn btn-primary')?>
    </div>
</div><!-- // div ccm-ui end -->