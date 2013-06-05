<?
defined('C5_EXECUTE') or die("Access Denied.");
$h = Loader::helper('concrete/dashboard');
$ih = Loader::helper('concrete/interface');
$form = Loader::helper('form');
if ($downloadableUpgradeAvailable) { ?>
	<?=$h->getDashboardPaneHeaderWrapper(t('Download Update'), false, 'span8 offset2');?>
	<? if (!defined('MULTI_SITE') || MULTI_SITE == false) { ?>
		<a href="<?=$this->action('check_for_updates')?>" class="btn" style="float: right"><?=t('Check For Updates')?></a>
	<? } ?>
		<h2><?=t('Currently Running %s',config::get('SITE_APP_VERSION'))?></h2>
		<div class="clearfix">
		</div>
		<br/>
		<h2><?=t('Available Update')?></h2>
		<form method="post" action="<?=$this->action('download_update')?>" id="ccm-download-update-form">
		
			<?=Loader::helper('validation/token')->output('download_update')?>
			<?=Loader::helper('concrete/interface')->submit(t('Download'), 'ccm-download-update-form', 'right', 'primary')?>
		
			<h3><?=t('Version: %s', $update->version)?>. <?=t('Release Date: %s', date(t('F d, Y'), strtotime($update->date)))?></h3>
			<hr/>
			<div id="ccm-release-notes">
			<?=$update->notes?>
			</div>
			<hr/>
			<span class="notes"><?=t('Note: Downloading an update will NOT automatically install it.')?></span>
		
		</form>
	<?=$h->getDashboardPaneFooterWrapper();?>
<? } else if (count($updates)) { ?>
	<?=$h->getDashboardPaneHeaderWrapper(t('Install Local Update'),false,'span8 offset2',false);?>
		<div class="ccm-pane-body">
			<?print '<strong>' . t('Make sure you <a href="%s">backup your database</a> before updating.', $this->url('/dashboard/system/backup_restore/backup')) . '</strong><br/>';
			$ih = Loader::helper('concrete/interface');

			if (count($updates) == 1) { ?>
					<p><?=t('An update is available. Click below to update to <strong>%s</strong>.', $updates[0]->getUpdateVersion())?></p>
					<span class="label"><?=t('Current Version %s',config::get('SITE_APP_VERSION'))?></span>
				</div>
				<div class="ccm-pane-footer">
					<form method="post" action="<?=$this->action('do_update')?>" id="ccm-update-form">
						<input type="hidden" name="updateVersion" value="<?=$updates[0]->getUpdateVersion()?>" />
						<?=$ih->submit(t('Update'), 'maintenance-mode-form', 'right', 'primary')?>
					</form>
				</div>
			<? } else { ?>
				<p><?=t('Several updates are available. Please choose the desired update from the list below.')?></p>
					<span class="label"><?=t('Current Version')?> <?=config::get('SITE_APP_VERSION')?></span>
				<form method="post" action="<?=$this->action('do_update')?>" id="ccm-update-form">
				<?  $checked = true;
					foreach($updates as $upd) { ?>
						<div class="ccm-dashboard-radio"><input type="radio" name="updateVersion" value="<?=$upd->getUpdateVersion()?>" <?=(!$checked?'':"checked")?> />
							<?=$upd->getUpdateVersion()?>
						</div>
						<? $checked = false;
					} ?>
					</div>
					<div class="ccm-pane-footer">
						<?=$ih->submit(t('Update'),false, 'right', 'primary')?>
					</div>
				</form>
			<? } ?>
		</div>
	<?=$h->getDashboardPaneFooterWrapper(false);?>
	<div class="clearfix">&nbsp;</div>
<? } else { ?>
	<?=$h->getDashboardPaneHeaderWrapper(t('Update concrete5'), false, 'span8 offset2');?>
	<? if (!defined('MULTI_SITE') || MULTI_SITE == false) { ?>
		<a href="<?=$this->action('check_for_updates')?>" class="btn" style="float: right"><?=t('Check For Updates')?></a>
	<? } ?>
		<h2><?=t('Currently Running %s',config::get('SITE_APP_VERSION'))?></h2>
		<div class="clearfix">
		</div>
		<br/>
		
		<p><?=t('No updates available.')?></p>

	<?=$h->getDashboardPaneFooterWrapper();?>
<? } ?>