<?php

use Concrete\Core\View\View;

defined('C5_EXECUTE') or die('Access Denied.');

$fp = FilePermissions::getGlobal();

if ($fp->canAddFile() || $fp->canSearchFiles()) { ?>

    <div class="table-responsive">
        <table class="ccm-search-results-table" data-search-results="files">
            <thead>
            <tr>
                <th colspan="2">
                    <span style="white-space: nowrap">
                        <input type="checkbox" data-search-checkbox="select-all" />
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"></a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#">Dropdown</a>
                        </div>
                    </span>
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
            foreach ($result->getItems() as $item) { ?>
                <tr>
                    <td class="ccm-search-results-checkbox">
                        <input data-search-checkbox="individual" type="checkbox"
                               data-node-id="<?=$item->getItem()->getTreeNodeID()?>" />
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
