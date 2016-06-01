<?php
defined('C5_EXECUTE') or die("Access Denied.");
$valt = Loader::helper('validation/token');
$th = Loader::helper('text');
?>

<?php if ($controller->getTask() == 'inspect') {
    ?>

    <div class="ccm-ui">
    <div class="alert alert-info"><?=$query?></div>
    <table class="table">
    <?php foreach ($parameters as $params) {
    ?>
        <tr>
            <td><?=$params?></td>
        </tr>
    <?php 
}
    ?>
    </table>
    </div>

<?php 
} else {
    ?>

    <?php if (count($entries)) {
    ?>

        <p class="lead"><?=t('Total Logged: %s', $total)?></p>

    <div class="ccm-dashboard-content-full">

        <div data-search-element="results">
            <div class="table-responsive">
                <table class="ccm-search-results-table">
                    <thead>
                        <tr>
                            <th class="<?=$list->getSortClassName('queryTotal')?>" style="white-space: nowrap"><a href="<?=$list->getSortURL('queryTotal', 'desc')?>"><?=t('Times Run')?></a></th>
                            <th class="<?=$list->getSortClassName('query')?>"><a href="<?=$list->getSortURL('query', 'asc')?>"><?=t('Query')?></a></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($entries as $ent) {
    ?>
                        <tr>
                            <td valign="top"><?=$ent['queryTotal']?></td>
                            <td valign="top"><?=$ent['query']?></td>
                            <td><a href="<?=$view->action('inspect', rawurlencode($ent['query']))?>" dialog-width="600" dialog-title="<?=t('Query Details')?>" dialog-modal="true" dialog-height="400" class="dialog-launch icon-link"><i class="fa fa-search"></i></a></td>
                        </tr>
                        <?php 
}
    ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="ccm-search-results-pagination">
            <?php echo $pagination->renderView('dashboard');
    ?>
        </div>

    </div>

    <div class="ccm-dashboard-header-buttons">
        <form method="post" action="<?=$view->action('clear')?>" class='form-inline' style='display:inline'>
            <?=Loader::helper('validation/token')->output('clear')?>
            <button type="submit" class="btn btn-danger"><?=t('Clear Log')?></button>
        </form>

        <form method="post" action="<?=$view->action('csv')?>" class='form-inline' style='display:inline'>
            <?=Loader::helper('validation/token')->output('csv')?>
            <button type="submit" class="btn btn-success"><?= t('Export to CSV') ?></button>
        </form>

    </div>

    <?php 
} else {
    ?>

    <p><?=t("The database query log is empty.")?></p>

    <?php 
}
    ?>


<?php 
} ?>