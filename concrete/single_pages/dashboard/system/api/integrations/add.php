<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php
/**
 * @var \Concrete\Core\Entity\OAuth\Client $client
 */
?>

<form method="post" class="ccm-dashboard-content-form" action="<?=$view->action('create')?>">
    <?=$this->controller->token->output('create')?>
    <fieldset>
        <legend><?=t('Add OAuth2 Integration')?></legend>
        <div class="form-group">
            <label class="form-label" for="name" ><?php echo t('Name'); ?></label>
            <div class="input-group">
                <?php echo $form->text('name', array('autofocus' => 'autofocus', 'autocomplete' => 'off', 'required' => 'required')); ?>
                <span class="input-group-text"><i class="fas fa-asterisk"></i></span>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="redirect"><?php echo t('Redirect'); ?></label>
            <div class="input-group">
                <?php echo $form->url('redirect', array('autocomplete' => 'off')); ?>
                <span class="input-group-text"><i class="fas fa-asterisk"></i></span>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="redirect"><?php echo t('Documentation'); ?></label>
            <div class="form-check">
                <?php echo $form->checkbox('enableDocumentation', 1); ?>
                <label class="form-check-label" for="enableDocumentation"><?=t('Enable interactive documentation.')?></label>
            </div>
            <div class="help-block"><?=t('When enabled, those with the `access_api` custom permission will be able to view interactive REST API documentation for the integration.')?></div>
        </div>

        <div class="alert alert-info"><?=t('When you add an Oauth2 integration, the client key and secret will automatically be generated.')?></div>
    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?=URL::to('/dashboard/system/api/integrations')?>" class="float-start btn btn-secondary"><?=t('Cancel')?></a>
            <button class="float-end btn btn-primary" type="submit" ><?=t('Add Integration')?></button>
        </div>
    </div>

</form>
