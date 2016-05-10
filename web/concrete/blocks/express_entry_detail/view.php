<?php defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<?
$c = Page::getCurrentPage();
?>

<? if ($success) { ?>
    <div class="alert alert-success"><?=$success?></div>
<? } ?>

<? if ($tableName) { ?>
    <h2><?=$tableName?></h2>
<? } ?>

<? if ($tableDescription) {  ?>
    <p><?=$tableDescription?></p>
<? } ?>


<? if ($enableSearch) { ?>
    <form method="get" action="<?=$c->getCollectionLink()?>">
        <div class="form-inline">
            <div class="form-group">
                <?=$form->label('keywords', t('Keyword Search'))?>
                <?=$form->text('keywords')?>
            </div>
            <button type="submit" class="btn btn-primary" name="search"><?=t('Search')?></button>
            <? if (count($tableSearchProperties)) { ?>
                <a href="#" data-document-library-advanced-search="<?=$bID?>"
                   class="ccm-block-document-library-advanced-search"><?=t('Advanced Search')?></a>
            <? } ?>
            <? if ($canAddFiles) { ?>
            <a href="#" data-document-library-add-files="<?=$bID?>"
               class="ccm-block-document-library-add-files"><?=t('Add Files')?></a>
            <? } ?>
        </div>

        <? if (count($tableSearchProperties)) { ?>
            <div data-document-library-advanced-search-fields="<?=$bID?>"
                 class="ccm-block-document-library-advanced-search-fields">
                <input type="hidden" name="advancedSearchDisplayed" value="">
                <? foreach($tableSearchProperties as $column) { ?>
                    <h4><?=$controller->getColumnTitle($column)?></h4>
                    <div><?=$controller->getSearchValue($column)?></div>
                <? } ?>
            </div>
        <? } ?>
        <br/>
    </form>
<? } else if ($canAddFiles) { ?>
    <div>
        <a href="#" data-document-library-add-files="<?=$bID?>"
           class="ccm-block-document-library-add-files"><?=t('Add Files')?></a>
    </div>
<br/>
<? } ?>

<? if ($canAddFiles) { ?>
    <div data-document-library-upload-action="<?=$view->action('upload')?>" data-document-library-add-files="<?=$bID?>" class="ccm-block-document-library-add-files-uploader">
        <div class="ccm-block-document-library-add-files-pending"><?=t('Upload Files')?></div>
        <div class="ccm-block-document-library-add-files-uploading"><?=t('Uploading')?> <i class="fa fa-spin fa-spinner"></i></div>
        <input type="file" name="file" />
        <?=Core::make('token')->output()?>
    </div>
<? } ?>

<? if (count($results)) { ?>



    <div id="ccm-block-document-library-wrapper-<?=$bID?>">

    <table
        id="ccm-block-document-library-table-<?=$bID?>"
        class="table ccm-block-document-library-table <? if ($tableStriped) { ?><? } ?>">
    <thead>
    <tr>
        <? foreach($tableColumns as $column) { ?>
            <th class="<?=$controller->getColumnClass($list, $column)?>">
                <? if ($controller->isColumnSortable($column)) { ?>
                    <a href="<?=$controller->getSortAction($c, $list, $column)?>"><?=$controller->getColumnTitle($column)?></a>
                <? } else { ?>
                    <span><?=$controller->getColumnTitle($column)?></span>
                <? } ?>
            </th>
        <? } ?>
    </tr>
    </thead>
    <tbody>
    <?
    $rowClass = 'ccm-block-document-library-row-a';
    foreach($results as $f) { ?>
        <tr class="<?=$rowClass?>">
        <? foreach($tableColumns as $column) { ?>
            <td><?=$controller->getColumnValue($column, $f)?></td>
        <? } ?>
        </tr>
        <?
        if (count($tableExpandableProperties)) { ?>
            <tr class="ccm-block-document-library-table-expanded-properties" data-document-library-details="<?=$f->getFileID()?>">
                <td colspan="<?=count($tableColumns)?>">
                    <? foreach($tableExpandableProperties as $column) { ?>
                        <h4><?=$controller->getColumnTitle($column)?></h4>
                        <?=$controller->getColumnValue($column, $f)?>
                    <? } ?>
                </td>
            </tr>
        <? } ?>
    <?
        $rowClass = ($rowClass == 'ccm-block-document-library-row-a') ? 'ccm-block-document-library-row-b' : 'ccm-block-document-library-row-a';
    } ?>
    </tbody>
    </table>
    </div>

    <? if (isset($pagination)) { ?>
        <?=$pagination?>
    <? } ?>

<? } else { ?>
    <p><?=t('No files found.')?></p>
<? } ?>

<style type="text/css">
<? if ($headerBackgroundColor) { ?>
    #ccm-block-document-library-table-<?=$bID?> thead th {
        background-color: <?=$headerBackgroundColor?>;
    }
<? } ?>
<? if ($headerTextColor) { ?>
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
<? } ?>
<? if ($headerBackgroundColorActiveSort) { ?>
    #ccm-block-document-library-table-<?=$bID?> thead th.ccm-block-document-library-active-sort-asc,
    #ccm-block-document-library-table-<?=$bID?> thead th.ccm-block-document-library-active-sort-desc {
        background-color: <?=$headerBackgroundColorActiveSort?>;
    }
<? } ?>

<? if ($rowBackgroundColorAlternate && $tableStriped) { ?>
    #ccm-block-document-library-table-<?=$bID?> > tbody > tr.ccm-block-document-library-row-b td {
        background-color: <?=$rowBackgroundColorAlternate?>;
    }
<? } ?>

<? if ($heightMode == 'fixed') { ?>
    #ccm-block-document-library-wrapper-<?=$bID?>  {
        height: <?=$fixedHeightSize?>px;
        overflow: scroll;
    }
<? } ?>
</style>

<script type="text/javascript">
$(function() {
    $.concreteDocumentLibrary({
        'bID': '<?=$bID?>',
        'allowFileUploading': <? if ($allowFileUploading) { ?>true<? } else { ?>false<? } ?>,
        'allowInPageFileManagement': <? if ($allowInPageFileManagement) { ?>true<? } else { ?>false<? } ?>
    });
});
</script>