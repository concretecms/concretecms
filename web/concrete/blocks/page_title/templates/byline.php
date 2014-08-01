<?php  defined('C5_EXECUTE') or die("Access Denied.");

$page = Page::getCurrentPage();
$date = $page->getCollectionDatePublic(DATE_APP_GENERIC_MDY_FULL);
$user = UserInfo::getByID($page->getCollectionUserID());
?>
<div class="ccm-block-page-title-byline">
    <h1 class="page-title"><?=$title?></h1>

    <span class="page-date">
    <? print $date; ?>
    </span>

    <? if (is_object($user)): ?>
    <span class="page-author">
    <? print $user->getUserDisplayName(); ?>
    </span>
    <? endif; ?>

</div>