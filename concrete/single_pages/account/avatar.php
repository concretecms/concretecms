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
/** @var Repository $config */
$config = $app->make(Repository::class);

$saveUrl = Url::createFromUrl($view->action('save_avatar'))->setQuery(array(
    'ccm_token' => $token->generate('avatar/save_avatar'),
));

?>

<div data-view="account">

    <p class="lead">
        <?php echo t('Change the picture attached to my posts.') ?>
    </p>

    <avatar-cropper
            v-bind:height="<?php echo h($config->get('concrete.icons.user_avatar.height')) ?>"
            v-bind:width="<?php echo h($config->get('concrete.icons.user_avatar.width')) ?>"
            uploadurl="<?php echo h($saveUrl) ?>"
            uploadtoken="<?php echo h($token->generate()) ?>"
            cancel-confirm-text="<?= h(t('Are you sure you want to quit?')) ?>"
            canceled-text="<?= h(t('Upload canceled.')) ?>"
            src="<?php echo h($profile->getUserAvatar()->getPath()) ?>">
    </avatar-cropper>

    <?php if ($profile->hasAvatar()) { ?>
        <form method="post" action="<?php echo $view->action('delete') ?>">
            <?php echo $token->output('delete_avatar') ?>

            <button class="btn btn-danger btn-sm">
                <?php echo t('Delete Avatar') ?>
            </button>
        </form>
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
