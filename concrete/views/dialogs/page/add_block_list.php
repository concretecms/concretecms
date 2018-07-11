<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Permission\Checker $cp */
/* @var Concrete\Core\Page\Page $c */

/* @var Concrete\Core\Application\Service\Urls $ci */

/* @var Concrete\Core\Entity\Block\BlockType\BlockType[] $blockTypesForSets */

foreach ($blockTypesForSets as $setName => $blockTypes) {
    ?>
    <div class="ccm-ui" id="ccm-add-block-list">
        <section>
            <legend><?= $setName ?></legend>
            <ul class="item-select-list">
                <?php
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
                            href="javascript:void(0)"
                        ><img src="<?=$btIcon?>" /> <?=t($bt->getBlockTypeName())?></a>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </section>
        <?php
    }
    ?>
</div>
<script>
    $(function() {
        $('#ccm-add-block-list').on('click', 'a', function() {
            ConcreteEvent.publish('AddBlockListAddBlock', {
                $launcher: $(this)
            });
            return false;
        });
    });
</script>
