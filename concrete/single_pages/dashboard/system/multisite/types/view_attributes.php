<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\SinglePage\Dashboard\System\Multisite\Types $controller
 * @var Concrete\Core\Entity\Site\Skeleton|null $skeleton
 * @var Concrete\Core\Filesystem\Element $attributesView
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Entity\Site\Type $type
 * @var Concrete\Core\Filesystem\Element $typeMenu
 */

$typeMenu->render();

if ($skeleton !== null) {
    ?>
    <div class="alert alert-info">
        <?= t('Attributes set here will automatically be applied to new pages of that type.') ?>
    </div>

    <?php
        $attributesView->render();
} else {
    ?>
    <div class="alert alert-warning">
        <?= t('Unable to retrieve skeleton object. You cannot set attributes on the default site type.') ?>
    </div>
    <?php
}
