<?php
defined('C5_EXECUTE') or die("Access Denied.");
$folder = $file->getFileFolderObject();
?>

<div class="dropdown">
    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
        <?=t('Manage')?>
    </button>
    <ul class="dropdown-menu">
        <?php
        if ($filePermissions->canEditFileProperties()) {
        ?>
            <li><a class="dialog-launch dropdown-item launch-tooltip"
                   data-bs-placement="left"
                   title="<?=t('Access or approve old versions of this file.')?>"
                   dialog-title="<?= t('Versions') ?>"
                   dialog-width="80%" dialog-height="600"
                   href="<?= URL::to('/ccm/system/dialogs/file/versions')?>?fID=<?=$file->getFileID()?>"
                ><?=t('Versions')?></a></li>
        <?php
        }
        if ($filePermissions->canEditFileContents()) {
        ?>
            <li class="dropdown-divider"></li>
            <li><a class="dialog-launch dropdown-item launch-tooltip"
                   data-bs-placement="left"
                   title="<?=t('Upload a new file to be used everywhere this current file is referenced.')?>"
                   dialog-title="<?= t('Swap') ?>"
                   dialog-width="80%" dialog-height="600"
                   href="<?= URL::to('/ccm/system/dialogs/file/replace')?>?fID=<?=$file->getFileID()?>"
                ><?=t('Swap')?></a></li>

            <li><a class="dropdown-item launch-tooltip"
                   data-bs-placement="left"
                   title="<?=t('Automatically regenerate thumbnails and attributes for all sizes of this file.')?>"
                   href="#" data-action="rescan-file"
                ><?=t('Rescan')?></a></li>
        <?php }
        if ($filePermissions->canEditFilePermissions() || $filePermissions->canDeleteFile()) { ?>
            <li class="dropdown-divider"></li>
            <?php if ($filePermissions->canEditFilePermissions()) { ?>
                <li><a class="dialog-launch dropdown-item launch-tooltip"
                       data-bs-placement="left"
                       title="<?= t('Configure who can view or edit this file.') ?>"
                       dialog-title="<?= t('Permissions and Storage') ?>"
                       dialog-width="520" dialog-height="500"
                       href="<?=URL::to('/ccm/system/file/permissions?fID=' . $file->getFileID())?>"
                    ><?=t('Permissions &amp; Storage')?></a></li>
            <?php } ?>
            <?php if ($filePermissions->canDeleteFile()) { ?>
                <li><a class="dialog-launch dropdown-item launch-tooltip"
                       data-bs-placement="left"
                       title="<?= t('Completely removes a file and all its data.') ?>"
                       dialog-title="<?= t('Delete File') ?>"
                       dialog-width="550"
                       dialog-height="auto"
                       href="<?=URL::to('/ccm/system/dialogs/file/delete', $file->getFileID())?>"
                    ><?=t('Delete')?></a></li>
            <?php } ?>
        <?php }
        ?>
    </ul>


</div>

<script type="text/javascript">
    $(function() {
        $('a[data-action=rescan-file]').on('click', function (e) {
            e.preventDefault()
            $.concreteAjax({
                url: '<?=URL::to('/dashboard/files/details', 'rescan', $file->getFileID())?>',
                data: {
                    token: '<?=$token->generate("ccm-filedetails-rescan-{$file->getFileID()}")?>'
                },
                success: function (r) {
                    window.location.reload();
                }
            })
        })

        ConcreteEvent.subscribe('ConcreteDeleteFile', function() {
            window.location.href = '<?=URL::to('/dashboard/files/search', 'folder', (int) $folder->getTreeNodeID())?>'
        })

    })
</script>