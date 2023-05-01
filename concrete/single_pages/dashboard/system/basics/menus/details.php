<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="ccm-dashboard-header-buttons">
    <div class="btn-group">
        <a href="<?=URL::to('/dashboard/system/basics/menus')?>" class="btn btn-secondary"><?=t('Back')?></a>
        <a href="<?=URL::to('/dashboard/system/basics/menus/details', 'edit', $menu->getId())?>" class="btn btn-primary"><?=t('Edit')?></a>
        <button type="button" data-bs-toggle="modal" data-bs-target="#delete-menu-modal" class="btn btn-danger"><?= t('Delete') ?></button>
    </div>
</div>

<h4><?=$menu->getName()?></h4>

<?php if (is_object($tree)) {
?>
<div data-tree="<?=$tree->getTreeID(); ?>">
</div>

<script type="text/javascript">
    $(function() {

        $('[data-tree]').concreteTree({
            'treeID': '<?=$tree->getTreeID(); ?>'
        });

    });
</script>

<?php } ?>


<div class="modal fade" tabindex="-1" role="dialog" id="delete-menu-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="<?= $view->action('delete', $menu->getId()) ?>">
                <?=$token->output('delete')?>
                <div class="modal-header">
                    <h5 class="modal-title"><?= t('Delete Menu') ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="<?= t('Close') ?>"></button>
                </div>
                <div class="modal-body">
                    <?= t('Are you sure you want to permanently remove this navigation menu?') ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= t('Close') ?></button>
                    <button type="submit" class="btn btn-danger"><?= t('Delete') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
