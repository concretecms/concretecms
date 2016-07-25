<?php defined('C5_EXECUTE') or die(_("Access Denied."));

$c = Page::getCurrentPage();
if ($tableName) { ?>

    <h2><?=$tableName?></h2>

    <?php if ($tableDescription) {  ?>
        <p><?=$tableDescription?></p>
    <?php } ?>

<?php }

if ($entity) { ?>

    <?php if ($enableSearch) { ?>
        <form method="get" action="<?=$c->getCollectionLink()?>">
            <div class="form-inline">
                <div class="form-group">
                    <?=$form->label('keywords', t('Keyword Search'))?>
                    <?=$form->text('keywords')?>
                </div>
                <button type="submit" class="btn btn-primary" name="search"><?=t('Search')?></button>
                <?php if (count($tableSearchProperties)) { ?>
                    <a href="#" data-express-entry-list-advanced-search="<?=$bID?>"
                       class="ccm-block-express-entry-list-advanced-search"><?=t('Advanced Search')?></a>
                <?php } ?>
            </div>

            <?php if (count($tableSearchProperties)) { ?>
                <div data-express-entry-list-advanced-search-fields="<?=$bID?>"
                     class="ccm-block-express-entry-list-advanced-search-fields">
                    <input type="hidden" name="advancedSearchDisplayed" value="">
                    <?php foreach($tableSearchProperties as $ak) { ?>
                        <h4><?=$ak->getAttributeKeyDisplayName()?></h4>
                        <div>
                        <?=$ak->render('search', null, true)?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
            <br/>
        </form>
    <?php }

    $results = $result->getItemListObject()->getResults();

    if (count($results)) { ?>


        <table
            id="ccm-block-express-entry-list-table-<?=$bID?>"
            class="table ccm-block-express-entry-list-table <?php if ($tableStriped) { ?><?php } ?>">
            <thead>
            <tr>
            <?php foreach($result->getColumns() as $column) {
                ?>
                <th class="<?=$column->getColumnStyleClass()?>"><a href="<?=$column->getColumnSortURL()?>"><?=$column->getColumnTitle()?></a></th>
            <?php } ?>
            </tr>
            </thead>
            <tbody>
            <?php
            $rowClass = 'ccm-block-express-entry-list-row-a';

            foreach($result->getItems() as $item) { ?>
                <tr class="<?=$rowClass?>">
                <?php foreach($item->getColumns() as $column) {
                    if ($controller->linkThisColumn($column)) { ?>
                        <td><a href="<?=URL::to($detailPage, 'view_express_entity', $item->getEntry()->getId())?>"><?=$column->getColumnValue($item);?></a></td>
                    <?php } else { ?>
                        <td><?=$column->getColumnValue($item);?></td>
                    <?php }  ?>
                <?php

                }
                $rowClass = ($rowClass == 'ccm-block-express-entry-list-row-a') ? 'ccm-block-express-entry-list-row-b' : 'ccm-block-express-entry-list-row-a';

                ?>
                </tr>
            <?php } ?>
            </tbody>
        </table>

        <?php if ($pagination) { ?>
            <?=$pagination ?>
        <?php } ?>


        <style type="text/css">
            <?php if ($headerBackgroundColor) { ?>
            #ccm-block-express-entry-list-table-<?=$bID?> thead th {
                background-color: <?=$headerBackgroundColor?>;
            }
            <?php } ?>
            <?php if ($headerTextColor) { ?>
            #ccm-block-express-entry-list-table-<?=$bID?> thead th,
            #ccm-block-express-entry-list-table-<?=$bID?> thead th a {
                color: <?=$headerTextColor?>;
            }
            #ccm-block-express-entry-list-table-<?=$bID?> thead th.ccm-results-list-active-sort-asc a:after {
                border-color: transparent transparent <?=$headerTextColor?> transparent;
            }
            #ccm-block-express-entry-list-table-<?=$bID?> thead th.ccm-results-list-active-sort-desc a:after {
                border-color: <?=$headerTextColor?> transparent transparent transparent;
            }
            <?php } ?>
            <?php if ($headerBackgroundColorActiveSort) { ?>
            #ccm-block-express-entry-list-table-<?=$bID?> thead th.ccm-results-list-active-sort-asc,
            #ccm-block-express-entry-list-table-<?=$bID?> thead th.ccm-results-list-active-sort-desc {
                background-color: <?=$headerBackgroundColorActiveSort?>;
            }
            <?php } ?>

            <?php if ($rowBackgroundColorAlternate && $tableStriped) { ?>
            #ccm-block-express-entry-list-table-<?=$bID?> > tbody > tr.ccm-block-express-entry-list-row-b td {
                background-color: <?=$rowBackgroundColorAlternate?>;
            }
            <?php } ?>

        </style>

    <script type="text/javascript">
        $(function() {
            $.concreteExpressEntryList({
                'bID': '<?=$bID?>'
            });
        });
    </script>

    <?php } else { ?>

        <p><?=t('No "%s" entries can be found', $entity->getName())?>

    <?php } ?>


<?php } ?>