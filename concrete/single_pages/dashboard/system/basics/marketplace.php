<?php

use Concrete\Core\Marketplace\Model\ValidateResult;

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $permissions \Concrete\Core\Permission\Checker
 * @var $connection \Concrete\Core\Marketplace\ConnectionInterface
 * @var \Concrete\Core\Marketplace\PackageRepository $marketplace
 */

?>

<fieldset class="mb-3">
    <legend><?=t('Connection Status')?></legend>

    <?php
    if ($connection) {
        $result = $marketplace->validate($connection, true);
        if ($result->valid) { ?>

            <div class="alert alert-success"><?=t('This site is successfully connected to the Concrete marketplace.')?></div>

        <?php
        } else { ?>

            <div class="alert alert-danger"><?=t('Error validating connection to marketplace. %s', h($result->error))?></div>

            <?php if ($result->code === ValidateResult::VALIDATE_RESULT_ERROR_URL_MISMATCH) { ?>
            <form action="<?= $this->action('do_connect') ?>" method="post">
                <?= $token->output('do_connect') ?>
                <button type="submit" name="connect" value="connect_url" class="btn btn-success"><?=t('Connect This URL to Marketplace')?></button>
            </form>
            <?php } ?>


        <?php }
    }
    ?>
</fieldset>

<?php if (isset($result) && !$result->valid) { ?>
    <hr>
    <form action="<?= $this->action('do_connect') ?>" class="mb-5">
        <?= $token->output('do_connect') ?>
        <fieldset>
            <h5><?=t('Re-Connect')?></h5>
            <p><?=t('If you have specified a public and private key but your site is still not connected to the marketplace, it may need to be re-connected. Click below to re-connect. Re-connecting will create a new site record in the Concrete CMS marketplace.')?></p>
            <button class="btn btn-danger" type="submit"><?=t('Re-Connect')?></button>
        </fieldset>
    </form>
    <?php } ?>

<?php if (isset($result) && $result->valid) {

    Element::get('dashboard/marketplace/extend')->render();

} ?>

<?php
if ($connection) {
    $result = $marketplace->validate($connection, true);
    if ($result->valid) {
        if ($permissions->canInstallPackages()) { ?>

        <?php
        } else { ?>

            <?= t('You do not have permission to connect this site to the marketplace.') ?>

        <?php
        }
    } else { ?>


    <?php }
} else { ?>

    <form action="<?= $this->action('do_connect') ?>">
        <?= $token->output('do_connect') ?>
        <button type="submit" class="btn btn-success">Connect to Marketplace</button>
    </form>

<?php } ?>

<?php if ($connection) { ?>

<form method="post" action="<?=$view->action('update_connection_settings')?>">
    <?=$token->output('update_connection_settings')?>
    <fieldset class="mb-3">
        <legend><?= t('Advanced') ?></legend>
        <h5><?=t('Connection Information')?></h5>
        <p><?=t('You can manually connect to an existing Concrete marketplace Site record using its public and private key. You can retrieve your marketplace public and private key from your Concrete Site page on market.concretecms.com.')?></p>

        <div class="mb-3">
            <label for="publicKey" class="form-label"><?=t('ID')?></label>
            <?=$form->text('publicKey', $connection->getPublic())?>
        </div>
        <div class="mb-3">
            <label for="privateKey" class="form-label"><?=t('Secret Key')?></label>
            <?=$form->text('privateKey', $connection->getPrivate())?>
        </div>
        <button class="btn btn-primary" type="submit"><?=t('Save')?></button>
    </fieldset>
</form>

<?php if (isset($result) && $result->valid) { ?>

<hr>
<fieldset>
    <h5><?=t('Concrete Site Record')?></h5>
    <p><?=t('Your site has a record saved for it in the marketplace. You can assign licenses to your Concrete Site directly from this page, hosted on market.concretecms.com')?></p>
    <a href="<?=$launchProjectPageUrl?>" target="_blank" class="btn btn-secondary"><?=t('Visit Page')?></a>
</fieldset>

<?php } ?>

<?php } ?>