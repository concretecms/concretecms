<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\SinglePage\Dashboard\Pages\Containers $controller
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Entity\Page\Container $containers
 */

?>

<div class="ccm-dashboard-header-buttons">
    <a href="<?= $view->action('add'); ?>" class="btn btn-primary"><?= t('Add Container'); ?></a>
</div>

<?php
if (count($containers) == 0) {
    ?>
    <br/><strong><?= t('No containers found.'); ?></strong><br/><br>
    <?php
} else {
    ?>
    <ul class="item-select-list">
        <?php foreach ($containers as $container) {
            ?>
            <li>
                <a href="<?= $view->action('edit', $container->getContainerID()); ?>">
                    <span><?= $container->getContainerIconImage(); ?></span>
                    <?=$container->getContainerName(); ?>
                </a>
            </li>
        <?php
        } ?>
    </ul>
<?php
}
?>
