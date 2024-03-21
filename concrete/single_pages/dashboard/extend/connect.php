<?php

use Concrete\Core\Marketplace\Model\ValidateResult;

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $permissions \Concrete\Core\Permission\Checker
 * @var \Concrete\Core\Marketplace\PackageRepository $marketplace
 */

$connection = $marketplace->getConnection();
if ($connection) {
    $result = $marketplace->validate($connection, true);
    if ($result->valid) {
        if ($permissions->canInstallPackages()) { ?>

            <div class="form-group">
                <label class="form-label">Marketplace Public Key</label>
                <div class="input-group">
                    <button class="btn btn-light input-group-btn" title="Copy" onclick="copyToClipboard()">
                        <i class="far fa-copy"></i>
                    </button>
                    <input class="form-control" disabled value="<?= $connection->getPublic() ?>"/>
                </div>
            </div>

            <script>
                function copyToClipboard() {
                    navigator.permissions.query({name: "clipboard-write"}).then((result) => {
                        if (result.state === "granted" || result.state === "prompt") {
                            navigator.clipboard.writeText(<?= json_encode(
                                $connection ? $connection->getPublic() : ''
                            ) ?>)
                        }
                    });
                }

            </script>
        <?php
        } else { ?>

            <?= t('You do not have permission to connect this site to the marketplace.') ?>

        <?php
        }
    } else { ?>

        <div class="alert alert-danger"><?=t('Error validating connection to marketplace. %s', h($result->error))?></div>


        <form action="<?= $this->action('do_connect') ?>" method="post">
                <?= $token->output('do_connect') ?>
            <?php if ($result->code === ValidateResult::VALIDATE_RESULT_ERROR_URL_MISMATCH) { ?>
                <button type="submit" name="connect" value="connect_url" class="btn btn-success"><?=t('Connect This URL to Marketplace')?></button>
            <?php } else { ?>
                <button type="submit" name="connect" value="connect" class="btn btn-success"><?=t('Connect to Marketplace')?></button>
            <?php } ?>
        </form>

    <?php }
} else { ?>

    <form action="<?= $this->action('do_connect') ?>">
        <?= $token->output('do_connect') ?>
        <button type="submit" class="btn btn-success">Connect to Marketplace</button>
    </form>

<?php } ?>