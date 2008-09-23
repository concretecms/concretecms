<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<? if (version_compare($latest_version, APP_VERSION, '>')) { ?>
<div id="ccm-dashboard-notification">
The latest version of Concrete5 is <strong><?=$latest_version?></strong>. You are running <?=APP_VERSION?>. <a href="<?=APP_VERSION_LATEST_DOWNLOAD?>">Upgrade Now</a>!
</div>
<? } ?>
