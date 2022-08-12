<?php
defined('C5_EXECUTE') or die("Access Denied.");

if ($enable_api) {
?>
    <div class="ccm-dashboard-header-buttons">
        <a href="<?=URL::to('/dashboard/system/api/integrations')?>" class="btn btn-secondary"><?=t('Integrations')?></a>
    </div>
<?php } ?>

<form method="post" action="<?php echo $view->action('submit'); ?>">
    <?php echo $token->output('submit'); ?>

    <div class="form-check mb-3">
        <?php echo $form->checkbox('enable_api', 1, $enable_api) ?>
        <label for="enable_api"><?php echo t('Enable API'); ?></label>
    </div>

    <div class="help-block"><?=t('You must ensure that the HTTP Authorization header is available on your web server. Some web servers or proxies may strip it away.')?></div>

<?php if ($enable_api) { ?>


    <section class="mt-5">
        <h3><?=t('Enabled Grant Types')?></h3>
        <div class="help-block"><?=t('Choose which OAuth2 grant types you want to have access to the API for this site.')?></div>

        <?php foreach($availableGrantTypes as $key => $label) {
            $enabled = false;
            if (isset($grantTypes[$key]) && $grantTypes[$key] === true) {
                $enabled = true;
            }
            ?>
            <div class="form-check">
                <?=$form->checkbox('enabledGrantTypes[]', $key, $enabled)?>
                <label><?=$label?></label>
            </div>
        <?php } ?>

    </section>

<?php } ?>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="btn btn-primary float-end" type="submit"><?=t("Save")?></button>
        </div>
    </div>
</form>
