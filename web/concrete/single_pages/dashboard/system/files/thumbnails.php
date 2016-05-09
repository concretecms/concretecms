<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<? if ($this->controller->getTask() == 'add'
    || $this->controller->getTask() == 'do_add'
    || $this->controller->getTask() == 'edit'
    || $this->controller->getTask() == 'update'
    || $this->controller->getTask() == 'delete') { ?>

    <?
    if (is_object($type)) {
        $ftTypeName = $type->getName();
        $ftTypeHandle = $type->getHandle();
        $ftTypeWidth = $type->getWidth();
        $ftTypeHeight = $type->getHeight();
        $ftTypeIsRequired = $type->isRequired();
        if ($type->isConstrained()) $ftTypeIsConstrained = 1;
        $method = 'update';

        if (!$ftTypeIsRequired) { ?>

            <div class="ccm-dashboard-header-buttons">
                <form method="post" action="<?=$this->action('delete')?>">
                    <input type="hidden" name="ftTypeID" value="<?=$type->getID()?>" />
                    <?=Loader::helper('validation/token')->output('delete');?>
                    <button type="button" class="btn btn-danger" data-action="delete-type"><?=t('Delete Type')?></button>
                </form>
            </div>

        <?
        }

    } else {
        $method = 'do_add';
    }
    ?>

    <form method="post" action="<?=$view->action($method)?>" id="ccm-attribute-key-form">
        <?=Loader::helper('validation/token')->output($method);?>
        <? if (is_object($type)) { ?>
            <input type="hidden" name="ftTypeID" value="<?=$type->getID()?>" />
        <? } ?>
        <fieldset>
            <div class="form-group">
                <?=$form->label('ftTypeHandle', t('Handle'))?>
                <div class="input-group">
                    <?=$form->text('ftTypeHandle', $ftTypeHandle)?>
                    <span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
                </div>
            </div>
            <div class="form-group">
                <?=$form->label('ftTypeName', t('Name'))?>
                <div class="input-group">
                    <?=$form->text('ftTypeName', $ftTypeName)?>
                    <span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
                </div>
            </div>
            <div class="form-group">
                <?=$form->label('ftTypeWidth', t('Width'))?>
                <div class="input-group">
                    <?=$form->text('ftTypeWidth', $ftTypeWidth)?>
                    <span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
                </div>
            </div>
            <div class="form-group">
                <?=$form->label('ftTypeHeight', t('Height'))?>
                <?=$form->text('ftTypeHeight', $ftTypeHeight)?>
            </div>
            <div class="form-group">
                <div><?=$form->label('ftTypeIsConstrained', t('Crop?'))?></div>
                <?=$form->checkbox('ftTypeIsConstrained', 1, $ftTypeIsConstrained)?> <?=t('No, keep images\' contrained ratio')?>
            </div>
            <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <?=t('If you leave the height field empty, the value of width becomes the max width and/or height and constrain the image ratio. If you set both width and height, concrete5 will try to crop the thumbnail to indicated size unless you check "No" to the crop option.')?></div>
        </fieldset>
        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?=URL::page($c)?>" class="btn pull-left btn-default"><?=t('Back')?></a>
                <? if (is_object($type)) { ?>
                    <button type="submit" class="btn btn-primary pull-right"><?=t('Save')?></button>
                <? } else { ?>
                    <button type="submit" class="btn btn-primary pull-right"><?=t('Add')?></button>
                <? } ?>
            </div>
        </div>
    </form>

    <script type="text/javascript">
        $(function() {
            $('button[data-action=delete-type]').on('click', function(e) {
                e.preventDefault();
                if (confirm('<?=t('Delete this thumbnail type?')?>')) {
                    $(this).closest('form').submit();
                }
            });
        })
    </script>

<? } else { ?>

    <div class="ccm-dashboard-header-buttons">
        <a href="<?php echo $view->action('add')?>" class="btn btn-primary"><?php echo t("Add Type")?></a>
    </div>

    <table class="table">
    <thead>
    <tr>
        <th><?=t('Handle')?></th>
        <th><?=t('Name')?></th>
        <th><?=t('Width')?></th>
        <th><?=t('Height')?></th>
        <th><?=t('Required')?></th>
        <th><?=t('Contrain')?></th>
    </tr>
    </thead>
    <tbody>
    <? foreach($types as $type) { ?>
    <tr>
        <td><a href="<?=$view->action('edit', $type->getID())?>"><?=$type->getHandle()?></a></td>
        <td><?=$type->getDisplayName()?></td>
        <td><?=$type->getWidth()?></td>
        <td><?=($type->getHeight()) ? $type->getHeight() : '<span class="text-muted">' . t('Automatic') . '</span>' ?></td>
        <td><?=($type->isRequired()) ? t('Yes') : t('No')?></td>
        <td><?=($type->isConstrained()) ? t('Yes') : t('No')?></td>
    </tr>
    <? } ?>
    </tbody>
    </table>
<? } ?>