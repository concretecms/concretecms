<?php defined('C5_EXECUTE') or die("Access Denied.");

$dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */
$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
/** @var \Concrete\Core\Package\PackageService $packageService */
$packageService = $app->make(\Concrete\Core\Package\PackageService::class);

?>


<div class="ccm-profile-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="ccm-profile-avatar">
                    <?php echo $profile->getUserAvatar()->output(); ?>
                </div>
                <div class="ccm-profile-username">
                    <h1><?=$profile->getUserName()?></h1>
                    <div class="ccm-profile-statistics">
                        <div class="ccm-profile-statistics-item">
                            <i class="fas fa-calendar-alt"></i> <?=t(/*i18n: %s is a date */'Joined on %s', $dh->formatDate($profile->getUserDateAdded(), true))?>
                        </div>
                    </div>

                </div>
                <div class="ccm-profile-buttons">
                    <?php if ($canEdit) {
                        ?>
                        <div class="btn-group">
                            <a href="<?=$view->url('/account/edit_profile')?>" class="btn btn-lg btn-outline-secondary"><i class="fas fa-cog"></i> <?=t('Edit')?></a>
                            <a href="<?=$view->url('/')?>" class="btn btn-lg btn-outline-secondary"><i class="fas fa-home"></i> <?=t('Home')?></a>
                        </div>
                        <?php
                    } else {
                        ?>
                        <?php if ($profile->getAttribute('profile_private_messages_enabled')) {
                            ?>
                            <a href="<?=$view->url('/account/messages', 'write', $profile->getUserID())?>" class="btn btn-lg btn-outline-secondary"><i class="fa-user fa"></i> <?=t('Send Message')?></a>
                            <?php
                        }
                        ?>
                        <?php
                    } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="ccm-profile-detail">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="card-header">
                        <?=t('Profile Information')?>
                    </div>
                    <div class="card-body">
                        <?php
                        $uaks = \Concrete\Core\Attribute\Key\UserKey::getPublicProfileList();
                        if (count($uaks) === 0) { ?>
                            <p class="card-text lead"><?=t('There is no public user information available.')?></p>
                        <?php } else {
                            foreach ($uaks as $ua) {
                                ?>
                                <div>
                                    <h4><?php echo $ua->getAttributeKeyDisplayName()?></h4>
                                    <?php
                                    $r = $profile->getAttribute($ua, 'displaySanitized', 'display');
                                    if ($r) {
                                        echo $r;
                                    } else {
                                        echo t('None');
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                        } ?>
                    </div>
                </div>

                <?php
                $a = new Area('Main');
                $a->setBlockWrapperStart('<div class="ccm-profile-body-item">');
                $a->setBlockWrapperEnd('</div>');
                $a->display($c);
                ?>

            </div>
        </div>
    </div>
</div>