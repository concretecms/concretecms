<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url as UrlFacade;
use Concrete\Core\Url\Url;
use Concrete\Core\User\UserInfo;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;

/** @var View $view */
/** @var UserInfo $profile */

$app = Application::getFacadeApplication();
/** @var Token $token */
$token = $app->make(Token::class);

?>

<div data-view="account">

    <p class="lead">
        <?php echo t('Change the picture attached to my posts.') ?>
    </p>

    <?php if ($profile->hasAvatar()) { ?>
        <form method="post" action="<?php echo $view->action('delete') ?>">
            <?php echo $token->output('delete_avatar') ?>

            <div class="mb-3">
                <div class="d-inline-block" style="max-width: <?=$width?>px; max-height: <?=$height?>px">
                    <?=$profile->getUserAvatar()->output()?>
                </div>
            </div>


            <button class="btn btn-danger btn-sm">
                <?php echo t('Delete Avatar') ?>
            </button>
        </form>
    <?php } else { ?>

        <?php
        $tag = $avatarCropperInstance->getTag();
        echo $tag;
        ?>

    <?php } ?>

    <br/>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?php echo (string)UrlFacade::to('/account') ?>" class="btn btn-secondary">
                <?php echo t('Back to Account') ?>
            </a>
        </div>
    </div>
</div>
