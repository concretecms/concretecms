<?php defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<?php
$c = Page::getCurrentPage();
?>

<?php
$view->inc('view_header.php');
?>

<?php if (count($results)) {?>



    <div id="ccm-block-document-library-wrapper-<?=$bID?>">

    <table
        id="ccm-block-document-library-table-<?=$bID?>"
        class="table ccm-block-document-library-table <?php if ($tableStriped) { ?><?php } ?>">
    <thead>
    <tr>
        <?php foreach($tableColumns as $column) { ?>
            <th class="<?=$controller->getColumnClass($list, $column)?>">
                <?php if ($controller->isColumnSortable($column)) { ?>
                    <a href="<?=$controller->getSortAction($c, $list, $column)?>"><?=$controller->getColumnTitle($column)?></a>
                <?php } else { ?>
                    <span><?=$controller->getColumnTitle($column)?></span>
                <?php } ?>
            </th>
        <?php } ?>
    </tr>
    </thead>
    <tbody>
    <?php
    $rowClass = 'ccm-block-document-library-row-a';
    foreach($results as $f) { ?>
        <tr class="<?=$rowClass?>">
        <?php foreach($tableColumns as $column) { ?>
            <td><?=$controller->getColumnValue($column, $f)?></td>
        <?php } ?>
        </tr>
        <?php
        if (count($tableExpandableProperties)) {
            if ($f instanceof \Concrete\Core\Tree\Node\Type\File) {
                $fileID = $f->getTreeNodeFileID();
            } else {
                $fileID = $f->getTreeNodeID();
            }
            ?>
            <tr class="ccm-block-document-library-table-expanded-properties" data-document-library-details="<?=$fileID?>">
                <td colspan="<?=count($tableColumns)?>">
                    <?php foreach($tableExpandableProperties as $column) { ?>
                        <h4><?=$controller->getColumnTitle($column)?></h4>
                        <?=$controller->getColumnValue($column, $f)?>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    <?php
        $rowClass = ($rowClass == 'ccm-block-document-library-row-a') ? 'ccm-block-document-library-row-b' : 'ccm-block-document-library-row-a';
    } ?>
    </tbody>
    </table>
    </div>

    <?php if (isset($pagination)) { ?>
        <?=$pagination?>
    <?php } ?>

<?php } else { ?>
    <p><?=t('No files found.')?></p>
<?php } ?>

<style type="text/css">
<?php if ($headerBackgroundColor) { ?>
    #ccm-block-document-library-table-<?=$bID?> thead th {
        background-color: <?=$headerBackgroundColor?>;
    }
<?php } ?>
<?php if ($headerTextColor) { ?>
    #ccm-block-document-library-table-<?=$bID?> thead th,
    #ccm-block-document-library-table-<?=$bID?> thead th a {
        color: <?=$headerTextColor?>;
    }
    #ccm-block-document-library-table-<?=$bID?> thead th.ccm-block-document-library-active-sort-asc a:after {
        border-color: transparent transparent <?=$headerTextColor?> transparent;
    }
    #ccm-block-document-library-table-<?=$bID?> thead th.ccm-block-document-library-active-sort-desc a:after {
        border-color: <?=$headerTextColor?> transparent transparent transparent;
    }
<?php } ?>
<?php if ($headerBackgroundColorActiveSort) { ?>
    #ccm-block-document-library-table-<?=$bID?> thead th.ccm-block-document-library-active-sort-asc,
    #ccm-block-document-library-table-<?=$bID?> thead th.ccm-block-document-library-active-sort-desc {
        background-color: <?=$headerBackgroundColorActiveSort?>;
    }
<?php } ?>

<?php if ($rowBackgroundColorAlternate && $tableStriped) { ?>
    #ccm-block-document-library-table-<?=$bID?> > tbody > tr.ccm-block-document-library-row-b td {
        background-color: <?=$rowBackgroundColorAlternate?>;
    }
<?php } ?>

<?php if ($heightMode == 'fixed') { ?>
    #ccm-block-document-library-wrapper-<?=$bID?>  {
        height: <?=$fixedHeightSize?>px;
        overflow: scroll;
    }
<?php } ?>
</style>