<?php
defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $urlHelper \Concrete\Core\Utility\Service\Url
 */
?>

    <div class="form-inline">

    <?php
    if (!empty($itemsPerPageOptions)) { ?>
        <div class="btn-group">
            <button type="button" class="btn btn-secondary p-2 dropdown-toggle" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false"><span
                        id="selected-option"><?= $itemsPerPage; ?></span>
            </button>
            <ul class="dropdown-menu">
                <li class="dropdown-header"><?= t('Items per page') ?></li>
                <?php foreach ($itemsPerPageOptions as $itemsPerPageOption) {
                    $url = $urlHelper->setVariable([
                        'itemsPerPage' => $itemsPerPageOption
                    ]);
                    ?>
                    <li data-items-per-page="<?= $itemsPerPageOption; ?>">
                        <a class="dropdown-item <?= ($itemsPerPageOption === $itemsPerPage) ? 'active' : ''; ?>"
                           href="<?=$url?>"><?= $itemsPerPageOption; ?></a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    <?php }
    ?>

        <ul class="ccm-dashboard-header-icons">
            <?php if ($exportURL) { ?>
            <li>
                <a href="<?= $exportURL ?>" class="link-primary">
                    <i class="fa fa-download"></i> <?= t('Export to CSV') ?>
                </a>
            </li>
            <?php } ?>
            <?php if ($createURL) { ?>
            <li><a href="<?= $createURL ?>" class="link-primary"><i class="fa fa-plus"></i> <?= t('New %s',
                        $entity->getEntityDisplayName()) ?></a></li>
            <?php } ?>
        </ul>
</div>



<?php
$addFolderAction = URL::to('/ccm/system/dialogs/tree/node/add/file_folder');
if (isset($currentFolder)) {
    $addFolderAction .= '?treeNodeID=' . $currentFolder->getTreeNodeID();
}
?>
<script type="text/javascript">
    $(function() {
        $('a[data-launch-dialog=add-file-manager-folder]').on('click', function (e) {
            e.preventDefault()
            $.fn.dialog.open({
                width: 550,
                height: 'auto',
                modal: true,
                title: '<?=t('Add Folder')?>',
                href: '<?=$addFolderAction?>'
            })
        })
        $('a[data-launch-dialog=navigate-file-manager]').on('click', function(e) {
            e.preventDefault()
            $.fn.dialog.open({
                width: '560',
                height: '500',
                modal: true,
                title: '<?=t('Jump to Folder')?>',
                href: '<?=URL::to('/ccm/system/dialogs/file/jump_to_folder')?>'
            })
        })
        $('a[data-dialog=add-files]').on('click', function(e) {
            e.preventDefault()
            $.fn.dialog.open({
                width: 620,
                height: 400,
                modal: true,
                title: '<?=t('Add Files')?>',
                href: '<?=URL::to('/ccm/system/dialogs/file/import')?>'
            })
        })
    });

</script>
