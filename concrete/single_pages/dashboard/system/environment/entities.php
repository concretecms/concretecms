<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>


<form method="post" id="entities-settings-form" action="<?= $view->action('update_entity_settings') ?>" style="position: relative">
    <?= $this->controller->token->output('update_entity_settings') ?>

    <fieldset>
        <legend><?= t('Settings') ?></legend>

        <div class="form-group">

        <label class="launch-tooltip" data-placement="right" title="<?= t('Defines whether the Doctrine proxy classes are created on the fly. On the fly generation is active when development mode is enabled.') ?>"><?= t('Doctrine Development Mode') ?></label>

        <div class="radio">
            <label>
                <input type="radio" name="DOCTRINE_DEV_MODE" value="1" <?php if (Config::get('concrete.cache.doctrine_dev_mode')) {
                    ?> checked <?php
                } ?> />
                <span><?=t('On - Proxy classes will be generated on the fly. Good for development.')?></span>
            </label>
        </div>

        <div class="radio">
            <label>
                <input type="radio" name="DOCTRINE_DEV_MODE" value="0" <?php if (!Config::get('concrete.cache.doctrine_dev_mode')) {
                    ?> checked <?php
                } ?> />
                <span><?= t('Off - Proxy classes need to be manually generated. Helps speed up a live site.') ?></span>
            </label>
        </div>

            <button class="btn btn-default" style="position: absolute; top: -50px; right: 20px" type="submit"><?=t('Save')?></button>

        </div>
    </fieldset>
</form>

<div class="spacer-row-6"></div>

<form method="post" id="entities-refresh-form" action="<?= $view->action('refresh_entities') ?>"  style="position: relative">
    <?= $this->controller->token->output('refresh_entities') ?>

    <fieldset>
        <legend><?=t("Entities")?></legend>

        <div class="form-group">
        <?php foreach($drivers as $namespace => $driver) { ?>

            <h4><?=$namespace?></h4>
            <div class="row">
                <div class="col-md-1"><span class="text-muted"><?=t('Paths')?></span></div>
                <div class="col-md-11">
                    <?php foreach($driver->getPaths() as $path) { ?>
                       <small><?=$path?></small>
                    <?php } ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-1"><span class="text-muted"><?=t('Driver')?></span></div>
                <div class="col-md-11">
                    <small><?=get_class($driver)?></small>
                </div>
            </div>

            <hr/>

        <?php } ?>

            <button class="btn btn-default" style="position: absolute; top: -50px; right: 20px" type="submit"><?=tc('Doctrine', 'Refresh Entities')?></button>

        </div>
    </fieldset>
</form>