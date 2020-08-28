<?php defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Attribute\ObjectInterface $attributedObject
 * @var Concrete\Core\Entity\Attribute\Set[] $attributeSets
 * @var Concrete\Core\Entity\Attribute\Key\Key[] $unassigned
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

    <?php foreach ($attributeSets as $set) { ?>
        <h5 class="mt-3"><?= $set->getAttributeSetDisplayName() ?></h5>
        <dl class="ccm-attribute-display">
            <?php foreach ($set->getAttributeKeys() as $key) { ?>
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
    <?php } ?>

    <?php if (count($unassigned)) { ?>

        <?php if (count($attributeSets)) { ?>
            <h5 class="mt-3"><?= t('Other') ?></h5>
        <?php } ?>

        <dl class="ccm-attribute-display">
            <?php foreach ($unassigned as $key) { ?>
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
    <?php } ?>

</section>