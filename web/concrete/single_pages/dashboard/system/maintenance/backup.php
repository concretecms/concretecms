<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>
<script type="text/javascript">
	dNum = 0;
	function confirmDelete(strFn) {
		//   var ele = $('#confirmDelete').clone().attr('id','confirmDelete'+dNum);
		//   $('body').append($('#confirmDelete'+dNum)); 
		$('#confirmDelete').clone().attr('id', 'confirmDelete'+dNum).appendTo('body');
		var alink = $('#confirmDelete' + dNum + ' input[name=backup_file]').val(strFn); 
		$('#confirmDelete' + dNum).dialog({width: 500, height: 200, title: "<?= t("Confirm Delete"); ?>"}); 
		dNum++;
	}

	rNum = 0;
	function confirmRestore(strFn) {
		//   var ele = $('#confirmDelete').clone().attr('id','confirmDelete'+rNum);
		//   $('body').append($('#confirmDelete'+rNum)); 
		$('#confirmRestore').clone().attr('id', 'confirmRestore'+rNum).appendTo('body');
		var alink = $('#confirmRestore' + rNum + ' input[name=backup_file]').val(strFn); 
		$('#confirmRestore' + rNum + ' .confirmActionBtn a').attr('href',alink); 
		$('#confirmRestore' + rNum).dialog({width:500, height: 200, title: "<?= t("Are you sure?"); ?>"})  
		rNum++;
	}
	$(document).ready(function () {
		$('#executeBackup').click( function() { 
			if ($('#useEncryption').is(':checked')) {
				window.location.href = $(this).attr('href')+$('#useEncryption').val();
				return false;
			}
		});


		if ($.cookie('useEncryption') == "1" ) {
			$('#useEncryption').attr('checked','checked');
		}

		$('#useEncryption').change(function() {
			if ($('#useEncryption').is(':checked')) {
				$.cookie('useEncryption','1');
			} else {
				$.cookie('useEncryption','0');

			}
		}); 
	});

</script>

<!--Dialog -->
<div id="confirmDelete" style="display:none" class="ccm-ui">
	<form method="post" action="<?= $this->action('delete_backup') ?>">
		<p><?= t('This action <strong>cannot be undone</strong>. Are you sure?') ?></p>
		<div class="ccm-buttons well">
			<input type="hidden" name="backup_file" value="" />
			<?= $interface->button_js(t('Cancel'), "jQuery.fn.dialog.closeTop();", 'left'); ?>
			<?= $interface->submit('Delete Backup', false, 'right'); ?>
		</div> 
	</form>
</div>

<!-- End of Dialog //-->

<!--Dialog -->
<div id="confirmRestore" style="display:none" class="ccm-ui">
	<form method="post" action="<?= $this->action('restore_backup') ?>">	
		<p><?= t('This action <strong>cannot be undone</strong>. Are you sure?') ?></p>
		<div class="ccm-buttons well">
			<input type="hidden" name="backup_file" value="" />
			<?= $interface->button_js(t('Cancel'), "jQuery.fn.dialog.closeTop();", 'left'); ?>
			<?= $interface->submit('Restore Backup', false, 'right'); ?>
		</div> 
	</form>
</div>

<!-- End of Dialog //-->

<script type="text/javascript">
	$(document).ready( function() { 
		$('a.dialog-launch').click( function() {
			$.fn.dialog.open({ href: $(this).attr('href'),modal:false });

			return false;
      
		});
	});

</script>


<?
$tp = new TaskPermission();
if ($tp->canBackup()) {
	?>
	<?= Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Existing Backups'), false, 'span12 offset2', false) ?>
	<div class="ccm-pane-body">
		<?php
		if (count($backups) > 0) {
			?>
			<br/>
			<table class="zebra-striped" cellspacing="1" cellpadding="0" border="0">
				<thead>
					<tr>
						<th><?= t('Date') ?></th>
						<th><?= t('File') ?></th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($backups as $arr_bkupInf) { ?>
						<tr> 
							<td style="white-space: nowrap"><?= date(DATE_APP_GENERIC_MDYT_FULL, strtotime($arr_bkupInf['date'])) ?></td>
							<td width="100%"><?= $arr_bkupInf['file']; ?></td>
							<td><?= $interface->button_js(t('Download'), 'window.location.href=\'' . $this->action('download', $arr_bkupInf['file']) . '\''); ?></td>
							<td>
								<? print $interface->button_js(t("Restore"), "confirmRestore('" . $arr_bkupInf['file'] . "')"); ?>
							</td>
							<td>
								<? print $interface->button_js(t("Delete"), "confirmDelete('" . $arr_bkupInf['file'] . "')"); ?>
							</td>
						</tr>
					<? } ?>
				</tbody>
			</table>
		<?php } else { ?>
			<p><?= t('You have no backups available.') ?></p>
		<? } ?>
	</div>
	<?= Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false); ?>
	<?
	$crypt = Loader::helper('encryption');
	?>
	<br />
	<?= Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Create New Backup'), false, 'span12 offset2', false) ?>
	<div class="ccm-pane-body">

		<form method="post" action="<?= $this->action('run_backup') ?>">
			<div class="ccm-buttons well">
				<div style="float: left;"><?= $interface->submit(t("Run Backup"), false, "left") ?></div>
				<? if ($crypt->isAvailable()) { ?>
				<label><input type="checkbox" name="useEncryption" id="useEncryption" value="1" />
							<span><?= t('Use Encryption') ?></span></label>
						<? } else { ?>
				<label><input type="checkbox" value="0" disabled />
							<span><?= t('Use Encryption') ?></span></label>
						<? } ?>
				<br class="clearfix" />
			</div>
		</form>

		<h2><?= t('Important Information about Backup & Restore') ?></h2>

		<p><?= t('Running a backup will create a database export file and store it on your server. Encryption is only advised if you plan on storing the backup on the server indefinitely. This is <strong>not recommended</strong>. After running backup, download the file and make sure that the entire database was saved correctly. If any error messages appear during the backup process, do <b>not</b> attempt to restore from that backup.') ?></p>

	</div>
	<?= Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false); ?>
<? } else { ?>
	<?= Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Backup'), false, 'span6 offset6', false) ?>
	<div class="ccm-pane-body">
		<p><?= t('You do not have permission to create or administer backups.') ?></p>
	</div>
	<?= Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false); ?>
<? } ?>
</div>