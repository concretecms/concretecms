<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<? if ($this->controller->getTask() == 'select_type' || $this->controller->getTask() == 'add' || $this->controller->getTask() == 'edit') { ?>

    <? if (isset($type)) { ?>
        <form method="post" action="<?=$view->action('add')?>" id="ccm-attribute-key-form">

            <div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions">
                    <a href="<?=URL::page($c)?>" class="btn pull-left btn-default"><?=t('Back')?></a>
                    <? if (is_object($location)) { ?>
                        <button type="submit" class="btn btn-primary pull-right"><?=t('Save')?></button>
                    <? } else { ?>
                        <button type="submit" class="btn btn-primary pull-right"><?=t('Add')?></button>
                    <? } ?>
                </div>
            </div>
        </form>
    <? } ?>

<? } else { ?>

    <form method="get" action="<?=$view->action('select_type')?>" id="ccm-file-storage-location-type-form">
        <fieldset>

            <legend><?=t('Add Location')?></legend>
            <label for="atID"><?=t('Choose Type')?></label>
            <div class="form-inline">
                <div class="form-group">
                    <?=$form->select('fslTypeID', $types)?>
                </div>
                <button type="submit" class="btn btn-default"><?=t('Go')?></button>
            </div>
        </fieldset>
    </form>

<? } ?>