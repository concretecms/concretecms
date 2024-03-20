<?php

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $permissions \Concrete\Core\Permission\Checker
 * @var \Concrete\Core\Marketplace\PackageRepository $marketplace
 */

$connection = $marketplace->getConnection();
if ($connection) {
    if ($marketplace->validate($connection)) {
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

        <div class="alert alert-danger"><?=t('Unable to validate connection to the marketplace. Please connect again.')?></div>

        <form action="<?= $this->action('do_connect') ?>">
            <?= $token->output('do_connect') ?>
            <button type="submit" class="btn btn-success">Connect to Marketplace</button>
        </form>

    <?php }
} else { ?>

    <form action="<?= $this->action('do_connect') ?>">
        <?= $token->output('do_connect') ?>
        <button type="submit" class="btn btn-success">Connect to Marketplace</button>
    </form>

<?php } ?>