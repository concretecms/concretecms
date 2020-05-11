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
                ?>
                <tr data-details-url="javascript:void(0)">
                    <td class="ccm-search-results-checkbox">
                        <?php if ($item->getResultFileID() > 0) { ?>
                            <input data-search-checkbox="individual" type="checkbox"
                                   data-node-type="<?=$item->getItem()->getTreeNodeTypeHandle()?>"
                                   data-item-id="<?=$item->getResultFileID()?>" />
                        <?php } ?>
                    </td>
                    <td class="ccm-search-results-icon">
                        <?=$item->getListingThumbnailImage()?>
                    </td>
                    <?php
                    $i = 0;
                    foreach ($item->getColumns() as $column) {
                        $class = '';
                        if ($column->getColumn() instanceof \Concrete\Core\File\Search\ColumnSet\Column\NameColumn) {
                            $class = 'ccm-search-results-name';
                            $value = '<a href="' . $item->getDetailsURL() . '">' . $column->getColumnValue($item) . '</a>';
                        } else {
                            $value = $column->getColumnValue($item);
                        }
                        ?>
                        <td class="<?=$class?>"><?=$value?></td>
                        <?php
                        $i++;
                    }

                    $menu = $item->getItem()->getTreeNodeMenu();
                    if ($menu) {
                    ?>
                    <td class="ccm-search-results-menu-launcher">
                        <div class="dropdown" data-menu="search-result">
                            <button class="btn btn-icon" data-boundary="viewport" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <svg width="16" height="4"><use xlink:href="#icon-menu-launcher" /></svg>
                            </button>
                            <?php
                            echo $menu->getMenuElement();
                            ?>
                        </div>
                    </td>
                    <?php } ?>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <?=$result->getPagination()->renderView('dashboard')?>
    <script type="text/javascript">
    $(function() {
        $('table[data-search-results=files]').concreteFileManagerTable({
            'folderID': '<?=$folderID?>'
        });
    });
    </script>

    <?php
} else {
    ?>
	<p>
        <?= t('You do not have access to the file manager.') ?>
    </p>
    <?php
}
