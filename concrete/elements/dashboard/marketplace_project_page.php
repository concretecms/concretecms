<?php
defined('C5_EXECUTE') or die("Access Denied.");
$packageRepository = $packageRepository ?? null;
$connection = $connection ?? null;

if (!$packageRepository || !$connection) {
    return;
}
?>
<h4><?= t("Project Page"); ?></h4>
<p>
    <?= t('Your marketplace project ID is:'); ?>
    <br/>
    <?= $connection->getPublic() ?>
</p>
