<? defined('C5_EXECUTE') or die("Access Denied.");
$h = Loader::helper('concrete/dashboard');
$ih = Loader::helper('concrete/interface');
$form = Loader::helper('form');
if (!$showDownloadBox) { 
	echo $h->getDashboardPaneHeaderWrapper(t('Download Update'));?>
	<div class="ccm-pane-body">
		<? if ($downloadableUpgradeAvailable) { ?>
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
		<? } else { ?>
			<p><?=t('All available updates have been either downloaded or applied.')?></p>
		<? } ?>
	
	</div>
	<?=$h->getDashboardPaneFooterWrapper();?>
	<div class="clearfix">&nbsp;</div>
<? } ?>

<? if (count($updates) > 0) { ?>
	<?=$h->getDashboardPaneHeaderWrapper(t('Install Local Update'),false,false,false);?>
		<div class="ccm-pane-body">
			<?print '<strong>' . t('Make sure you <a href="%s">backup your database</a> before updating.', $this->url('/dashboard/system/backup')) . '</strong><br/>';
			$ih = Loader::helper('concrete/interface');

			switch(count($updates)) {
				case '1': ?>
					<p><?=t('An update is available. Click below to update to <strong>%s</strong>.', $updates[0]->getUpdateVersion())?></p>
				</div>
				<div class="ccm-pane-footer">
					<form method="post" action="<?=$this->action('do_update')?>" id="ccm-update-form">
						<input type="hidden" name="updateVersion" value="<?=$updates[0]->getUpdateVersion()?>" />
						<?=$ih->submit(t('Update'), 'maintenance-mode-form', 'right', 'primary')?>
					</form>
				</div>
				<?break;
				default: ?>
					<p><?=t('Several updates are available. Please choose the desired update from the list below.')?></p>
				</div>
				<div class="ccm-pane-footer">
					<form method="post" action="<?=$this->action('do_update')?>" id="ccm-update-form">
					<? 
						$checked = true;
						foreach($updates as $upd) { 
							?>
							<div class="ccm-dashboard-radio"><input type="radio" name="updateVersion" value="<?=$upd->getUpdateVersion()?>" <? if ($checked) { ?> checked <? } ?> />
								<?=$upd->getUpdateVersion()?>
							</div>
							<?
							$checked = false;
						}
						?>
						<?=$ih->submit(t('Update'),'maintenance-mode-form', 'right', 'primary')?>
					</form>
					</div>
				<? break;
			}
			?>
		</div>
	<?=$h->getDashboardPaneFooterWrapper(false);?>
	<div class="clearfix">&nbsp;</div>

<? } else if (!$downloadableUpgradeAvailable) { ?>
	<?=$h->getDashboardPaneHeaderWrapper(t('Update concrete5'));?>
		<div class="ccm-pane-body">
			<h2><?=t('You are currently up to date!')?></h2>
		</div>
	<?=$h->getDashboardPaneFooterWrapper();?>

<? } ?>