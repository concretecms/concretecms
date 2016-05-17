<?php defined('C5_EXECUTE') or die("Access Denied.");

if ($mode == \Concrete\Core\Entity\Attribute\Key\Type\ImageFileType::TYPE_FILE_MANAGER) {

    $al = Core::make('helper/concrete/asset_library');
    print $al->file('ccm-file-akID-' . $controller->getAttributeKey()->getAttributeKeyID(), $this->field('value'), t('Choose File'), $file);

} else { ?>

    <input type="file" name="<?=$view->field('value')?>">

<?php } ?>