<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="ccm-block-desktop-draft-list">
    <h3><?= t('Page Drafts'); ?>
        <i class="ccm-block-desktop-draft-list-for-me-loader fa fa-refresh fa-spin pull-right hidden"></i>
    </h3>
    <?php if (!empty($drafts)) {
    ?>
        <div class="draft-list">
            <?php foreach ($drafts as $draft) {
        ?>
                <div class="draft-list-item">
                    <a href="<?= $draft['link']; ?>">
                        <?= t('%s created by %s on %s', $draft['name'], $draft['user'], $draft['dateAdded']); ?>
                    </a>
                    <?php if (!empty($draft['deleteLink'])) {
            ?>
                        <a class="dialog-launch btn btn-danger btn-xs" href="<?= $draft['deleteLink']; ?>" dialog-modal="true" dialog-title="<?= t('Delete Draft'); ?>" dialog-width="400" dialog-height="250">
                            <?= t('Delete Draft'); ?>
                        </a>
                    <?php
        } ?>
                </div>
            <?php
    } ?>
            <?php
            if ($pagination && $pagination->haveToPaginate()) {
                $pagination->setBaseURL($view->action('reload_drafts')); ?>
                <div class="ccm-search-results-pagination">
                    <?= $pagination->renderDefaultView(); ?>
                </div>
                <?php
            } ?>
        </div>
    <?php
} else {
                ?>
        <p><?= t('There are no drafts.'); ?></p>
    <?php
            } ?>
</div>

<script type="text/javascript">
    $(function() {
        $('div.ccm-block-desktop-draft-list').concreteDraftList({
            reloadUrl:'<?= $view->action('reload_drafts'); ?>'
        });
    });
</script>