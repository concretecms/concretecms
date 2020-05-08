<?php

/**
 * @var $resultsBulkMenu \Concrete\Core\Application\UserInterface\ContextMenu\DropdownMenu
 */

defined('C5_EXECUTE') or die('Access Denied.');

$fp = FilePermissions::getGlobal();

if ($fp->canAddFile() || $fp->canSearchFiles()) { ?>

    <div class="table-responsive">
        <table class="ccm-search-results-table" data-search-results="files">
            <thead>
            <tr>

                <th colspan="2" class="ccm-search-results-bulk-selector">

                    <div class="btn-group dropdown">
                        <span class="btn btn-secondary" data-search-checkbox-button="select-all">
                            <input type="checkbox" data-search-checkbox="select-all" />
                        </span>
                        <button type="button" disabled="disabled" data-search-checkbox-button="dropdown"
                                class="btn btn-secondary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" data-reference="parent">
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <div data-search-menu="dropdown">
                            <?php
                            echo $resultsBulkMenu->getMenuElement();
                            ?>
                        </div>
                    </div>


                </th>
                <?php foreach ($result->getColumns() as $column) { ?>
                    <th class="<?=$column->getColumnStyleClass()?>">
                        <?php if ($column->isColumnSortable()) { ?>
                            <a href="<?=$column->getColumnSortURL()?>"><?=$column->getColumnTitle()?></a>
                        <?php } else { ?>
                            <span><?=$column->getColumnTitle()?></span>
                        <?php } ?>
                    </th>
                <?php } ?>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($result->getItems() as $item) {
                $detailsURL = '#';
                if ($item->getItem() instanceof \Concrete\Core\Tree\Node\Type\File) {
                    $detailsURL = URL::to('/dashboard/files/details', 'view', $item->getItem()->getTreeNodeFileID());
                }
                ?>
                <tr data-details-url="<?=$detailsURL?>">
                    <td class="ccm-search-results-checkbox">
                        <input data-search-checkbox="individual" type="checkbox"
                               data-node-type="<?=$item->getItem()->getTreeNodeTypeHandle()?>"
                               data-item-id="<?=$item->getResultFileID()?>" />
                    </td>
                    <td class="ccm-search-results-icon">
                        <?=$item->getListingThumbnailImage()?>
                    </td>
                    <?php
                    $i = 0;
                    foreach ($item->getColumns() as $column) { ?>
                        <td <?php if ($i == 0) { ?>class="ccm-search-results-name<?php } ?>">
                            <?=$column->getColumnValue($item);?>
                        </td>
                        <?php
                        $i++;
                    }
                    ?>
                    <td class="ccm-search-results-menu-launcher">
                        <div class="dropdown" data-menu="search-result">
                            <button class="btn btn-icon" data-boundary="viewport" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <svg width="16" height="4"><use xlink:href="#icon-menu-launcher" /></svg>
                            </button>
                            <?php
                            $menu = $item->getItem()->getTreeNodeMenu();
                            echo $menu->getMenuElement();
                            ?>
                        </div>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <?=$result->getPagination()->renderView('dashboard')?>

    <?php
} else {
    ?>
	<p>
        <?= t('You do not have access to the file manager.') ?>
    </p>
    <?php
}
