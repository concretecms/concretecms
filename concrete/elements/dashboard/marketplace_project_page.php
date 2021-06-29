<?php
defined('C5_EXECUTE') or die("Access Denied.");
$mi = Marketplace::getInstance();
?>
<h4><?= t("Project Page"); ?></h4>
<p><?= t('Your marketplace project page URL is:'); ?><br/><a target="_blank" href="<?= $mi->getSitePageURL(); ?>"><?= $mi->getSitePageURL(); ?></a></p>
