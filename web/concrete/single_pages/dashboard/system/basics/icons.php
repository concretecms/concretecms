<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<form method="post" class="form-horizontal" id="favicon-form" action="<?=$view->action('update_favicon')?>" >
    <?=$this->controller->token->output('update_favicon')?>
    <fieldset>
        <legend><?=t('Favicon')?></legend>
            <div class="help-block"><?=t('Your image should be 16x16 pixels, and should be a gif or a png with a .ico file extension.')?></div>
            <?php
            $faviconFID = intval(Config::get('concrete.misc.favicon_fid'));
            $f = File::getByID($faviconFID);
            ?>
            <div class="form-group">
                <div class="col-md-6">
                    <?=$concrete_asset_library->file('ccm-favicon-file', 'faviconFID', t('Choose File'), $f);?>
                </div>
                <div class="col-md-6">
                    <button class="pull-right btn btn-default" type="submit" ><?=t('Save')?></button>
                </div>
            </div>
    </fieldset>
</form>

<div style="height: 100px"></div>

<form method="post" id="iphone-form" class="form-horizontal" action="<?=$view->action('update_iphone_thumbnail')?>" >
    <?=$this->controller->token->output('update_iphone_thumbnail')?>
    <fieldset>
        <legend><?=t('iPhone Thumbnail')?></legend>
        <div class="help-block"><?=t('iPhone home screen icons should be 57x57 and be in the .png format.')?></div>
        <?php
        $iosHomeFID=intval(Config::get('concrete.misc.iphone_home_screen_thumbnail_fid'));
        $f = File::getByID($iosHomeFID);
        ?>
        <div class="form-group">
            <div class="col-md-6">
                <?=$concrete_asset_library->file('ccm-iphone-file', 'iosHomeFID', t('Choose File'), $f);?>
            </div>
            <div class="col-md-6">
                <button class="pull-right btn btn-default" type="submit" ><?=t('Save')?></button>
            </div>
        </div>
    </fieldset>
</form>

<div style="height: 100px"></div>

<form method="post" id="modern-form" class="form-horizontal" action="<?php echo $view->action('update_modern_thumbnail'); ?>" >
    <?php echo $this->controller->token->output('update_modern_thumbnail'); ?>
    <input id="remove-existing-modern-thumbnail" name="remove_icon" type="hidden" value="0" />
    <fieldset>
        <legend><?php echo t('Windows 8 Thumbnail'); ?></legend>
        <div class="help-block"><?=t('Windows 8 start screen tiles should be 144x144 and be in the .png format.'); ?></div>
        <?php
        $modernThumbFID = intval(Config::get('concrete.misc.modern_tile_thumbnail_fid'));
        $f = File::getByID($modernThumbFID);
        $modernThumbBG = strval(Config::get('concrete.misc.modern_tile_thumbnail_bgcolor'));
        ?>
        <div class="form-group">
            <div class="col-md-6">
                <?=$concrete_asset_library->file('ccm-modern-file', 'modernThumbFID', t('Choose File'), $f);?>
            </div>
            <div class="col-md-4">
                <div class="controls">

                    <?php
                    $widget = Core::make('helper/form/color');
                    print $widget->output('modernThumbBG', $modernThumbBG);
                    ?>

                </div>
            </div>
            <div class="col-md-2">
                <button class="pull-right btn btn-default" type="submit" ><?=t('Save')?></button>
            </div>
        </div>
    </fieldset>
</form>
