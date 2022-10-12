<?php defined('C5_EXECUTE') or die('Access Denied.');

$c = Page::getCurrentPage();
$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();

if ($tableName) { ?>
    <<?php echo $titleFormat; ?>><?=$tableName?></<?php echo $titleFormat; ?>>
<?php } ?>
<?php if ($tableDescription) { ?>
    <p><?=$tableDescription?></p>
<?php }

if (isset($entity)) { ?>
    <?php if ($enableSearch) { ?>
        <form method="get" action="<?=$c->getCollectionLink()?>">

            <?php if ($enableKeywordSearch) { ?>
            <div class="row g-3 align-items-center mb-4">
                <div class="col-md-6">
                    <?=$form->text('keywords', ['class' => 'form-control col-4', 'placeholder' => t('Search')])?>
                </div>
                <div class="col-md-6">
                    <?php if (count($tableSearchProperties) || count($tableSearchAssociations)) { ?>
                        <a href="#" data-express-entry-list-advanced-search="<?=$bID?>"
                           class="ccm-block-express-entry-list-advanced-search"><?=t('Advanced Search')?></a>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>

            <?php if (count($tableSearchProperties) || count($tableSearchAssociations)) { ?>
                <div data-express-entry-list-advanced-search-fields="<?=$bID?>" class="ccm-block-express-entry-list-advanced-search-fields">
                    <input type="hidden" name="advancedSearchDisplayed" value="<?php echo $app->request->request('advancedSearchDisplayed') || !$enableKeywordSearch ? 1 : ''; ?>">
                    <?php foreach ($tableSearchProperties as $ak) { ?>
                        <h4><?=$ak->getAttributeKeyDisplayName()?></h4>
                        <?=$ak->render(new \Concrete\Core\Attribute\Context\BasicSearchContext(), null, true)?>
                    <?php } ?>
                    <?php foreach ($tableSearchAssociations as $association) { ?>
                        <h5><?= $association->getTargetEntity()->getEntityDisplayName() ?></h5>
                        <?php
                        $field = new \Concrete\Core\Express\Search\Field\AssociationField($association);
                        $field->loadDataFromRequest($controller->getRequest()->query->all());
                        echo $field->renderSearchField();
                        ?>
                    <?php } ?>
                </div>
            <?php } ?>

            <div class="mb-3 clearfix">
                <button type="submit" class="btn btn-primary float-end" name="search"><?=t('Search')?></button>
            </div>
        </form>
    <?php }

    try {
        $results = $result->getItemListObject()->getResults();
    } catch (\Exception $e) {
        $results = [];
    }
    if (count($results)) { ?>

        <?php if (isset($enableItemsPerPageSelection) && $enableItemsPerPageSelection) { ?>
            <div class="mt-3 mb-3">
                <div class="row row-cols-auto align-items-center">
                    <div class="col-auto">
                        <b><?=t('Items Per Page')?></b>
                    </div>
                    <div class="col-auto">
                        <select class="ms-3 form-select" data-express-entry-list-select-items-per-page="<?=$bID?>">
                            <?php foreach($itemsPerPageOptions as $itemsPerPage) {
                                $url = \League\Url\Url::createFromServer($_SERVER);
                                $query = $url->getQuery();
                                $query->modify(['itemsPerPage' => $itemsPerPage]);
                                $url->setQuery($query);
                                $itemsPerPageOptionUrl = (string) $url;
                                ?>
                                <option data-location="<?=$itemsPerPageOptionUrl?>" <?php if ($itemsPerPage == $itemsPerPageSelected) { ?>selected<?php } ?>><?=$itemsPerPage?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
        <?php } ?>

        <table id="ccm-block-express-entry-list-table-<?=$bID?>"
        class="table ccm-block-express-entry-list-table <?php if ($tableStriped) { ?><?php } ?>">
            <thead>
                <tr>
                <?php foreach ($result->getColumns() as $column) { ?>
                    <th class="<?=$column->getColumnStyleClass()?>">
                        <a href="<?=h($column->getColumnSortURL())?>"><?=$column->getColumnTitle()?></a>
                    </th>
                <?php } ?>
                </tr>
            </thead>
            <tbody>
            <?php
            $rowClass = 'ccm-block-express-entry-list-row-a';
            foreach ($result->getItems() as $item) { ?>
                <tr class="<?=$rowClass?>">
                <?php foreach ($item->getColumns() as $column) {
                    if ($controller->linkThisColumn($column)) { ?>
                        <td><a href="<?=URL::to($detailPage, 'view_express_entity', $item->getEntry()->getId())?>"><?=$column->getColumnValue($item);?></a></td>
                    <?php
                    } else { ?>
                        <td><?=$column->getColumnValue($item);?></td>
                    <?php
                    } ?>
                <?php
                }
                $rowClass = ($rowClass == 'ccm-block-express-entry-list-row-a') ? 'ccm-block-express-entry-list-row-b' : 'ccm-block-express-entry-list-row-a';
                ?>
                </tr>
            <?php } ?>
            </tbody>
        </table>

        <?php if (isset($enablePagination) && $enablePagination && $pagination) { ?>
            <?=$pagination ?>
        <?php } ?>

        <style>
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
    <?php } else { ?>
        <p><?=t('No "%s" entries can be found', $entity->getEntityDisplayName())?></p>
    <?php } ?>

    <script>
        $(function() {
            $.concreteExpressEntryList({
                'bID': '<?=$bID?>',
                'hideFields': <?php echo $app->request->request('advancedSearchDisplayed') || !$enableKeywordSearch ? 'false' : 'true'; ?>
            });
        });
    </script>
<?php } ?>
