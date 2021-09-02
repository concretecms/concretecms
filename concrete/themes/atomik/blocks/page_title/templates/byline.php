<?php  defined('C5_EXECUTE') or die("Access Denied.");
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
    <h1 class="page-title"><?=h($title)?></h1>

    <div class="ccm-block-page-title-byline-byline">

        <?php if ($user) { ?>

            <?php
            $avatar = $avatarService->getAvatar($user);
            if ($avatar) {
            ?>
                <div class="ccm-block-page-title-byline-byline-avatar"><?=$avatar->output()?></div>
            <?php } ?>

            <div class="ccm-block-page-title-byline-byline-author">
                <?php if ($publicProfilesEnabled) { ?>
                    <a href="<?=$user->getUserPublicProfileURL()?>"><?php echo $user->getUserDisplayName(); ?></a>
                <?php } else { ?>
                    <?php echo $user->getUserDisplayName(); ?>
                <?php } ?>
            </div>

        <?php } ?>

        <div class="ccm-block-page-title-byline-byline-date">
            <?php echo $page->getCollectionDatePublicObject()->format('F j, Y • g:iA'); ?>
        </div>

    </div>
</div>
