<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php if ($this->controller->getTask() == 'add'
    || $this->controller->getTask() == 'do_add'
    || $this->controller->getTask() == 'edit'
    || $this->controller->getTask() == 'update'
    || $this->controller->getTask() == 'delete') {
    ?>

    <?php
    if (is_object($type)) {
        $sizingHelpText = $sizingModeHelp[$type->getSizingMode()];
        $ftTypeName = $type->getName();
        $ftTypeSizingMode = $type->getSizingMode();
        $ftTypeHandle = $type->getHandle();
        $ftTypeWidth = $type->getWidth();
        $ftTypeHeight = $type->getHeight();
        $ftTypeIsRequired = $type->isRequired();
        $method = 'update';

        if (!$ftTypeIsRequired) {
            ?>

            <div class="ccm-dashboard-header-buttons">
                <form method="post" action="<?=$this->action('delete')?>">
                    <input type="hidden" name="ftTypeID" value="<?=$type->getID()?>" />
                    <?=Loader::helper('validation/token')->output('delete');
            ?>
                    <button type="button" class="btn btn-danger" data-action="delete-type"><?=t('Delete Type')?></button>
                </form>
            </div>

        <?php

        }
    } else {
        $method = 'do_add';
    }
    ?>

    <form method="post" action="<?=$view->action($method)?>" id="ccm-attribute-key-form">
        <?=Loader::helper('validation/token')->output($method);
    ?>
        <?php if (is_object($type)) {
    ?>
            <input type="hidden" name="ftTypeID" value="<?=$type->getID()?>" />
        <?php
}
    ?>
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
                    <span class="input-group-addon"><?php echo t('px'); ?></span>
                </div>
            </div>
            <div class="form-group">
                <?=$form->label('ftTypeHeight', t('Height'))?>
                <div class="input-group">
                    <?=$form->text('ftTypeHeight', $ftTypeHeight)?>
                    <span class="input-group-addon"><?php echo t('px'); ?></span>
                </div>
            </div>
            <div class="form-group">
                <?=$form->label('ftTypeSizingMode', t('Sizing Mode'))?>
                <?=$form->select('ftTypeSizingMode', $sizingModes, $ftTypeSizingMode)?>
                <p class="sizingmode-help help-block"><span><?=$sizingHelpText?></span></p>
            </div>
        </fieldset>
        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?=URL::page($c)?>" class="btn pull-left btn-default"><?=t('Back')?></a>
                <?php if (is_object($type)) {
    ?>
                    <button type="submit" class="btn btn-primary pull-right"><?=t('Save')?></button>
                <?php
} else {
    ?>
                    <button type="submit" class="btn btn-primary pull-right"><?=t('Add')?></button>
                <?php
}
    ?>
            </div>
        </div>
    </form>

    <script type="text/javascript">
        $(function() {
            var sizingModeHelp = <?php echo json_encode($sizingModeHelp)?>;
            $('button[data-action=delete-type]').on('click', function(e) {
                e.preventDefault();
                if (confirm('<?=t('Delete this thumbnail type?')?>')) {
                    $(this).closest('form').submit();
                }
            });

            $('#ftTypeSizingMode').on('change', function(e) {
                $('.sizingmode-help').find('span').html(sizingModeHelp[$(this).val()]);
            });

            $('#ftTypeSizingMode').trigger('change');
        })
    </script>

<?php
} else {
    ?>

    <div class="ccm-dashboard-header-buttons btn-group">
        <a href="<?php echo $view->action('options')?>" class="btn btn-default"><?php echo t('Options')?></a>
        <a href="<?php echo $view->action('add')?>" class="btn btn-primary"><?php echo t("Add Type")?></a>
    </div>

    <table class="table">
    <thead>
    <tr>
        <th><?=t('Handle')?></th>
        <th><?=t('Name')?></th>
        <th><?=t('Width')?></th>
        <th><?=t('Height')?></th>
        <th><?=t('Sizing')?></th>
        <th><?=t('Required')?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($types as $type) {
    ?>
    <tr>
        <td><a href="<?=$view->action('edit', $type->getID())?>"><?=$type->getHandle()?></a></td>
        <td><?=$type->getDisplayName()?></td>
        <td><?=$type->getWidth() ? $type->getWidth() : '<span class="text-muted">' . t('Automatic') . '</span>' ?></td>
        <td><?=$type->getHeight() ? $type->getHeight() : '<span class="text-muted">' . t('Automatic') . '</span>' ?></td>
        <td><?=$type->getSizingModeDisplayName()?></td>
        <td><?=$type->isRequired() ? t('Yes') : t('No')?></td>
    </tr>
    <?php
}
    ?>
    </tbody>
    </table>
<?php
} ?>
