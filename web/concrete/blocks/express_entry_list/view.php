<?php defined('C5_EXECUTE') or die(_("Access Denied."));

if ($tableName) { ?>

    <h2><?=$tableName?></h2>

<?php }

if ($entity) {

    $results = $result->getItemListObject()->getResults();

    if (count($results)) { ?>


        <table class="table">
            <thead>
            <tr>
            <?php foreach($result->getColumns() as $column) {
                ?>
                <th class="<?=$column->getColumnStyleClass()?>"><a href="<?=$column->getColumnSortURL()?>"><?=$column->getColumnTitle()?></a></th>
            <?php } ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach($result->getItems() as $item) { ?>
                <tr>
                <?php foreach($item->getColumns() as $column) { ?>
                    <td><?=$column->getColumnValue($item);?></td>
                <?php } ?>
                </tr>
            <?php } ?>
            </tbody>
        </table>

        <?php if ($pagination) { ?>
            <?=$pagination ?>
        <?php } ?>


    <?php } else { ?>

        <p><?=t('No "%s" entries can be found', $entity->getName())?>

    <?php } ?>


<?php } ?>