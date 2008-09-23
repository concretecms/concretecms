<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<? if (version_compare($latest_version, APP_VERSION, '>')) { ?>
<div id="ccm-dashboard-notification">
The latest version of Concrete5 is <strong><?=$latest_version?></strong>. You are running <?=APP_VERSION?>. <a href="<?=APP_VERSION_LATEST_DOWNLOAD?>">Update Now</a>!
</div>
<? } else if (version_compare(APP_VERSION, Config::get('SITE_APP_VERSION'), '>')) { ?>
<div id="ccm-dashboard-notification">
You have downloaded a new version of Concrete5 but have not upgraded to it yet. <?=APP_VERSION?>. <a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/upgrade">Upgrade your site now</a>!
</div>
<? } ?>