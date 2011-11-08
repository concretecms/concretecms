<?
defined('C5_EXECUTE') or die("Access Denied.");
$h = Loader::helper('concrete/dashboard');
$ih = Loader::helper('concrete/interface');
$form = Loader::helper('form');
if ($downloadableUpgradeAvailable) { ?>
	<?=$h->getDashboardPaneHeaderWrapper(t('Download Update'));?>
		<form method="post" action="<?=$this->action('download_update')?>" id="ccm-download-update-form">
		
			<?=Loader::helper('validation/token')->output('download_update')?>
			<?=Loader::helper('concrete/interface')->submit(t('Download'), 'ccm-download-update-form')?>
		
			<h2><?=t('Version: %s', $update->version)?>. <?=t('Release Date: %s', date(t('F d, Y'), strtotime($update->date)))?></h2>
			<div><a href="javascript:void(0)" onclick="jQuery.fn.dialog.open({modal: false, title: '<?=t("Release Notes")?>', width: 500, height: 400, element: $('#ccm-release-notes')})"><?=t('View Full Release Notes &gt;')?></a></div>
			<br/>
			<span class="notes"><?=t('Note: Downloading an update will NOT automatically install it.')?></span>
		
		<div id="ccm-release-notes" style="display: none">
		<?=$update->notes?>
		<br/><br/>
		<?=Loader::helper('concrete/interface')->button_js(t('Close'), 'javascript:jQuery.fn.dialog.closeTop()', 'left');?>
		</div>
		</form>
	<?=$h->getDashboardPaneFooterWrapper();?>
<? } else if (count($updates)) { ?>
	<?=$h->getDashboardPaneHeaderWrapper(t('Install Local Update'),false,false,false);?>
		<div class="ccm-pane-body">
			<?print '<strong>' . t('Make sure you <a href="%s">backup your database</a> before updating.', $this->url('/dashboard/system/maintenance/backup')) . '</strong><br/>';
			$ih = Loader::helper('concrete/interface');

			if (count($updates) == 1) { ?>
					<code><?=t('Current Version')?> <?=config::get('SITE_APP_VERSION')?></code>
					<p><?=t('An update is available. Click below to update to <strong>%s</strong>.', $updates[0]->getUpdateVersion())?></p>
				</div>
				<div class="ccm-pane-footer">
					<form method="post" action="<?=$this->action('do_update')?>" id="ccm-update-form">
						<input type="hidden" name="updateVersion" value="<?=$updates[0]->getUpdateVersion()?>" />
						<?=$ih->submit(t('Update'), 'maintenance-mode-form', 'right', 'primary')?>
					</form>
				</div>
			<? } else { ?>
				<p><?=t('Several updates are available. Please choose the desired update from the list below.')?></p>
					<code><?=t('Current Version')?> <?=config::get('SITE_APP_VERSION')?></code>
				<form method="post" action="<?=$this->action('do_update')?>" id="ccm-update-form">
				<?  $checked = true;
					foreach($updates as $upd) { ?>
						<div class="ccm-dashboard-radio"><input type="radio" name="updateVersion" value="<?=$upd->getUpdateVersion()?>" <?=(!$checked?:"checked")?> />
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
	<?=$h->getDashboardPaneHeaderWrapper(t('Update concrete5'));?>
		<h3><?=t('You are currently up to date!')?></h3>
		<code><?=t('Current Version')?> <?=config::get('SITE_APP_VERSION')?></code>
	<?=$h->getDashboardPaneFooterWrapper();?>
<? } ?>