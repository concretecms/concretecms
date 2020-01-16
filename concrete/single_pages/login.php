<?php

use Concrete\Core\Attribute\Key\Key;
use Concrete\Core\Http\ResponseAssetGroup;

defined('C5_EXECUTE') or die('Access denied.');

$form = Loader::helper('form');

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
$image = date('Ymd') . '.jpg';

/* @var Key[] $required_attributes */

$attribute_mode = (isset($required_attributes) && count($required_attributes));

// See if we have a user object and if that user is registered
$loggedIn = !$attribute_mode && isset($user) && $user->isRegistered();

// Determine title
$title = t('Sign in.');

if ($attribute_mode) {
    $title = t('Required Attributes');
}

if ($loggedIn) {
    $title = 'Log Out.';
}
?>

<div class="login-page">
    <div class="container">
        <?php
        $disclaimer = new Area('Disclaimer');
        if ($disclaimer->getTotalBlocksInArea($c) || $c->isEditMode()) { ?>
            <div class="row">
                <div class="col-12">
                    <?= $disclaimer->display($c); ?>
                </div>
            </div>
        <?php } ?>

        <div class="row">
            <div class="col-12">
                <h1><?= $title ?></h1>
            </div>
        </div>

        <?php if ($attribute_mode) {

            $attribute_helper = new Concrete\Core\Form\Service\Widget\Attribute();
            ?>
            <form action="<?= View::action('fill_attributes') ?>" method="POST">
                <div data-handle="required_attributes"
                     class="authentication-type authentication-type-required-attributes">
                    <div class="ccm-required-attribute-form"
                         style="height:340px;overflow:auto;margin-bottom:20px;">
                        <?php
                        foreach ($required_attributes as $key) {
                            echo $attribute_helper->display($key, true);
                        }
                        ?>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary pull-right"><?= t('Submit') ?></button>
                    </div>

                </div>
            </form>


        <?php } else { ?>

            <div class="row">

                <?php
                if (count($activeAuths) > 1) {
                    ?>
                    <div class="col-3">
                        <ul class="auth-types">
                            <?php
                            /** @var AuthenticationType[] $activeAuths */
                            foreach ($activeAuths as $auth) {
                                ?>
                                <li data-handle="<?= $auth->getAuthenticationTypeHandle() ?>">
                                    <?= $auth->getAuthenticationTypeIconHTML() ?>
                                    <span><?= $auth->getAuthenticationTypeDisplayName() ?></span>
                                </li>
                                <?php

                            }
                            ?>
                        </ul>
                    </div>
                    <div class="col-9">
                        <?php if ($loggedIn) { ?>
                            <div class="text-center">
                                <h3><?= t('You are already logged in.') ?></h3>
                                <?= Core::make('helper/navigation')->getLogInOutLink() ?>
                            </div>
                        <?php } else {

                            foreach ($activeAuths as $auth) {
                                ?>
                                <div data-handle="<?= $auth->getAuthenticationTypeHandle() ?>"
                                     class="authentication-type authentication-type-<?= $auth->getAuthenticationTypeHandle() ?>">
                                    <?php $auth->renderForm($authTypeElement ?: 'form', $authTypeParams ?: array()) ?>
                                </div>
                                <?php
                            }
                        }

                        ?>


                    </div>

                    <?php

                } else { ?>

                    <?php if ($loggedIn) { ?>
                        <div class="text-center">
                            <h3><?= t('You are already logged in.') ?></h3>
                            <?= Core::make('helper/navigation')->getLogInOutLink() ?>
                        </div>
                    <?php } else {
                        $auth = $activeAuths[0]; ?>
                        <div data-handle="<?= $auth->getAuthenticationTypeHandle() ?>"
                             class="authentication-type authentication-type-<?= $auth->getAuthenticationTypeHandle() ?>">
                            <?php $auth->renderForm($authTypeElement ?: 'form', $authTypeParams ?: array()) ?>
                        </div>
                        <?php
                    }

                    ?>

                <?php }
                ?>


            </div>

        <?php } ?>

    </div>
</div>
