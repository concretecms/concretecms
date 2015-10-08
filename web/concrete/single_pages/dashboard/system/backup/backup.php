<?php defined('C5_EXECUTE') or die('Access Denied.');

$dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */

?>
<script type="text/javascript">
	dNum = 0;
	function confirmDelete(strFn) {
		//   var ele = $('#confirmDelete').clone().attr('id','confirmDelete'+dNum);
		//   $('body').append($('#confirmDelete'+dNum)); 
		$('#confirmDelete').clone().attr('id', 'confirmDelete'+dNum).appendTo('body');
		var alink = $('#confirmDelete' + dNum + ' input[name=backup_file]').val(strFn); 
		$('#confirmDelete' + dNum).dialog({width: 500, height: 200, title: "<?= t("Confirm Delete"); ?>", buttons:[{}], 'open': function() {
			$(this).parent().find('.ui-dialog-buttonpane').addClass("ccm-ui").html('');
			$(this).find('.dialog-buttons').appendTo($(this).parent().find('.ui-dialog-buttonpane'));
			$(this).find('.dialog-buttons').remove();
			}
		});
		dNum++;
	}

	rNum = 0;
	function confirmRestore(strFn) {
		//   var ele = $('#confirmDelete').clone().attr('id','confirmDelete'+rNum);
		//   $('body').append($('#confirmDelete'+rNum)); 
		$('#confirmRestore').clone().attr('id', 'confirmRestore'+rNum).appendTo('body');
		var alink = $('#confirmRestore' + rNum + ' input[name=backup_file]').val(strFn); 
		$('#confirmRestore' + rNum + ' .confirmActionBtn a').attr('href',alink); 
		$('#confirmRestore' + rNum).dialog({width:500, height: 200, title: "<?= t("Are you sure?"); ?>", buttons:[{}], 'open': function() {
			$(this).parent().find('.ui-dialog-buttonpane').addClass("ccm-ui").html('');
			$(this).find('.dialog-buttons').appendTo($(this).parent().find('.ui-dialog-buttonpane'));
			$(this).find('.dialog-buttons').remove();
			}
		});
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
		<p><?= t('This action <strong>cannot be undone</strong>. Are you sure?') ?></p>
		<div class="dialog-buttons">
			<form method="post" action="<?= $view->action('delete_backup') ?>" style="display: inline">
			<input type="hidden" name="backup_file" value="" />
			<?= $interface->submit(t('Delete Backup'), false, 'right', 'error'); ?>
	</form>
		</div> 
</div>

<!-- End of Dialog //-->

<!--Dialog -->
<div id="confirmRestore" style="display:none" class="ccm-ui">
		<p><?= t('This action <strong>cannot be undone</strong>. Are you sure?') ?></p>
		<div class="dialog-buttons">
			<form method="post" action="<?= $view->action('restore_backup') ?>" style="display: inline">	
			<input type="hidden" name="backup_file" value="" />
			<?= $interface->submit(t('Restore Backup'), false, 'right', 'primary'); ?>
		</form>
		</div> 
</div>

<!-- End of Dialog //-->


<?
$tp = new TaskPermission();
if ($tp->canBackup()) {
	?>
    <h3><?=t('Existing Backups')?></h3>
    <?php
    if (count($backups) > 0) {
        ?>
        <table class="table table-striped" cellspacing="1" cellpadding="0" border="0">
            <thead>
                <tr>
                    <th><?= t('Date') ?></th>
                    <th><?= t('File') ?></th>
                    <th colspan="3"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($backups as $arr_bkupInf) { ?>
                    <tr>
                        <td width="50%" style="white-space: nowrap"><?= $dh->formatDateTime($arr_bkupInf['date'], true) ?></td>
                        <td width="50%"><?= $arr_bkupInf['file']; ?></td>
                        <td style="white-space: nowrap">
                            <?= $interface->button_js(t('Download'), 'window.location.href=\'' . $view->action('download', $arr_bkupInf['file']) . '\'', 'left', 'small'); ?>

                            <?php print $interface->button_js(t("Restore"), "confirmRestore('" . $arr_bkupInf['file'] . "')", 'left','small'); ?>

                            <?php print $interface->button_js(t("Delete"), "confirmDelete('" . $arr_bkupInf['file'] . "')",'left','small'); ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>


    <?php } else { ?>
        <p><?= t('You have no backups available.') ?></p>
    <?php } ?>

        <?
            $crypt = Loader::helper('encryption');
        ?>
        <h3><?=t('Create new Backup')?></h3>
            <form method="post" action="<?= $view->action('run_backup') ?>">
                <fieldset>
                    <?php echo $this->controller->token->output('run_backup'); ?>
                    <div class="control-group">
                        <div class="checkbox">
                            <?php if ($crypt->isAvailable()) { ?>
                                <label><input type="checkbox" name="useEncryption" id="useEncryption" value="1" />
                                <span><?= t('Use Encryption') ?></span></label>
                            <?php } else { ?>
                                <label><input type="checkbox" value="0" disabled />
                                <span><?= t('Use Encryption') ?></span></label>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <?= $interface->submit(t("Run Backup"), false, "left", 'btn btn-primary') ?>
                    </div>
                </fieldset>
            </form>

            <h2><?= t('Important Information about Backup & Restore') ?></h2>

            <p class="bg-warning"><?= t('Running a backup will create a database export file and store it on your server. Encryption is only advised if you plan on storing the backup on the server indefinitely. This is <strong>not recommended</strong>. After running backup, download the file and make sure that the entire database was saved correctly. If any error messages appear during the backup process, do <b>not</b> attempt to restore from that backup.') ?></p>

<?php } else { ?>
	<p><?= t('You do not have permission to create or administer backups.') ?></p>
<?php } ?>
