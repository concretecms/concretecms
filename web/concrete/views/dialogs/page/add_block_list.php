<?php
defined('C5_EXECUTE') or die("Access Denied.");
$sets = BlockTypeSet::getList();
$types = array();
foreach ($blockTypes as $bt) {
    if (!$cp->canAddBlockType($bt)) {
        continue;
    }

    $btsets = $bt->getBlockTypeSets();
    foreach ($btsets as $set) {
        $types[$set->getBlockTypeSetName()][] = $bt;
    }
    if (count($btsets) == 0) {
        $types['Other'][] = $bt;
    }
}

for ($i = 0; $i < count($sets); ++$i) {
    $set = $sets[$i];
    ?>

<div class="ccm-ui" id="ccm-add-block-list">

<section>
    <legend><?= $set->getBlockTypeSetDisplayName() ?></legend>
    <ul class="item-select-list">
        <?php $blockTypes = isset($types[$set->getBlockTypeSetName()]) ? $types[$set->getBlockTypeSetName()] : array();
    foreach ($blockTypes as $bt) {
        $btIcon = $ci->getBlockTypeIconURL($bt);
        ?>
            <li>
                <a
                    data-cID="<?= $c->getCollectionID() ?>"
                    data-block-type-handle="<?= $bt->getBlockTypeHandle() ?>"
                    data-dialog-title="<?= t('Add %s', t($bt->getBlockTypeName())) ?>"
                    data-dialog-width="<?= $bt->getBlockTypeInterfaceWidth() ?>"
                    data-dialog-height="<?= $bt->getBlockTypeInterfaceHeight() ?>"
                    data-has-add-template="<?= $bt->hasAddTemplate() ?>"
                    data-supports-inline-add="<?= $bt->supportsInlineAdd() ?>"
                    data-btID="<?= $bt->getBlockTypeID() ?>"
                    title="<?= t($bt->getBlockTypeName()) ?>"
                    href="javascript:void(0)"><img src="<?=$btIcon?>" /> <?=t($bt->getBlockTypeName())?></a>
            </li>
        <?php 
    }
    ?>
    </ul>
</section>

<?php 
} ?>

<?php if (is_array($types['Other'])) {
    ?>

    <section>
        <legend><?=t('Other')?></legend>
        <ul class="item-select-list">
            <?php $blockTypes = $types['Other'];
    foreach ($blockTypes as $bt) {
        $btIcon = $ci->getBlockTypeIconURL($bt);
        ?>
                <li>
                    <a
                        data-cID="<?= $c->getCollectionID() ?>"
                        data-block-type-handle="<?= $bt->getBlockTypeHandle() ?>"
                        data-dialog-title="<?= t('Add %s', t($bt->getBlockTypeName())) ?>"
                        data-dialog-width="<?= $bt->getBlockTypeInterfaceWidth() ?>"
                        data-dialog-height="<?= $bt->getBlockTypeInterfaceHeight() ?>"
                        data-has-add-template="<?= $bt->hasAddTemplate() ?>"
                        data-supports-inline-add="<?= $bt->supportsInlineAdd() ?>"
                        data-btID="<?= $bt->getBlockTypeID() ?>"
                        title="<?= t($bt->getBlockTypeName()) ?>"
                        href="javascript:void(0)"><img src="<?=$btIcon?>" /> <?=t($bt->getBlockTypeName())?></a>
                </li>
            <?php 
    }
    ?>
        </ul>
    </section>

<?php 
} ?>

</div>

<script type="text/javascript">
    $(function() {
        $('#ccm-add-block-list').on('click', 'a', function() {
            ConcreteEvent.publish('AddBlockListAddBlock', {
                $launcher: $(this)
            });
            return false;
        });
    });
</script>