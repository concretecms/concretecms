<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<h1><?=t('Welcome Back')?></h1>
<br/>

<? if (version_compare($latest_version, APP_VERSION, '>')) { ?>
<p><span class="label notice"><?=t('concrete5 Update')?></span> <?=t('The latest version of concrete5 is <strong>%s</strong>. You are currently running concrete5 version <strong>%s</strong>.', $latest_version, APP_VERSION)?> <a class="" href="<?=$view->url('/dashboard/system/backup/update')?>"><?=t('Learn more and update.')?></a></p>

<? } else if (version_compare(APP_VERSION, Config::get('concrete.version'), '>')) { ?>
<p><span class="label warning"><?=t('concrete5')?></span>
<?=t('You have downloaded a new version of concrete5 but have not upgraded to it yet.');?> <a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/upgrade"><?=t('Update concrete5.')?></a></p>
<? } ?>

<? if ($updates > 0) { ?>
	<p><span class="label"><?=t('Add-On Updates')?></span>
	<? if ($updates == 1) { ?>
		<?=t('There is currently <strong>1</strong> update available.')?>
	<? } else { ?>
		<?=t('There are currently <strong>%s</strong> updates available.', $updates)?>
	<? } ?>
	<a class="" href="<?=$view->url('/dashboard/extend/update')?>"><?=t('Update add-ons.')?></a></p>
<? } ?>

