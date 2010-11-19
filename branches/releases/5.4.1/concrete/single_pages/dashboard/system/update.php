<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php  if ($showDownloadBox) { ?>
<h1><span><?php echo t('Download Update')?></span></h1>
<div class="ccm-dashboard-inner">
	<?php  if ($downloadableUpgradeAvailable) { ?>
	<form method="post" action="<?php echo $this->action('download_update')?>" id="ccm-download-update-form">
	
		<?php echo Loader::helper('validation/token')->output('download_update')?>
		<?php echo Loader::helper('concrete/interface')->submit(t('Download'), 'ccm-download-update-form')?>
	
		<h2><?php echo t('Version: %s', $update->version)?>. <?php echo t('Release Date: %s', date(t('F d, Y'), strtotime($update->date)))?></h2>
		<div><a href="javascript:void(0)" onclick="jQuery.fn.dialog.open({modal: false, title: '<?php echo t("Release Notes")?>', width: 500, height: 400, element: $('#ccm-release-notes')})"><?php echo t('View Full Release Notes &gt;')?></a></div>
		<br/>
		<span class="notes"><?php echo t('Note: Downloading an update will NOT automatically install it.')?></span>
	
	<div id="ccm-release-notes" style="display: none">
	<?php echo $update->notes?>
	<br/><br/>
	<?php echo Loader::helper('concrete/interface')->button_js(t('Close'), 'javascript:jQuery.fn.dialog.closeTop()', 'left');?>
	</div>
	
	</form>
	
	
	<?php  } else { ?>
		<p><?php echo t('All available updates have been either downloaded or applied.')?></p>
	<?php  } ?>

	</div>
<?php  } ?>

<?php  if (count($updates) > 0) { ?>

<h1><span><?php echo t('Install Local Update')?></span></h1>
<div class="ccm-dashboard-inner">
<?php 

print '<strong>' . t('Make sure you <a href="%s">backup your database</a> before updating.', $this->url('/dashboard/system/backup')) . '</strong><br/>';
$ih = Loader::helper('concrete/interface');

switch(count($updates)) {
	case '1': ?>
	
	<form method="post" action="<?php echo $this->action('do_update')?>" id="ccm-update-form">
	<input type="hidden" name="updateVersion" value="<?php echo $updates[0]->getUpdateVersion()?>" />
	
	<p><?php echo t('An update is available. Click below to update to <strong>%s</strong>', $updates[0]->getUpdateVersion())?>
	</p>

	<?php echo $ih->submit(t('Update'), 'ccm-update-form', 'left')?>
	
			<div class="ccm-spacer">&nbsp;</div>

	</form>	
	
	<?php 	
		break;
	case '0':
		print '<h2>' . t('You are currently up to date!') . '</h2>';	
		break;
	default: ?>
	
	<form method="post" action="<?php echo $this->action('do_update')?>" id="ccm-update-form">
	<p><?php echo t('Several updates are available. Please choose the desired update from the list below.')?></p>
	<?php  
		$checked = true;
		foreach($updates as $upd) { 
	
		?>

		<div class="ccm-dashboard-radio"><input type="radio" name="updateVersion" value="<?php echo $upd->getUpdateVersion()?>" <?php  if ($checked) { ?> checked <?php  } ?> />
			<?php echo $upd->getUpdateVersion()?>
		</div>
		
		<?php 
		$checked = false;
		
		}
		
		?>

		<?php echo $ih->submit(t('Update'), 'ccm-update-form', 'left')?>
		
		<div class="ccm-spacer">&nbsp;</div>
		
	
	</form>	

	
	<?php  
	
		break;
}
?>
</div>

<?php  } else if (!$downloadableUpgradeAvailable) { ?>

<h1><span><?php echo t('Update concrete5')?></span></h1>
<div class="ccm-dashboard-inner">
	<h2><?php echo t('You are currently up to date!')?></h2>
</div>

<?php  } ?>