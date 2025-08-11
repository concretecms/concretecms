<?php defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Entity\OAuth\Client; ?>
<?php
/**
 * @var \Concrete\Core\Entity\OAuth\Client $client
 */
$clientSecret = isset($clientSecret) ? $clientSecret : null;

// Make sure the client secret matches this client
if ($clientSecret && !password_verify($clientSecret, $client->getClientSecret())) {
    $clientSecret = null;
}

// Get the consent type from the client
$consentType = $client->getConsentType();
?>

<div class="ccm-dashboard-header-buttons">
    <div class="btn-group">
        <a href="<?=URL::to('/dashboard/system/api/integrations'); ?>" class="btn btn-secondary"><?php echo t('Back to Integrations'); ?></a>
        <button class="btn btn-danger" data-dialog="delete-client"><?=t("Delete")?></button>
        <a href="<?=URL::to('/dashboard/system/api/integrations', 'edit', $client->getIdentifier()); ?>" class="btn btn-primary"><?php echo t('Edit'); ?></a>
    </div>
</div>

<fieldset>
    <legend><?=t('Integration Details')?></legend>

    <div class="mb-3">
        <label class="form-label"><?=t('Name')?></label>
        <div><?=h($client->getName())?></div>
    </div>

    <div class="mb-3">
        <label class="form-label"><?=t('Redirect URI(s)')?></label>
        <?php
        $redirectUri = $client->getRedirectUri() ?: t('None provided');
        ?>
        <ul>
            <li><?= implode('</li><li>', (array) $redirectUri) ?></li>
        </ul>
    </div>

    <div class="mb-3">
        <label class="form-label"><?=t('Client ID')?></label>
        <input type="text" class="form-control" onclick="this.select()" value="<?=$client->getClientKey()?>">
    </div>

    <div class="mb-3 <?= $clientSecret ? 'has-warning' : '' ?>">
        <label class="form-label"><?=t('Client Secret')?></label>
        <input type="<?= $clientSecret ? 'text' : 'password' ?>" autocomplete="off" class="form-control" onclick="this.select()" value="<?= $clientSecret ?: str_repeat('*', 96) ?>" <?= $clientSecret ? '' : 'disabled' ?>>
        <div class="help-block">
            <?php
            if ($clientSecret) {
                echo t('Make sure to copy this API secret, this is the last time it will be displayed.');
            } else {
                echo t('This API secret was displayed when this client was first created. It can no longer be displayed.');
            }
            ?>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label"><?=t('User Consent Level')?></label>
        <?php if ($consentType === Client::CONSENT_SIMPLE) { ?>
            <div><?= t('Standard') ?></div>
        <?php } else if ($consentType === Client::CONSENT_NONE) { ?>
            <div><?= t('None') ?></div>
        <?php } ?>
        <div class="consent-warning alert alert-danger mt-3 <?= $consentType !== Client::CONSENT_NONE ? 'd-none' : '' ?>" >
            <?= t("Only disable user consent if you trust this integration fully. By disabling user consent, you remove the user's ability to deny access.") ?>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label"><?=t('Scopes')?></label>
        <?php if ($client->hasCustomScopes()) { ?>

            <?php foreach ($client->getScopes() as $scope) { ?>
                <div><?=$scope->getIdentifier()?></div>
            <?php } ?>

        <?php } else { ?>
            <div><?= t('All') ?></div>
        <?php } ?>

    </div>


</fieldset>

<?php if ($client->isDocumentationEnabled()) { ?>


    <fieldset>
        <legend><?=t('API Documentation')?></legend>
        <?=t('Access the automatically generated API documentation using the Swagger interactive API console. Click below to open the API console in a new window.')?>

        <div class="text-center mt-4"><a target="_blank" href="<?=URL::to('/ccm/system/api/documentation', $client->getIdentifier())?>" class="btn-lg btn btn-secondary"><?=t('View API Documentation Console')?></a></div>

    </fieldset>

<?php } ?>


<div style="display: none">
    <div data-dialog-wrapper="delete-client">
        <form method="post" action="<?php echo $view->action('delete'); ?>">
            <?php echo Loader::helper('validation/token')->output('delete'); ?>
            <input type="hidden" name="clientID" value="<?php echo $client->getIdentifier(); ?>">
            <p><?=t('Are you sure you want to delete this credentials set ? This cannot be undone.')?></p>
            <div class="dialog-buttons">
                <button class="btn btn-secondary float-start" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
                <button class="btn btn-danger float-end" onclick="$('div[data-dialog-wrapper=delete-client] form').submit()"><?=t('Delete')?></button>
            </div>
        </form>
    </div>
</div>
