<?php
defined('C5_EXECUTE') or die("Access Denied.");

?>

<form method="post" id="entities-settings-form" action="<?= $view->action('update_entity_settings') ?>">
    <?= $this->controller->token->output('update_entity_settings') ?>

    <fieldset style="margin-bottom:15px;">
        <legend><?= t('Settings') ?></legend>

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
    </fieldset>

    <div class="well clearfix">
        <?= $interface->submit(t('Save'), 'entities-settings-form', 'right', 'btn-primary') ?>
    </div>
</form>

<form method="post" id="entities-refresh-form" action="<?= $view->action('refresh_entities') ?>">
    <?= $this->controller->token->output('refresh_entities') ?>

    <fieldset>
        <legend><?=t("Entities")?></legend>

        <p><?= t("Search for application and package-specific entities, refresh their database schema and generate their proxy classes.") ?></p>

        <?= $interface->submit(tc('Doctrine', 'Refresh Entities'), 'entities-refresh-form', 'left', 'btn-default') ?>
    </fieldset>
</form>
