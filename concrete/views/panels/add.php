<?php

use Concrete\Core\Block\View\BlockView;

defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Permission\Checker $cp */
/* @var Concrete\Core\Page\Page $c */

/* @var string $tab */
/* @var Concrete\Core\Application\Service\Urls $ci */
?>
<section>
    <?php if ($tab == 'containers' || $tab == 'blocks') { ?>
        <div class="ccm-panel-header-search">
            <svg><use xlink:href="#icon-search" /></svg>
            <input type="text" data-input="search-blocks" placeholder="<?= t('Search') ?>" autocomplete="false"/>
        </div>
    <?php } ?>

    <header class="pl-0 pr-0">
        <div id="dropdown-menu" class="dropdown" data-panel-menu="dropdown">
            <div class="ccm-panel-header-list-grid-view-switcher"><i class="fa fa-list fa-xs fa-fw"></i></div>
            <h4 data-toggle="dropdown" data-panel-header="dropdown-menu" class="dropdown-toggle">
                <?php
                switch($tab) {
                    case 'containers':
                        print t('Containers');
                        break;
                    case 'stacks':
                        print t('Stacks');
                        break;
                    case 'clipboard':
                        print t('Clipboard');
                        break;
                    case 'blocks':
                        print t('Blocks');
                        break;
                }
                ?>
            </h4>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="#" data-panel-dropdown-tab="blocks">Blocks</a>
                <a class="dropdown-item" href="#" data-panel-dropdown-tab="clipboard">Clipboard</a>
                <a class="dropdown-item" href="#" data-panel-dropdown-tab="stacks">Stacks</a>
                <a class="dropdown-item" href="#" data-panel-dropdown-tab="containers">Containers</a>
            </div>
        </div>
    </header>

    <?php
    switch ($tab) {
    case 'containers':
        /* @var Concrete\Core\Entity\Page\Container[] $containers */
        ?>
        <div class="ccm-panel-content-inner ccm-stacked-list" id="ccm-panel-add-blocktypes-list" data-hide-grid-view-switcher>
            <ul class="ccm-stacked-list">
                <?php
                foreach ($containers as $container) {
                    ?>
                    <li>
                        <a
                            href="#"
                            class="ccm-panel-add-container-item"
                            data-panel-add-block-drag-item="container"
                            data-cID="<?= (int) $c->getCollectionID() ?>"
                            data-container-id="<?=$container->getContainerID() ?>"
                            data-block-type-handle="core_container"
                            data-has-add-template="0"
                            data-supports-inline-add="0"
                            data-btID="0"
                            data-dragging-avatar="<?= h('<div class="ccm-block-icon-wrapper d-flex align-items-center justify-content-center">' . $container->getContainerIconImage() . '</div><p><span>' . $container->getContainerName() . '</span></p>') ?>"
                        >
                        <!-- <span class="handle"> -->
                            <div class="ccm-block-icon-wrapper d-flex align-items-center justify-content-center"><img src="<?=$container->getContainerIconImage(false)?>" /></div>
                                <p><span><?= h($container->getContainerName()) ?></span></p>
                        <!-- </span> -->
                        </a>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </div>
        <script>

        </script>
    <?php
    break;

        case 'stacks':
            /* @var Concrete\Core\Page\Stack\Stack[] $stacks */
            ?>
            <div id="ccm-panel-add-block-stack-list">
                <?php
                    View::element('panels/add/stack_list', ['stacks' => $stacks, 'c' => $c]);
                ?>
            </div>
            <script>
            $('#ccm-panel-add-block-stack-list').on('click', 'div.ccm-panel-add-block-stack-item', function () {
                var $stack = $(this);
                if ($stack.data('ccm-stack-content-loaded')) {
                	$stack.toggleClass('ccm-panel-add-block-stack-item-expanded');
                	$stack.data('ccm-stack-content-loaded').toggle($stack.hasClass('ccm-panel-add-block-stack-item-expanded'));
                    return;
                }
                if ($stack.hasClass('ccm-panel-add-block-stack-item-expanded')) {
                    return;
                }
                $.concreteAjax({
                    dataType: 'html',
                    type: 'POST',
                    data: {'cID': $(this).attr('data-cID'), 'stackID': $(this).attr('data-sID'), 'ccm_token': $(this).attr('data-token')},
                    url: '<?=URL::to('/ccm/system/panels/add/get_stack_contents')?>',
                    success: function (r) {
                        var $content = $(r);
                        $stack.append($content);
                        $stack.data('ccm-stack-content-loaded', $content);
                        $stack.addClass('ccm-panel-add-block-stack-item-expanded');
                        $content.find('div.block').each(function () {
                            var block, me = $(this), dragger = me.find('div.block-name');
                            var stack = new Concrete.Stack($stack, Concrete.getEditMode(), null);
                            block = new Concrete.StackBlock($(this), stack, stack, dragger);

                            block.setPeper(dragger);
                        });
                    }
                })
            });

            $('#ccm-panel-add-block-stack-list').on('click', 'div.ccm-panel-add-folder-stack-item', function () {
                var $stackFolder = $(this);
                if ($stackFolder.data('ccm-folder-stack-content-loaded')) {
                    $stackFolder.toggleClass('ccm-panel-add-folder-stack-item-expanded');
                    $stackFolder.data('ccm-folder-stack-content-loaded').toggle($stackFolder.hasClass('ccm-panel-add-folder-stack-item-expanded'));
                    return;
                }
                if ($stackFolder.hasClass('ccm-panel-add-folder-stack-item-expanded')) {
                    return;
                }
                $.concreteAjax({
                    dataType: 'html',
                    type: 'POST',
                    data: {'cID': $(this).attr('data-cID'), 'stackFolderID': $(this).attr('data-sfID')},
                    url: '<?=URL::to('/ccm/system/panels/add/get_stack_folder_contents')?>',
                    success: function (r) {
                        var $content = $(r);
                        $stackFolder.after($content);
                        $stackFolder.data('ccm-folder-stack-content-loaded', $content);
                        $stackFolder.addClass('ccm-panel-add-folder-stack-item-expanded');
                        $content.find('div.ccm-panel-add-block-stack-item').each(function () {
                            var stack; var me = $(this); var dragger = me.find('div.ccm-panel-add-block-stack-item-handle');
                            stack = new Concrete.Stack($(this), Concrete.getEditMode(), dragger);

                            stack.setPeper(dragger)
                        });
                    }
                })
            });
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
                        data-dragging-avatar="<?= h('<div class="ccm-block-icon-wrapper d-flex align-items-center justify-content-center"><img src="' . $icon . '" /></div><p><span>' . t($type->getBlockTypeName()) . '</span></p>') ?>"
                        data-block-id="<?= (int) ($block->getBlockID()) ?>"
                    >

                        <div class="block-content">
                            <div class="block-name float-left">
                                <span class="handle"><?= h(t($type->getBlockTypeName())) ?></span>
                            </div>
                            <div class="delete float-right">
                                <button class="ccm-delete-clipboard-item btn btn-sm btn-link text-danger"><?= t('Delete') ?></button>
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
            <div class="ccm-panel-content-inner" id="ccm-panel-add-blocktypes-list">
                <?php
                $i = 0;
                foreach ($blockTypesForSets as $setName => $blockTypes) {
                    $i++;
                    ?>
                    <div class="ccm-panel-add-block-set">
                        <header
                            data-toggle="collapse"
                            data-target="#ccm-block-set-<?= $i ?>"
                            aria-expanded="true"
                            aria-controls="ccm-block-set-<?= $i ?>"
                        >
                            <?= $setName ?><i class="fa fa-chevron-up float-right"></i>
                        </header>
                        <div id="ccm-block-set-<?= $i ?>" class="ccm-block-set collapse show">
                        <?php
                            // This class is added to help align the last row when it contains less than 3 elements
                            $justifyLastRowClass= (count($blockTypes) % 3) > 0 ? 'ccm-flex-align-last-row' : '';
                        ?>
                        <ul class="d-flex flex-row flex-wrap justify-content-between <?= $justifyLastRowClass; ?>">
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
                                        data-dragging-avatar="<?= h('<div class="ccm-block-icon-wrapper d-flex align-items-center justify-content-center"><img src="' . $btIcon . '" /></div><p><span>' . t($bt->getBlockTypeInSetName()) . '</span></p>') ?>"
                                        title="<?= t($bt->getBlockTypeName()) ?>"
                                        href="javascript:void(0)"
                                    >
                                        <div class="ccm-block-icon-wrapper d-flex align-items-center justify-content-center"><img src="<?= $btIcon ?>"/></div>
                                        <p><span><?= t($bt->getBlockTypeInSetName()) ?></span></p>
                                    </a>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                        </div>
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
?>

<script type="text/javascript">
    $(function() {
        // switching the up/down arrows for collapsing block sets
        $('#ccm-panel-add-block').find('div[id^="ccm-block-set-"]').on('hidden.bs.collapse shown.bs.collapse', function () {
            $(this).prev('header[data-toggle="collapse"]').find('i.fa').toggleClass('fa-chevron-up fa-chevron-down');
        });

        // This makes the elements under the dropdown not react to hover.
        // Originally the block below the dropdown would grab the focus
        // and it would be impossible to click a link in the dropdown
        $('#dropdown-menu').on('hide.bs.dropdown shown.bs.dropdown', function () {
            $('#ccm-panel-add-blocktypes-list').toggleClass('ccm-no-pointer-events');
        });
        // switching between grid and stacked view for blocks
        var gridViewSwitcher = $('.ccm-panel-header-list-grid-view-switcher');

        gridViewSwitcher.on('click', function () {
            $('#ccm-panel-add-blocktypes-list').toggleClass('ccm-stacked-list');
            $(this).find('i.fa').toggleClass('fa-list fa-th');
        });

        // hiding the grid view switcher when not needed.
        // This uses a data attribute to mark panels
        // that don't require the grid switcher
        Concrete.event.bind('PanelLoad', function(evt, data) {
            gridViewSwitcher.removeClass('d-none');
            var element = $(data.element);
            if (element.find('[data-hide-grid-view-switcher]').length) {
                gridViewSwitcher.addClass('d-none');
            }
        });
    });
</script>