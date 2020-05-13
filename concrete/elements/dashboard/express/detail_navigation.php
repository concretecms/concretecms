<?php
defined('C5_EXECUTE') or die("Access Denied.");
$c = Page::getCurrentPage();
?>

<div class="col-4">
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?= ($c->getCollectionPath() === '/dashboard/system/express/entities' && $view->controller->getTask() === 'view_entity') ? ' active' : '' ?>"
               href="<?= URL::to('/dashboard/system/express/entities', 'view_entity', $entity->getId()) ?>">
                <?= t('Details') ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($c->getCollectionPath() === '/dashboard/system/express/entities' && ($view->controller->getTask() === 'edit' || $view->controller->getTask() === 'update')) ? ' active' : '' ?>"
               href="<?= URL::to('/dashboard/system/express/entities', 'edit', $entity->getId()) ?>">
                <?= t('Edit Entity') ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($c->getCollectionPath() === '/dashboard/system/express/entities/attributes') ? ' active' : '' ?>"
               href="<?= URL::to('/dashboard/system/express/entities/attributes', $entity->getId()) ?>">
                <?= t('Attributes') ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($c->getCollectionPath() === '/dashboard/system/express/entities/associations') ? ' active' : '' ?>"
               href="<?= URL::to('/dashboard/system/express/entities/associations', $entity->getId()) ?>">
                <?= t('Associations') ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($c->getCollectionPath() === '/dashboard/system/express/entities/forms') ? ' active' : '' ?>"
               href="<?= URL::to('/dashboard/system/express/entities/forms', $entity->getId()) ?>">
                <?= t('Forms') ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($c->getCollectionPath() === '/dashboard/system/express/entities/customize_search') ? ' active' : '' ?>"
               href="<?= URL::to('/dashboard/system/express/entities/customize_search', $entity->getId()) ?>">
                <?= t('Customize Search/Listing') ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($c->getCollectionPath() === '/dashboard/system/express/entities/order_entries') ? ' active' : '' ?>"
               href="<?= URL::to('/dashboard/system/express/entities/order_entries', $entity->getId()) ?>">
                <?= t('Re-Order Entries') ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?= URL::to('/dashboard/express/entries', $entity->getId()) ?>">
                <i class="fas fa-share float-right"></i>
                <?= tc(/*i18n: %s is an entity name*/ 'Express', 'View %s Entries', $entity->getEntityDisplayName()) ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?= URL::to('/dashboard/system/express/entities', 'clear_entries', $entity->getId()) ?>">
                <i class="fas fa-trash-alt float-right text-danger"></i>
                <span class="text-danger"><?= t('Clear Entries') ?></span>
            </a>
        </li>
    </ul>

</div>
