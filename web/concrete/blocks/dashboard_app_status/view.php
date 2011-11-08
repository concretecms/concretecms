<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<h1 id="ccm-dashboard-welcome-back"><?=t('Welcome Back')?>
<span><?=t('You are currently running concrete5 version <strong>%s</strong>.', APP_VERSION)?></span>
</h1>

<? if (version_compare($latest_version, APP_VERSION, '>')) { ?>
<div class="block-message alert-message info">
<h4><?=t('A new version of concrete5 is available!')?></h4>
<p><?=t('The latest version of concrete5 is <strong>%s</strong>. You are running %s.', $latest_version, APP_VERSION)?></p>
<div class="alert-actions"><a class="small btn" href="<?=$this->url('/dashboard/system/maintenance/update', 'update')?>"><?=t('Update concrete5')?></a></div>
</div>
<? } else if (version_compare(APP_VERSION, Config::get('SITE_APP_VERSION'), '>')) { ?>
<div class="block-message alert-message warning">
<h4><?=t('A new version of concrete5 is available!')?></h4>
<p><?=t('You have downloaded a new version of concrete5 but have not upgraded to it yet.');?></p>
<div class="alert-actions"><a class="small btn" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/upgrade"><?=t('Update concrete5')?></a></div>
</div>
<? } ?>

<? if ($updates > 0) { ?>
	<div class="block-message alert-message info">
	<h4><?=t('Add-On updates are available!')?></h4>
	<? if ($updates == 1) { ?>
		<p><?=t('There is currently <strong>1</strong> update available.')?></p>
	<? } else { ?>
		<p><?=t('There are currently <strong>%s</strong> updates available.', $updates)?></p>
	<? } ?>
	<div class="alert-actions"><a class="small btn" href="<?=$this->url('/dashboard/extend/update')?>"><?=t('Update Add-Ons')?></a></div>
	</div>
<? } ?>