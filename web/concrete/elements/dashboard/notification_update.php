<? defined('C5_EXECUTE') or die("Access Denied."); ?> 
<? if (version_compare($latest_version, APP_VERSION, '>')) { ?>
<div id="ccm-dashboard-notification">
<?=t('The latest version of concrete5 is <strong>%s</strong>. You are running %s. <a href="%s">Update Now</a>!', $latest_version, APP_VERSION, $this->url('/dashboard/system/backup_restore/update'))?>
</div>
<? } else if (version_compare(APP_VERSION, Config::get('SITE_APP_VERSION'), '>')) { ?>
<div id="ccm-dashboard-notification">
<?=t('You have downloaded a new version of concrete5 but have not upgraded to it yet. <a href="%s">Upgrade your site to %s now</a>!', REL_DIR_FILES_TOOLS_REQUIRED . '/upgrade', APP_VERSION); ?>
</div>
<? } ?>