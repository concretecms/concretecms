<?php use Concrete\Core\User\UserInfoRepository;

defined('C5_EXECUTE') or die("Access Denied.");
$dh = app('helper/date');
/* @var $dh \Concrete\Core\Localization\Service\Date */
$page = \Concrete\Core\Page\Page::getCurrentPage();
$date = $dh->formatDate($page->getCollectionDatePublic(), true);
/** @var \Concrete\Core\User\UserInfo $user */
$user = app(UserInfoRepository::class)->getByID($page->getCollectionUserID());
$title = $title ?? null;
?>
<div class="ccm-block-page-title-byline">
    <h1 class="page-title"><?=h($title)?></h1>

    <span class="page-date">
    <?php echo $date; ?>
    </span>

    <?php if (is_object($user)): ?>
    <span class="page-author">
    <?php echo $user->getUserDisplayName(); ?>
    </span>
    <?php endif; ?>
</div>
