<?php

use Concrete\Core\View\View;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\Dialog\Permissions\Collection $controller
 * @var Concrete\Core\Page\Page $page
 * @var Concrete\Core\User\User $user
 * @var bool $close
 */

if (!$close) {
    if (!$page->isEditMode()) {
        // first, we attempt to check the user in as editing the collection
        if ($user->isRegistered()) {
            $user->loadCollectionEdit($page);
        }
    }
    if ($page->isEditMode()) {
        View::element('permission/details/collection', [
            'c' => $page,
        ]);
    } else {
        ?>
        <div class="alert alert-danger"><?= t('Someone has already checked out this page for editing.') ?></div>
        <?php
    }
}
