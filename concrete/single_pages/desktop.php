<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<p><?=t('Currently logged in as <strong>%s</strong>. Last login on <strong>%s</strong>',
        $profile->getUserDisplayName(),
        app()->make('date')->formatDateTime($profile->getPreviousLogin(), 'long'),
    )?>. <a href="<?=URL::to('/')?>"><?=t('Return to Previous Page.')?></a></p>

<hr/>

<?php $a = new Area("Main"); $a->display($c); ?>

<?php
$profileURL = $profile->getUserPublicProfileURL();
if ($profileURL) {
    ?>
    <hr/>
    <div>
        <a href="<?=$profileURL?>"><?=t("View Public Profile")?></a>
        <p><?=t('View your public user profile and the information you are sharing.')?></p>
    </div>


    <?php
} ?>