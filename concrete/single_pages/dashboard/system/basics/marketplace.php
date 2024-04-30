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

<?php if (isset($result) && $result->valid) { ?>

<fieldset class="mb-3">
    <legend><?=t('Project Page')?></legend>

    <div class="mb-3"><?=t('Assign licenses to your Concrete site from your Concrete Project page, hosted on marketplace.concretecms.com.')?></div>

    <div class="mb-3">
        <a href="<?=$launchProjectPageUrl?>" class="btn btn-success" target="_blank"><?=t('Visit Page')?></a>
    </div>
</fieldset>

<?php } ?>

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
        <legend><?= t('Advanced: Connection Information') ?></legend>
        <p><?=t('You can manually connect to an existing project page using its public and private key. You can retrieve your marketplace public and private key from your project page on marketplace.concretecms.com.')?></p>

        <div class="form-group">
            <label for="publicKey" class="form-label"><?=t('ID')?></label>
            <?=$form->text('publicKey', $connection->getPublic())?>
        </div>
        <div class="form-group">
            <label for="privateKey" class="form-label"><?=t('Secret Key')?></label>
            <?=$form->text('privateKey', $connection->getPrivate())?>
        </div>
    <button class="btn btn-primary" type="submit"><?=t('Save')?></button>
    </fieldset>
</form>
<?php } ?>