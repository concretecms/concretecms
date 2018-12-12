<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php
/**
 * @var \Concrete\Core\Entity\OAuth\Client $client
 */
?>

<form method="post" class="ccm-dashboard-content-form" action="<?=$view->action('update', $client->getIdentifier())?>">
    <?=$this->controller->token->output('update')?>
    <fieldset>
        <legend><?=t('Update OAuth2 Integration')?></legend>
        <div class="form-group">
            <label for="name" class="control-label"><?php echo t('Name'); ?></label>
            <div class="input-group">
                <?php echo $form->text('name', $client->getName(), array('autofocus' => 'autofocus', 'autocomplete' => 'off', 'required' => 'required')); ?>
                <span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
            </div>
        </div>

        <div class="form-group">
            <label for="redirect" class="control-label"><?php echo t('Redirect'); ?></label>
            <div class="input-group">
                <?php echo $form->url('redirect', $client->getRedirectUri(), array('autocomplete' => 'off')); ?>
                <span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
            </div>
        </div>
    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?=URL::to('/dashboard/users/oauth2/view_client', $client->getIdentifier())?>" class="pull-left btn btn-default"><?=t('Cancel')?></a>
            <button class="pull-right btn btn-primary" type="submit" ><?=t('Update')?></button>
        </div>
    </div>

</form>
