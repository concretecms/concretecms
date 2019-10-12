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

/* @var Key[] $required_attributes */
$attribute_mode = (isset($required_attributes) && count($required_attributes));
?>


<div class="login-page mt-5">
    <div class="row justify-content-center">
        <?php
        $disclaimer = new Area('Disclaimer');
        if ($disclaimer->getTotalBlocksInArea($c) || $c->isEditMode()) { ?>
            <div class="ccm-login-disclaimer">
                <?= $disclaimer->display($c); ?>
            </div>
        <?php } ?>
        <div class="col-sm-6">
            <h1><?= t('Sign In') ?></h1>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-sm-6">
            <div class="login-detail">
                <?php
                /** @var AuthenticationType[] $activeAuths */
                foreach ($activeAuths as $auth) {
                    ?>
                    <div data-handle="<?= $auth->getAuthenticationTypeHandle() ?>"
                         class="authentication-type authentication-type-<?= $auth->getAuthenticationTypeHandle() ?>">
                        <?php $auth->renderForm($authTypeElement ?: 'form', $authTypeParams ?: array()) ?>
                    </div>
                    <?php

                }
                ?>
            </div>
        </div>
    </div>
</div>
