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
        <li><a class="ccm-hover-icon" title="<?= t('Jump to Folder') ?>" data-launch-dialog="navigate-file-manager"
               href="#">
                <svg width="21" height="10">
                    <use xlink:href="#icon-navigate-to-folder"/>
                </svg>
            </a>
        </li>
        <li><a class="ccm-hover-icon" title="<?= t('New Folder') ?>" href="#" data-launch-dialog="add-file-manager-folder">
                <svg width="20" height="16">
                    <use xlink:href="#icon-create-folder"/>
                </svg>
            </a></li>
        <li><a class="ccm-hover-icon" title="<?= t('Upload Files') ?>" href="#" id="ccm-file-manager-upload"
               data-dialog="add-files">
                <svg width="14" height="17">
                    <use xlink:href="#icon-add-files"/>
                </svg>
            </a></li>
    </ul>
</div>


<div style="display: none">
    <div class="dialog-buttons"></div>
    <div data-dialog="add-file-manager-folder" class="ccm-ui">
        <form data-dialog-form="add-folder" method="post" action="<?= $addFolderAction ?>">
            <?= $token->output('add_folder') ?>
            <?= $form->hidden('currentFolder', $currentFolder); ?>
            <div class="form-group">
                <?= $form->label('folderName', t('Folder Name')) ?>
                <?= $form->text('folderName', '', array('autofocus' => true)) ?>
            </div>
        </form>
        <div class="dialog-buttons">
            <button class="btn btn-default pull-left" data-dialog-action="cancel"><?= t('Cancel') ?></button>
            <button class="btn btn-primary pull-right" data-dialog-action="submit"><?= t('Add Folder') ?></button>
        </div>
    </div>

</div>

<script type="text/javascript">
    $(function() {
        $('a[data-launch-dialog=add-file-manager-folder]').on('click', function (e) {
            e.preventDefault()
            $.fn.dialog.open({
                width: 550,
                height: 'auto',
                modal: true,
                title: '<?=t('Add Folder')?>',
                href: '<?=URL::to('/ccm/system/dialogs/tree/node/add/file_folder')?>'
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
