<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

    <div class="ccm-dashboard-header-buttons">
        <a href="<?php echo View::url('/dashboard/pages/containers/add'); ?>"
           class="btn btn-primary"><?php echo t('Add Container'); ?></a>
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
    } ?>
