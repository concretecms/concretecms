<?php
use Concrete\Core\Attribute\Key\Key;
use Concrete\Core\Http\ResponseAssetGroup;

defined('C5_EXECUTE') or die('Access denied.');

/* @var \Concrete\Core\Error\ErrorList\ErrorList $error */
/* @var \League\OAuth2\Server\RequestTypes\AuthorizationRequest $auth */
/* @var \Concrete\Core\Http\Request $request */
/* @var \Concrete\Core\Entity\OAuth\Client $client */
/* @var \Concrete\Core\View\View $consentView */

$r = ResponseAssetGroup::get();
$r->requireAsset('javascript', 'underscore');
$r->requireAsset('javascript', 'core/events');

$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
$form = $app->make(\Concrete\Core\Form\Service\Form::class);
$token = $app->make(\Concrete\Core\Validation\CSRF\Token::class);

if (isset($authType) && $authType) {
    $active = $authType;
    $activeAuths = array($authType);
} else {
    $active = null;
    $activeAuths = AuthenticationType::getList(true, true);
}
if (!isset($authTypeElement)) {
    $authTypeElement = null;
}
if (!isset($authTypeParams)) {
    $authTypeParams = null;
}

// Always use last week's picture
$image = (date('Ymd') - 7) . '.jpg';
?>

<div class="login-page">
    <div class="container">
        <div class="login-page-header">
            <div class="row">
                <div class="col-12">
                    <h2 class="login-page-title"><?=t('Authorize')?></h2>
                </div>
            </div>
        </div>
        <div class="row gx-5 login-page-content">

            <?php
            if (!$authorize) {
                ?>
                <h3 class="text-center"><?= t('Sign in to %s', "<strong>{$client->getName()}</strong>") ?></h3>
                <?php
            }
            ?>

            <form method="post" action="<?= $request->getUri() ?>">
                <?php
                if (!$authorize) {
                    ?>
                    <div class="form-group">
                        <label class="control-label form-label"
                               for="uName"><?= $emailLogin ? t('Email Address') : t('Username') ?></label>
                        <input name="uName" id="uName" class="form-control" autofocus="autofocus"/>
                    </div>

                    <div class="form-group">
                        <label class="control-label form-label" for="uPassword"><?= t('Password') ?></label>
                        <input name="uPassword" id="uPassword" class="form-control" type="password" autocomplete="off"/>
                    </div>

                    <?php if (isset($locales) && is_array($locales) && count($locales) > 0) {
                        ?>
                        <div class="form-group">
                            <label for="USER_LOCALE" class="control-label form-label"><?= t('Language') ?></label>
                            <?= $form->select('USER_LOCALE', $locales) ?>
                        </div>
                        <?php
                    } ?>

                    <div class="form-group">
                        <button class="btn btn-primary"><?= t('Log in') ?></button>
                    </div>

                    <?php $token->output('oauth_login_' . $client->getClientKey()); ?>

                    <?php if (Config::get('concrete.user.registration.enabled')) {
                        ?>
                        <br/>
                        <hr/>
                        <div class="d-grid">
                            <a href="<?= URL::to('/register') ?>" class="btn btn-success" target="_blank">
                                <?= t('Not a member? Register') ?>
                            </a>
                        </div>
                        <?php
                    } ?>

                    <?php
                } elseif ($consentView) {
                    $consentView->addScopeItems($view->getScopeItems());
                    echo $consentView->render();
                }
                ?>

            </form>



        </div>
    </div>

</div>
