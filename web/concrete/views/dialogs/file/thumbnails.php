<?
defined('C5_EXECUTE') or die("Access Denied.");
?>
<div class="ccm-ui">

<? foreach($types as $type) {
    $width = $type->getWidth();
    $height = $type->getHeight() ? $type->getHeight() : t('Automatic');
    $thumbnailPath = $type->getFilePath($version);
    $location = $version->getFile()->getFileStorageLocationObject();
    $configuration = $location->getConfigurationObject();
    $filesystem = $location->getFileSystemObject();
    $hasFile = $filesystem->has($thumbnailPath);
    ?>
    <h4><?=$type->getName()?> <small><?=t('%s x %s dimensions', $width, $height)?></small>
        <? if ($fp->canEditFile() && $hasFile) { ?>
            <a href="<?=URL::to('/ccm/system/dialogs/file/thumbnails/edit')?>?fID=<?=$version->getFileID()?>&fvID=<?=$version->getFileID()?>&thumbnail=<?=$type->getHandle()?>" dialog-width="90%" dialog-height="70%"
               class="pull-right btn btn-sm btn-default dialog-launch" dialog-title="<?=t('Edit Thumbnail Images')?>" ><?=t('Edit Thumbnail')?></a>
        <? } ?>
    </h4>
    <hr/>
    <div class="ccm-file-manager-image-thumbnail">
    <?
        if ($hasFile) { ?>
            <img style="max-width: 100%" src="<?=$configuration->getPublicURLToFile($thumbnailPath)?>" />
        <? } else { ?>
            <?=t('No thumbnail found. Usually this is because the source file is smaller than this thumbnail configuration.')?>
        <? } ?>
    </div>

<? } ?>


</div>