<?php
defined('C5_EXECUTE') or die("Access Denied.");
use Concrete\Core\File\StorageLocation\StorageLocation;

$u = new User();
/** @var Concrete\Core\File\Service\Application $ch */
$ch = Loader::helper('concrete/file');
$h = Loader::helper('concrete/ui');
$form = Loader::helper('form');

$fp = FilePermissions::getGlobal();
if (!$fp->canAddFiles()) {
    die(t("Unable to add files."));
}

$types = $fp->getAllowedFileExtensions();
$ocID = 0;
if (Loader::helper('validation/numbers')->integer($_REQUEST['ocID'])) {
    $ocID = $_REQUEST['ocID'];
}

$types = $ch->serializeUploadFileExtensions($types);
$valt = Loader::helper('validation/token');
?>
<div class="ccm-ui">
<ul class="nav nav-tabs" id="ccm-file-import-tabs">
<li class="active"><a href="javascript:void(0)" id="ccm-file-add-incoming"><?=t('Add Incoming')?></a></li>
<li><a href="javascript:void(0)" id="ccm-file-add-remote"><?=t('Add Remote Files')?></a></li>
</ul>

<script type="text/javascript">
var ccm_fiActiveTab = "ccm-file-add-incoming";
$("#ccm-file-import-tabs a").click(function() {

	$("li.active").removeClass('active');
	var activesection = ccm_fiActiveTab.substring(13);

	$("#" + ccm_fiActiveTab + "-tab").hide();
	ccm_fiActiveTab = $(this).attr('id');

	$(this).parent().addClass("active");
	$("#" + ccm_fiActiveTab + "-tab").show();
});

$('#check-all-incoming').click(function (event) {
    var checked = this.checked;
    $('.ccm-file-select-incoming').each(function () {
        this.checked = checked;
    });
});

ConcreteFileImportDialog = {

    addFiles: function() {
        var $form = $('#' + ccm_fiActiveTab + '-form');
        if ($form.length) {
            $form.concreteAjaxForm({
                success: function(r) {
                    jQuery.fn.dialog.closeTop();
                    ConcreteEvent.trigger('FileManagerAddFilesComplete', {files: r.files});
                }
            }).submit();
        }
    }
}
</script>

<?php
    $valt = Loader::helper('validation/token');
    $fh = Loader::helper('validation/file');
    $error = false;

    try {
        $incoming_contents = $ch->getIncomingDirectoryContents();
    } catch (\Exception $e) {
        $error = t('Unable to get contents of incoming/ directory');
        $error .= '<br>';
        $error .= $e->getMessage();
    }
?>
<div id="ccm-file-add-incoming-tab">
<?php if (!empty($incoming_contents) && is_array($incoming_contents)) {
    ?>
    <br/>
<form id="ccm-file-add-incoming-form" method="post" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/importers/incoming">
    <input type="hidden" name="ocID" value="<?=$ocID?>" />
		<table id="incoming_file_table" class="table table-striped" width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<th width="10%" valign="middle" class="center theader">
                    <input type="checkbox" id="check-all-incoming"/>
				</th>
				<th width="20%" valign="middle" class="center theader"></th>
				<th width="45%" valign="middle" class="theader"><?=t('Filename')?></th>
				<th width="25%" valign="middle" class="center theader"><?=t('Size')?></th>
			</tr>
		<?php foreach ($incoming_contents as $i => $file) {
    $ft = \Concrete\Core\File\Type\TypeList::getType($file['basename']);
    ?>
			<tr>
				<td width="10%" style="vertical-align: middle" class="center">
					<?php if ($fh->extension($file['basename'])) {
    ?>
						<input type="checkbox" name="send_file<?=$i?>" class="ccm-file-select-incoming" value="<?=$file['basename']?>" />
					<?php 
}
    ?>
				</td>
				<td width="20%" style="vertical-align: middle" class="center"><?=$ft->getThumbnail()?></td>
				<td width="45%" style="vertical-align: middle"><?=$file['basename']?></td>
				<td width="25%" style="vertical-align: middle" class="center"><?=Loader::helper('number')->formatSize($file['size'], 'KB')?></td>
			</tr>
		<?php 
}
    ?>
            <tr>
                <td><input type="checkbox" name="removeFilesAfterPost" value="1" /></td>
                <td colspan="2"><?=t('Remove files from incoming/ directory.')?></td>
            </tr>
		</table>



	<?=$valt->output('import_incoming');
    ?>

</form>
<?php 
} else {
    ?>
    <br/><br/>
    <?php if ($error) {
    ?>
        <div class="alert alert-danger">
            <?php echo $error;
    ?>
        </div>
    <?php 
} else {
    echo t('No files found in %s for the storage location "%s".', REL_DIR_FILES_INCOMING, StorageLocation::getDefault()->getName());
}
} ?>
</div>

<div class="dialog-buttons">
    <button class="btn btn-default" onclick="jQuery.fn.dialog.closeTop()"><?=t("Cancel")?></button>
    <button class="btn btn-success pull-right" onclick="ConcreteFileImportDialog.addFiles()"><?=t("Add Files")?></button>
</div>

    <div id="ccm-file-add-remote-tab" style="display: none">
        <br/>
<form method="POST" id="ccm-file-add-remote-form" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/importers/remote">
    <input type="hidden" name="ocID" value="<?=$ocID?>" />
	<p><?=t('Enter URL to valid file(s)')?></p>
	<?=$valt->output('import_remote');?>

	<?=$form->text('url_upload_1', array('style' => 'width:455px'))?><br/><br/>
	<?=$form->text('url_upload_2', array('style' => 'width:455px'))?><br/><br/>
	<?=$form->text('url_upload_3', array('style' => 'width:455px'))?><br/><br/>
	<?=$form->text('url_upload_4', array('style' => 'width:455px'))?><br/><br/>
	<?=$form->text('url_upload_5', array('style' => 'width:455px'))?><br/>
</form>

</div>
</div>
