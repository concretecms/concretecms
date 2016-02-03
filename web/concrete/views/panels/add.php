<?php
use Concrete\Core\Page\Stack\Pile\PileContent;

/**
 * @var \Concrete\Core\Block\BlockType\BlockType[] $blockTypes
 */
defined('C5_EXECUTE') or die("Access Denied.");
?>

<section>
    <div data-panel-menu="accordion" class="ccm-panel-header-accordion">
        <nav>
            <span></span>
            <ul class="ccm-panel-header-accordion-dropdown">
                <li><a data-panel-accordion-tab="blocks"
                        <?php
                        if (!in_array($tab, ['clipboard', 'stacks', 'tiles'])) { ?>
                            data-panel-accordion-tab-selected="true"
                        <?php } ?>
                    ><?= t('Blocks') ?></a>
                </li>
                <li><a data-panel-accordion-tab="clipboard"
                       <?php if ($tab == 'clipboard') { ?>data-panel-accordion-tab-selected="true" <?php } ?>
                    ><?= t('Clipboard') ?></a>
                </li>
                <li><a data-panel-accordion-tab="stacks"
                       <?php if ($tab == 'stacks') { ?>data-panel-accordion-tab-selected="true" <?php } ?>
                    ><?= t('Stacks') ?></a>
                </li>
            </ul>
        </nav>
    </div>

    <?php
    switch ($tab) {

    case 'tiles':
        ?>
        Gathering tiles
        <?php
        break;

    case 'stacks':
        ?>
        <div id="ccm-panel-add-block-stack-list">
            <?php
            /** @var Stack[] $stacks */
            foreach ($stacks as $stack) {
                if (!$stack) {
                    continue;
                }

                /** @var Block[] $blocks */
                $blocks = $stack->getBlocks();
                $block_count = count($blocks);
                ?>
                <div class="ccm-panel-add-block-stack-item"
                     data-panel-add-block-drag-item="stack-item"
                     data-cID="<?= intval($c->getCollectionID()) ?>"
                     data-sID="<?= intval($stack->getCollectionID()) ?>"
                     data-block-type-handle="stack"
                     data-has-add-template="no"
                     data-supports-inline-add="no"
                     data-btID="0"
                     data-dragging-avatar="<?= h(
                         '<p><img src="/concrete/images/stack.png" /><span>' . t('Stack') . '</span></p>'
                     ) ?>"
                     data-block-id="<?= intval($stack->getCollectionID()) ?>">
                    <div class="stack-name">
                        <span class="handle"><?= htmlspecialchars($stack->getStackName()) ?></span>
                    </div>
                    <div class="blocks">
                        <div class="block-count">
                            <?= t2('%d Block', '%d Blocks', $block_count) ?>
                        </div>
                        <?php

                        foreach ($blocks as $block) {
                            $type = $block->getBlockTypeObject();
                            $icon = $ci->getBlockTypeIconURL($type);
                            ?>
                            <div class="block ccm-panel-add-block-draggable-block-type"
                                 data-panel-add-block-drag-item="block"
                                 data-cID="<?= $stack->getCollectionID() ?>"
                                 data-block-type-handle="<?= $type->getBlockTypeHandle() ?>"
                                 data-dialog-title="<?= t('Add %s', t($type->getBlockTypeName())) ?>"
                                 data-dialog-width="<?= $type->getBlockTypeInterfaceWidth() ?>"
                                 data-dialog-height="<?= $type->getBlockTypeInterfaceHeight() ?>"
                                 data-has-add-template="<?= $type->hasAddTemplate() ?>"
                                 data-supports-inline-add="<?= $type->supportsInlineAdd() ?>"
                                 data-btID="<?= $type->getBlockTypeID() ?>"
                                 data-dragging-avatar="<?=
                                 h(
                                     '<p><img src="' . $icon . '" /><span>' . t(
                                         $type->getBlockTypeName()
                                     ) . '</span></p>'
                                 ) ?>"
                                 title="<?= t($type->getBlockTypeName()) ?>"
                                 href="javascript:void(0)"
                                 data-block-id="<?= intval($block->getBlockID()) ?>">

                                <div class="block-name">
                                    <span class="handle"><?= h($type->getBlockTypeName()) ?></span>
                                </div>
                                <div class="block-content">
                                    <?php
                                    $bv = new \Concrete\Core\Block\View\BlockView($block);
                                    $bv->render('scrapbook');
                                    ?>
                                </div>
                                <div class="block-handle"></div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
        <script>
            $('div.ccm-panel-add-block-stack-item').each(function () {
                var active = false,
                    item = $(this),
                    count = item.find('div.block-count');

                item.click(function (e) {
                    e.preventDefault();
                    var method;
                    if (active) {
                        method = $.fn.removeClass;
                    } else {
                        method = $.fn.addClass;
                    }

                    active = !active;

                    method.call(item, 'active');

                    var blocks = item.find('div.blocks');
                    if (active) {
                        blocks.height('auto');
                        var height = blocks.height();
                        blocks.height('');
                        blocks.height(height);
                    } else {
                        blocks.height('');
                    }

                    setTimeout(function () {
                        count.hasClass('hidden') ? count.removeClass('hidden') : count.addClass('hidden');
                    }, 250);
                    return false;
                });
            });
            $('div.ccm-panel-add-block-stack-item').find('a.stack-handle').toggle(function (e) {
                e.preventDefault();
                $(this).closest('div.ccm-panel-add-block-stack-item').addClass('active');
            }, function (e) {
                e.preventDefault();
                $(this).closest('div.ccm-panel-add-block-stack-item').removeClass('active');
            })
        </script>
        <?php
        break;

    case 'clipboard':
        ?>

        <div id="ccm-panel-add-clipboard-block-list">
            <?php
            /** @var PileContent[] $contents */
            $pileToken = Core::make('token')->generate('tools/clipboard/from');
            foreach ($contents as $pile_content) {
                $block = Block::getByID($pile_content->getItemID());

                if (!$block || !is_object($block) || $block->isError()) {
                    continue;
                }

                $type = $block->getBlockTypeObject();
                $icon = $ci->getBlockTypeIconURL($type);
                ?>
                <div class="ccm-panel-add-clipboard-block-item"
                     data-event="duplicate"
                     data-panel-add-block-drag-item="clipboard-item"
                     data-name="<?= h($type->getBlockTypeName()) ?>"
                     data-cID="<?= $c->getCollectionID() ?>"
                     data-token="<?= $pileToken ?>"
                     data-block-type-handle="<?= $type->getBlockTypeHandle() ?>"
                     data-dialog-title="<?= t('Add %s', t($type->getBlockTypeName())) ?>"
                     data-dialog-width="<?= $type->getBlockTypeInterfaceWidth() ?>"
                     data-dialog-height="<?= $type->getBlockTypeInterfaceHeight() ?>"
                     data-has-add-template="<?= $type->hasAddTemplate() ?>"
                     data-supports-inline-add="<?= $type->supportsInlineAdd() ?>"
                     data-btID="<?= $type->getBlockTypeID() ?>"
                     data-pcID="<?= $pile_content->getPileContentID() ?>"
                     data-dragging-avatar="<?=
                     h(
                         '<p><img src="' . $icon . '" /><span>' . t(
                             $type->getBlockTypeName()
                         ) . '</span></p>'
                     ) ?>"
                     data-block-id="<?= intval($block->getBlockID()) ?>">
                    <div class="block-content">
                        <div class="block-name">
                            <span class="handle"><?= h(t($type->getBlockTypeName())) ?></span>
                        </div>
                        <div class="blocks">
                            <div class="block ccm-panel-add-block-draggable-block-type"
                                 title="<?= t($type->getBlockTypeName()) ?>">
                                <div class="block-content">
                                    <?php
                                    $bv = new \Concrete\Core\Block\View\BlockView($block);
                                    $bv->render('scrapbook');
                                    ?>
                                </div>
                                <div class="block-handle"></div>
                            </div>
                        </div>
                    </div>
                    <div class="delete">
                        <button class="ccm-delete-clipboard-item pull-right btn btn-sm btn-link">
                            <?= t('Delete') ?>
                        </button>
                    </div>
                </div>
                <?php
            }
            ?>
            <script>
                $('button.ccm-delete-clipboard-item').unbind().click(function (e) {
                    e.preventDefault();
                    var me = $(this),
                        item = me.closest('.ccm-panel-add-clipboard-block-item');

                    $.post(CCM_TOOLS_PATH + '/pile_manager', {
                        task: 'delete',
                        pcID: item.data('pcid'),
                        cID: item.data('cid'),
                        ccm_token: item.data('token')
                    }, function () {
                        item.remove();
                    }).fail(function (data) {
                        alert("<?= t('An error occurred while deleting this item:') ?>\n" + data.responseJSON.errors.join("\n"));
                    });
                    return false;
                });
            </script>
        </div>

        <?php
        break;

    default:
    ?>
    <div class="ccm-panel-header-search">
        <i class="fa fa-search"></i>
        <input type="text" data-input="search-blocks" placeholder="<?= t('Search') ?>" autocomplete="false"/>
    </div>

    <div class="ccm-panel-content-inner" id="ccm-panel-add-blocktypes-list">

        <?php
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
            <div class="ccm-panel-add-block-set">
                <header><?= $set->getBlockTypeSetDisplayName() ?></header>
                <ul>
                    <?php
                    /** @var \Concrete\Core\Block\BlockType\BlockType[] $blocktypes */
                    $blocktypes = $types[$set->getBlockTypeSetName()];
                    if (!$blocktypes) {
                        $blocktypes = array();
                    }
                    if (count($blocktypes)) {
                        usort(
                            $blocktypes,
                            function ($bt_a, $bt_b) use ($set) {
                                return ($set->displayOrder($bt_a) > $set->displayOrder($bt_b)) ? 1 : -1;
                            }
                        );

                        foreach ($blocktypes as $bt) {
                            $btIcon = $ci->getBlockTypeIconURL($bt);

                            ?>

                            <li>
                                <a
                                    data-panel-add-block-drag-item="block"
                                    class="ccm-panel-add-block-draggable-block-type"
                                    data-cID="<?= $c->getCollectionID() ?>"
                                    data-block-type-handle="<?= $bt->getBlockTypeHandle() ?>"
                                    data-dialog-title="<?= t('Add %s', t($bt->getBlockTypeName())) ?>"
                                    data-dialog-width="<?= $bt->getBlockTypeInterfaceWidth() ?>"
                                    data-dialog-height="<?= $bt->getBlockTypeInterfaceHeight() ?>"
                                    data-has-add-template="<?= $bt->hasAddTemplate() ?>"
                                    data-supports-inline-add="<?= $bt->supportsInlineAdd() ?>"
                                    data-btID="<?= $bt->getBlockTypeID() ?>"
                                    data-dragging-avatar="<?=
                                    h(
                                        '<p><img src="' . $btIcon . '" /><span>' . t(
                                            $bt->getBlockTypeName()
                                        ) . '</span></p>'
                                    ) ?>"
                                    title="<?= t($bt->getBlockTypeName()) ?>"
                                    href="javascript:void(0)"
                                >
                                    <p><img src="<?= $btIcon ?>"/><span><?= t($bt->getBlockTypeName()) ?></span></p>
                                </a>
                            </li>

                            <?php
                        }
                        ?>
                        <?php
                    } else {
                        ?>
                        <p><?= t('No block types available.') ?></p>
                        <?php
                    }
                    ?>
                </ul>
            </div>

            <?php
        } ?>

        <?php if (isset($types['Other']) && is_array($types['Other'])) {
            ?>

            <div class="ccm-panel-add-block-set">
                <header><?= t('Other') ?></header>
                <ul>
                    <?php $blocktypes = $types['Other'];
                    foreach ($blocktypes as $bt) {
                        $btIcon = $ci->getBlockTypeIconURL($bt);
                        ?>

                        <li>
                            <a
                                data-panel-add-block-drag-item="block"
                                class="ccm-panel-add-block-draggable-block-type"
                                data-cID="<?= $c->getCollectionID() ?>"
                                data-block-type-handle="<?= $bt->getBlockTypeHandle() ?>"
                                data-dialog-title="<?= t('Add %s', t($bt->getBlockTypeName())) ?>"
                                data-dialog-width="<?= $bt->getBlockTypeInterfaceWidth() ?>"
                                data-dialog-height="<?= $bt->getBlockTypeInterfaceHeight() ?>"
                                data-has-add-template="<?= $bt->hasAddTemplate() ?>"
                                data-supports-inline-add="<?= $bt->supportsInlineAdd() ?>"
                                data-btID="<?= $bt->getBlockTypeID() ?>"
                                data-dragging-avatar="<?=
                                h(
                                    '<p><img src="' . $btIcon . '" /><span>' . t(
                                        $bt->getBlockTypeName()
                                    ) . '</span></p>'
                                ) ?>"
                                title="<?= t($bt->getBlockTypeName()) ?>"
                                href="javascript:void(0)"
                            ><p><img src="<?= $btIcon ?>"/><span><?= t($bt->getBlockTypeName()) ?></span></p></a>
                        </li>

                        <?php
                    }
                    ?>
                </ul>

            </div>

            <?php
        } ?>

        <?php if (Config::get('concrete.marketplace.enabled')) {
            ?>
            <div class="ccm-marketplace-btn-wrapper">
                <button type="button" onclick="window.location.href='<?= URL::to('/dashboard/extend/addons') ?>'"
                        class="btn-info btn-block btn btn-large"><?= t("Get More Blocks") ?></button>
            </div>
            <?php
        } ?>

    </div>

</section>

<?php } ?>
