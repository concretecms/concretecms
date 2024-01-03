<?php
use Concrete\Core\Marketplace\Marketplace;

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $permissions \Concrete\Core\Permission\Checker
 * @var $marketplace Marketplace
 */

$credentials = $marketplace->getCredentials();

if (!$marketplace->isConnected()) {
    ?>
    <form action="<?= $this->action('do_connect') ?>">
        <?= $token->output('do_connect') ?>
        <button type="submit" class="btn btn-success">Connect to Marketplace</button>
    </form>
    <?php
} elseif ($permissions->canInstallPackages()) {
    ?>
    <div class="form-group">
        <label class="form-label">Marketplace Public Key</label>
        <div class="input-group">
            <button class="btn btn-light input-group-btn" title="Copy" onclick="copyToClipboard()">
                <i class="far fa-copy"></i>
            </button>
            <input class="form-control" disabled value="<?= $credentials[0] ?>" />
        </div>
    </div>
    <?php
} else {
    ?>
    <?=t('You do not have permission to connect this site to the marketplace.')?>
<?php } ?>

<script>
    function copyToClipboard() {
        navigator.permissions.query({name: "clipboard-write"}).then((result) => {
            if (result.state === "granted" || result.state === "prompt") {
                navigator.clipboard.writeText(<?= json_encode($credentials[0]) ?>)
            }
        });
    }

</script>