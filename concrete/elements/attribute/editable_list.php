<?php defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Attribute\ObjectInterface $attributedObject
 * @var Concrete\Core\Entity\Attribute\Key\Key[] $attributes
 * @var League\Url\UrlInterface $editDialogURL
 * @var string $sectionTitle
 */

?>

<section data-section="attributes">

    <?php if ($editDialogURL) { ?>
        <a class="dialog-launch btn-section btn btn-secondary"
           href="<?= $editDialogURL ?>"
           dialog-width="800" dialog-height="640" dialog-title="<?= t('Edit Attributes') ?>">
            <?= t('Edit') ?></a>
    <?php } ?>

    <?php if ($sectionTitle) { ?>
        <h3><?= $sectionTitle ?></h3>
    <?php } ?>

    <dl class="ccm-attribute-display">
        <?php foreach ($attributes as $key) { ?>
            <dt><?= $key->getAttributeKeyDisplayName() ?></dt>
            <dd><?php
                $value = $attributedObject->getAttributeValueObject($key);
                if ($value !== null) {
                    echo $value->getDisplayValue();
                }
                ?>
            </dd>
        <?php } ?>
    </dl>

</section>