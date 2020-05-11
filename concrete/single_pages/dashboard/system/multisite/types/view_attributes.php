<?php

use Concrete\Core\View\View;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\SinglePage\Dashboard\System\Multisite\Types $controller
 * @var Concrete\Core\Entity\Attribute\Category $category
 * @var Concrete\Core\Entity\Site\Skeleton|null $skeleton
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
    <div data-container="editable-fields">
        <?php View::element(
        'attribute/editable_set_list',
        [
            'category' => $category,
            'object' => $skeleton,
            'saveAction' => $controller->action('update_attribute', $type->getSiteTypeID()),
            'clearAction' => $controller->action('clear_attribute', $type->getSiteTypeID()),
            'permissionsCallback' => function ($ak) {
                return true;
            },
        ]
    ) ?>
    </div>
    <script>
    $(document).ready(function() {
        $('div[data-container=editable-fields]').concreteEditableFieldContainer({
            url: <?= json_encode((string) $controller->action('save', $type->getSiteTypeID())) ?>,
            data: {
                ccm_token: <?= json_encode($token->generate()) ?>
            }
        });
    });
    </script>
    <?php
} else {
    ?>
    <div class="alert alert-warning">
        <?= t('Unable to retrieve skeleton object. You cannot set attributes on the default site type.') ?>
    </div>
    <?php
}
