<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<? if ($this->controller->getTask() == 'select_type' || $this->controller->getTask() == 'add' || $this->controller->getTask() == 'edit') { ?>

    <?
    if (is_object($location)) {
        $fslName = $location->getName();
        $fslIsDefault = $location->isDefault();
    }
    $method = is_object($location) ? 'update' : 'add';
    ?>
    <form method="post" action="<?=$view->action($method)?>" id="ccm-attribute-key-form">
        <?=Loader::helper('validation/token')->output($method);?>
        <input type="hidden" name="fslTypeID" value="<?=$type->getID()?>" />
        <fieldset>
            <legend><?=t('Basics')?></legend>
            <div class="form-group">
                <?=$form->label('fslName', t('Name'))?>
                <div class="input-group">
                    <?=$form->text('fslName', $fslName)?>
                    <span class="input-group-addon"><i class="glyphicon glyphicon-asterisk"></i></span>
                </div>
            </div>
            <div class="form-group">
                <label><?=t('Default')?>
                <div class="radio">
                    <label><?=$form->radio('fslIsDefault', 1, $fslIsDefault)?>
                        <?=t('Yes, make this the default storage location for new files.')?>
                    </label>
                </div>
                <div class="radio">
                    <label><?=$form->radio('fslIsDefault', 0, $fslIsDefault)?>
                        <?=t('No, this is not the default storage location.')?>
                    </label>
                </div>
            </div>

        </fieldset>
        <? if ($type->hasOptionsForm()) {
        ?>
        <fieldset>
            <legend><?=t('Options %s Storage Type', $type->getName())?></legend>
            <? $type->includeOptionsForm($location);?>
        </fieldset>
        <? } ?>
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

<? } else { ?>

    <h3><?=t('Storage Locations')?></h3>
    <ul class="item-select-list">
    <? foreach($locations as $location) { ?>
        <li><i class=""></i><a href="<?=$this->action('edit', $location->getID())?>"><?=$location->getName()?></a></li>
    <? } ?>
    </ul>

    <br/><br/>
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