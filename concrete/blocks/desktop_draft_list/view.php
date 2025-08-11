<?php defined('C5_EXECUTE') or die('Access Denied.');
/** @var Concrete\Core\Block\View\BlockView $view */
/** @var array<string, mixed> $drafts */
/** @var Concrete\Core\Search\Pagination\Pagination<\Concrete\Core\Page\Page>|null $pagination */
?>

<div class="card ccm-block-desktop-draft-list">
    <div class="card-body">
    <h5 class="card-title clearfix"><?= t('Page Drafts'); ?>
        <i class="ccm-block-desktop-draft-list-for-me-loader fas fa-sync fa-spin float-end invisible"></i>
    </h5>
    <?php if (!empty($drafts)) {
    ?>
        <div class="draft-list">
            <?php foreach ($drafts as $draft) {
        ?>
                <div class="draft-list-item">
                    <a href="<?= $draft['link']?>">
                        <?= t('%s created by %s on %s', $draft['name'], $draft['user'], $draft['dateAdded'])?>
                    </a>
                    <?php if (!empty($draft['deleteLink'])) {
            ?>
                        <a class="dialog-launch btn btn-danger btn-sm" href="<?= $draft['deleteLink']; ?>" dialog-modal="true" dialog-title="<?= t('Delete Draft'); ?>" dialog-width="400" dialog-height="250">
                            <?= t('Delete'); ?>
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
                    <?= $pagination->renderDefaultView()?>
                </div>
                <?php
            } ?>
        </div>
    <?php
} else {
                ?>
        <p class="card-text"><?= t('There are no drafts.'); ?></p>
    <?php
            } ?>
    </div>
</div>

<script type="text/javascript">
    $(function() {
        $('div.ccm-block-desktop-draft-list').concreteDraftList({
            reloadUrl:'<?= $view->action('reload_drafts'); ?>'
        });
    });
</script>
