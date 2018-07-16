<?php

use Concrete\Core\Block\View\BlockView;

defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Permission\Checker $cp */
/* @var Concrete\Core\Page\Page $c */

/* @var string $tab */
/* @var Concrete\Core\Application\Service\Urls $ci */
?>
<section>
    <div data-panel-menu="accordion" class="ccm-panel-header-accordion">
        <nav>
            <span></span>
            <ul class="ccm-panel-header-accordion-dropdown">
                <li><a data-panel-accordion-tab="blocks"<?= $tab === 'blocks' ? ' data-panel-accordion-tab-selected="true"' : '' ?>><?= t('Blocks') ?></a></li>
                <li><a data-panel-accordion-tab="clipboard"<?= $tab === 'clipboard' ? ' data-panel-accordion-tab-selected="true"' : '' ?>><?= t('Clipboard') ?></a></li>
                <li><a data-panel-accordion-tab="stacks"<?= $tab === 'stacks' ? ' data-panel-accordion-tab-selected="true"' : '' ?>><?= t('Stacks') ?></a></li>
            </ul>
        </nav>
    </div>
    <?php
    switch ($tab) {
        case 'stacks':
            /* @var Concrete\Core\Page\Stack\Stack[] $stacks */
            ?>
            <div id="ccm-panel-add-block-stack-list">
                <?php
                foreach ($stacks as $stack) {
                    if (!$stack) {
                        continue;
                    }
                    $blocks = $stack->getBlocks();
                    ?>
                    <div
                        class="ccm-panel-add-block-stack-item"
                        data-panel-add-block-drag-item="stack-item"
                        data-cID="<?= (int) $c->getCollectionID() ?>"
                        data-sID="<?= (int) $stack->getCollectionID() ?>"
                        data-block-type-handle="stack"
                        data-has-add-template="no"
                        data-supports-inline-add="no"
                        data-btID="0"
                        data-dragging-avatar="<?= h('<p><img src="' . URL::to('/concrete/images/stack.png') . '" /><span>' . t('Stack') . '</span></p>') ?>"
                        data-block-id="<?= (int) $stack->getCollectionID() ?>"
                    >
                        <div class="stack-name">
                            <span class="handle"><?= h($stack->getStackName()) ?></span>
                        </div>
                        <div class="blocks">
                            <div class="block-count">
                                <?= t2('%d Block', '%d Blocks', count($blocks)) ?>
                            </div>
                            <?php
                            foreach ($blocks as $block) {
                                $type = $block->getBlockTypeObject();
                                $icon = $ci->getBlockTypeIconURL($type);
                                ?>
                                <div
                                    class="block ccm-panel-add-block-draggable-block-type"
                                    data-panel-add-block-drag-item="block"
                                    data-cID="<?= $stack->getCollectionID() ?>"
                                    data-block-type-handle="<?= $type->getBlockTypeHandle() ?>"
                                    data-dialog-title="<?= t('Add %s', t($type->getBlockTypeName())) ?>"
                                    data-dialog-width="<?= $type->getBlockTypeInterfaceWidth() ?>"
                                    data-dialog-height="<?= $type->getBlockTypeInterfaceHeight() ?>"
                                    data-has-add-template="<?= $type->hasAddTemplate() ?>"
                                    data-supports-inline-add="<?= $type->supportsInlineAdd() ?>"
                                    data-btID="<?= $type->getBlockTypeID() ?>"
                                    data-dragging-avatar="<?= h('<p><img src="' . $icon . '" /><span>' . t($type->getBlockTypeName()) . '</span></p>') ?>"
                                    title="<?= t($type->getBlockTypeName()) ?>"
                                    data-block-id="<?= (int) $block->getBlockID() ?>"
                                >
                                    <div class="block-name">
                                        <span class="handle"><?= h($type->getBlockTypeName()) ?></span>
                                    </div>
                                    <div class="block-content">
                                        <?php
                                        $bv = new BlockView($block);
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
            /* @var Concrete\Core\Page\Stack\Pile\PileContent[] $contents */
            ?>
            <div id="ccm-panel-add-clipboard-block-list">
                <?php
                $pileToken = Core::make('token')->generate('tools/clipboard/from');
                foreach ($contents as $pile_content) {
                    $block = Block::getByID($pile_content->getItemID());
                    if (!$block || !is_object($block) || $block->isError()) {
                        continue;
                    }
                    $type = $block->getBlockTypeObject();
                    $icon = $ci->getBlockTypeIconURL($type);
                    ?>
                    <div
                        class="ccm-panel-add-clipboard-block-item"
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
                        data-dragging-avatar="<?= h('<p><img src="' . $icon . '" /><span>' . t($type->getBlockTypeName()) . '</span></p>') ?>"
                        data-block-id="<?= (int) ($block->getBlockID()) ?>"
                    >
                        <div class="block-content">
                            <div class="block-name">
                                <span class="handle"><?= h(t($type->getBlockTypeName())) ?></span>
                            </div>
                            <div class="blocks">
                                <div class="block ccm-panel-add-block-draggable-block-type" title="<?= t($type->getBlockTypeName()) ?>">
                                    <div class="block-content">
                                        <?php
                                        $bv = new BlockView($block);
                                        $bv->render('scrapbook');
                                        ?>
                                    </div>
                                    <div class="block-handle"></div>
                                </div>
                            </div>
                        </div>
                        <div class="delete">
                            <button class="ccm-delete-clipboard-item pull-right btn btn-sm btn-link"><?= t('Delete') ?></button>
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

        case 'blocks':
            /* @var Concrete\Core\Entity\Block\BlockType\BlockType[] $blockTypesForSets */
            ?>
            <div class="ccm-panel-header-search">
                <i class="fa fa-search"></i>
                <input type="text" data-input="search-blocks" placeholder="<?= t('Search') ?>" autocomplete="false"/>
            </div>
            <div class="ccm-panel-content-inner" id="ccm-panel-add-blocktypes-list">
                <?php
                foreach ($blockTypesForSets as $setName => $blockTypes) {
                    ?>
                    <div class="ccm-panel-add-block-set">
                        <header><?= $setName ?></header>
                        <ul>
                            <?php
                            foreach ($blockTypes as $bt) {
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
                                        data-dragging-avatar="<?= h('<p><img src="' . $btIcon . '" /><span>' . t($bt->getBlockTypeInSetName()) . '</span></p>') ?>"
                                        title="<?= t($bt->getBlockTypeName()) ?>"
                                        href="javascript:void(0)"
                                    >
                                        <p><img src="<?= $btIcon ?>"/><span><?= t($bt->getBlockTypeInSetName()) ?></span></p>
                                    </a>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                    </div>
                    <?php
                }
                $p = new Permissions();
                if (Config::get('concrete.marketplace.enabled') && $p->canInstallPackages()) {
                    ?>
                    <div class="ccm-marketplace-btn-wrapper">
                        <button
                            type="button"
                            onclick="window.location.href='<?= URL::to('/dashboard/extend/addons') ?>'"
                            class="btn-info btn-block btn btn-large"
                        ><?= t('Get More Blocks') ?></button>
                    </div>
                    <?php
                }
                ?>
            </div>
        </section>
        <?php
        break;
}
