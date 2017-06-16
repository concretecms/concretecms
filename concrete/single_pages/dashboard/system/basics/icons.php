<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<form method="post" class="form-horizontal" id="favicon-form" action="<?php echo $view->action('update_icons')?>" >
    <?php echo $this->controller->token->output('update_icons')?>
    <fieldset>
        <legend><?php echo t('Icon')?></legend>
        <div class="help-block"><?php echo t('The master icon should be %1$dx%2$d and be in the %3$s format.  All icons (Favicon, iOS, Android &amp; Metro) will be automatically generated from the master icon.', 512, 512, '.png')?></div>
        <?php
            $appIconFID = intval($config->get('misc.app_icon_fid'));
            $f = File::getByID($appIconFID);
        ?>
        <div class="form-group">
            <?php echo $concrete_asset_library->file('ccm-appicon-file', 'appIconFID', t('Choose File'), $f);?>
        </div>
    </fieldset>
    <fieldset>
        <legend><?php echo t('Additional Options'); ?></legend>
        <div class="help-block"><?php echo t('For Windows metro tiles you can define also a background color.'); ?></div>
        <div class="form-group">
            <label class="control-label"><?php echo t('Background Color')?></label>
            <div>
                <?php
                    $widget = Core::make('helper/form/color');
                    $modernThumbBG = $config->get('misc.modern_tile_thumbnail_bgcolor');
                    echo $widget->output('modernThumbBG', $modernThumbBG);
                ?>
            </div>
        </div>

    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="pull-right btn btn-primary" type="submit" ><?php echo t('Save')?></button>
        </div>
    </div>

</form>
