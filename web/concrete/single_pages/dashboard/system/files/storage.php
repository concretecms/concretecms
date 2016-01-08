<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php if ($this->controller->getTask() == 'select_type'
    || $this->controller->getTask() == 'add'
    || $this->controller->getTask() == 'edit'
    || $this->controller->getTask() == 'update'
    || $this->controller->getTask() == 'delete') { ?>

    <?php
    if (is_object($location)) {
        $fslName = $location->getName();
        $fslIsDefault = $location->isDefault();
        $method = 'update';

        if (!$fslIsDefault && $type->getHandle() != 'default') { ?>

        <div class="ccm-dashboard-header-buttons">
            <form method="post" action="<?=$this->action('delete')?>">
                <input type="hidden" name="fslID" value="<?=$location->getID()?>" />
                <?=Loader::helper('validation/token')->output('delete');?>
                <button type="button" class="btn btn-danger" data-action="delete-location"><?=t('Delete Location')?></button>
            </form>
        </div>

        <?php
        }

    } else {
        $method = 'add';
    }
    ?>
    <form method="post" action="<?=$view->action($method)?>" id="ccm-attribute-key-form">
        <?=Loader::helper('validation/token')->output($method);?>
        <input type="hidden" name="fslTypeID" value="<?=$type->getID()?>" />
        <?php if (is_object($location)) { ?>
            <input type="hidden" name="fslID" value="<?=$location->getID()?>" />
        <?php } ?>
        <fieldset>
            <legend><?=t('Basics')?></legend>
            <div class="form-group">
                <?=$form->label('fslName', t('Name'))?>
                <div class="input-group">
                    <?=$form->text('fslName', $fslName)?>
                    <span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
                </div>
            </div>
            <?php if ($fslIsDefault) {
                $args = array('disabled' => 'disabled');
            } else {
                $args = array();
            }
            ?>
            <div class="form-group">
                <label><?=t('Default')?>
                <div class="radio">
                    <label><?=$form->radio('fslIsDefault', 1, $fslIsDefault, $args)?>
                        <?=t('Yes, make this the default storage location for new files.')?>
                    </label>
                </div>
                <div class="radio">
                    <label><?=$form->radio('fslIsDefault', 0, $fslIsDefault, $args)?>
                        <?=t('No, this is not the default storage location.')?>
                    </label>
                </div>
            </div>

        </fieldset>
        <?php if ($type->hasOptionsForm()) {
        ?>
        <fieldset>
            <legend><?=t('Options %s Storage Type', $type->getName())?></legend>
            <?php $type->includeOptionsForm($location);?>
        </fieldset>
        <?php } ?>
        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?=URL::page($c)?>" class="btn pull-left btn-default"><?=t('Back')?></a>
                <?php if (is_object($location)) { ?>
                    <button type="submit" class="btn btn-primary pull-right"><?=t('Save')?></button>
                <?php } else { ?>
                    <button type="submit" class="btn btn-primary pull-right"><?=t('Add')?></button>
                <?php } ?>
            </div>
        </div>
    </form>

    <script type="text/javascript">
    $(function() {
        $('button[data-action=delete-location]').on('click', function(e) {
            e.preventDefault();
            if (confirm('<?=t('Delete this storage location? All files using it will have their storage location reset to the default.')?>')) {
                $(this).closest('form').submit();
            }
        });
    })
    </script>
<?php } else { ?>

    <h3><?=t('Storage Locations')?></h3>
    <ul class="item-select-list">
    <?php foreach($locations as $location) { ?>
        <li><a href="<?=$this->action('edit', $location->getID())?>"><i class="fa fa-hdd-o"></i> <?=$location->getDisplayName()?></a></li>
    <?php } ?>
    </ul>

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

<?php } ?>