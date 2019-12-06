<?php

defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @var $dataSource \Concrete\Core\Entity\Board\DataSource;
 */
$driver = $dataSource->getDriver();
?>

<div class="row">
    <div class="col-4">
        <?php
        $element = Element::get('dashboard/boards/menu', ['board' => $board, 'action' => 'data_sources']);
        $element->render();
        ?>
    </div>
    <div class="col-8">

        <h3 class="font-weight-light"><?=t('Add Data Source')?></h3>
        <form method="post" action="<?=$view->action('add_data_source', $board->getBoardID(), $dataSource->getID())?>">
            <?=$token->output('add_data_source')?>

            <div class="form-group">
                <?=$form->label('dataSourceName', t('Data Source Name'))?>
                <?=$form->text('dataSourceName')?>
            </div>

            <?php
            $element = $driver->getConfigurationFormElement();
            $element->render();
            ?>
            <div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions ">
                    <a href="<?=$view->url('/dashboard/boards/data_sources', $board->getBoardID())?>" class="btn btn-secondary float-left"><?=t("Cancel")?></a>
                    <button type="submit" class="btn btn-primary float-right"><?=t('Add Data Source')?></button>
                </div>
            </div>
        </form>
    </div>
</div>
