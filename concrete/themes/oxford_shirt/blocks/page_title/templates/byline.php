<?php  defined('C5_EXECUTE') or die("Access Denied.");

/** @var bool $useFilterTitle  */
/** @var bool $useFilterTopic */
/** @var bool $useFilterDate */
/** @var bool $useFilterTag */
/** @var bool $formatting */
/** @var Concrete\Block\PageTitle\Controller $controller */
/** @var string $title */
/** @var \Concrete\Core\Tree\Node\Type\Topic | null $currentTopic */

if ($useFilterTitle) {
    $currentTopic = $currentTopic ?? null;
    if (is_object($currentTopic) && $useFilterTopic) {
        $title = $controller->formatPageTitle($currentTopic->getTreeNodeDisplayName(), $topicTextFormat ?? false);
    }
    if (isset($tag) && $useFilterTag) {
        $title = $controller->formatPageTitle($tag, $tagTextFormat ?? false);
    }
    if (isset($year) && isset($month) && $useFilterDate) {
        $srv = app('helper/date');
        $date = strtotime("$year-$month-01");
        $title = $srv->date($filterDateFormat ?? 'F Y', $date);

        $title = $controller->formatPageTitle($title, $dateTextFormat ?? false);
    }
}

$dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */
$page = Page::getCurrentPage();
$date = $dh->formatDate($page->getCollectionDatePublic(), true);
$user = UserInfo::getByID($page->getCollectionUserID());
$avatarService = app(\Concrete\Core\User\Avatar\AvatarService::class);
$site = app('site')->getSite();
$config = $site->getConfigRepository();
$publicProfilesEnabled = $config->get('user.profiles_enabled');


?>
<div class="ccm-block-page-title-byline">
    <?php if ($title) {
        echo "<$formatting  class=\"ccm-block-page-title page-title mb-2 mb-sm-3 \">" . h($title) . "</$formatting>";
    } ?>

    <div class="byline-description muted mb-2 mb-sm-3">
        <?php echo $page->getCollectionDescription(); ?>
    </div>

    <div id="page-title-byline-author" class="author-date-wrapper subtitle-big">
        <div class="author-byline-date">
            <?php echo $page->getCollectionDatePublicObject()->format('F j, Y, '); ?>
        </div>

        <?php if ($user) { ?>

            <div class="author-byline-author">
                <?php if ($publicProfilesEnabled) { ?>
                    <a href="<?=$user->getUserPublicProfileURL()?>"><?php echo (str_repeat('&nbsp;', 1) . $user->getUserDisplayName()); ?></a>
                <?php } else { ?>
                    <?php echo (str_repeat('&nbsp;', 1) . $user->getUserDisplayName()); ?>
                <?php } ?>
            </div>

        <?php } ?>
        
    </div>
</div>

