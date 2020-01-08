<?php

defined('C5_EXECUTE') or die("Access Denied.");

?>

<div class="row">
    <div class="col-4">
        <?php
        $element = Element::get('dashboard/boards/menu', ['board' => $board, 'action' => 'pool']);
        $element->render();
        ?>
    </div>
    <div class="col-8">

        <h3><?=t('Data Source Objects')?></h3>
        <p><?=t('Total data stored in your data pool.')?></p>
        
        <table class="table table-striped">
        <thead>
            <tr>
                <th></th>
                <th class="w-100"><?=t('Data Source')?></th>
                <th class="text-center"><?=t('#')?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($configuredSources as $configuredSource) {
                $source = $configuredSource->getDataSource();
                $driver = $source->getDriver();
                $formatter = $driver->getIconFormatter();
                ?>
                <tr>
                    <td><?=$formatter->getListIconElement()?></td>
                    <td><?=$configuredSource->getName()?></td>
                    <td class="text-center"><span class="badge badge-info"><?=$configuredSource->getItemCount()?></span></td>
                </tr>
            <?php } ?>
        </tbody>
        </table>
        
        <hr>
        
        <form method="post" action="<?=$view->action('refresh_pool', $board->getBoardID())?>">
            <?=$token->output('refresh_pool')?>
            <h3><?=t("Refresh Pool")?></h3>
            
            <p><?=t("Removes all contents from this data pool and resets/refreshes them directly from the currently configured data sources.")?></p>
            
            <button type="submit" class="btn btn-lg btn-primary"><?=t("Refresh Pool")?></button>
        </form>
        
    </div>
</div>
