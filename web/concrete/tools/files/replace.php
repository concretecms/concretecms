<?php
defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
$ch = Loader::helper('concrete/file');
$valt = Loader::helper('validation/token');
$form = Loader::helper('form');
use Concrete\Core\File\StorageLocation\StorageLocation;


$f = File::getByID($_REQUEST['fID']);
$fp = new Permissions($f);
if (!$fp->canEditFileContents()) {
    die(t('Access Denied.'));
}

$searchInstance = Loader::helper('text')->entities($_REQUEST['searchInstance']);
?>

<div class="ccm-ui">

    <ul class="nav nav-tabs" id="ccm-file-import-tabs">
        <li class="active"><a href="javascript:void(0)" id="ccm-file-add-computer"><?=t('Add From Computer')?></a></li>
        <li><a href="javascript:void(0)" id="ccm-file-add-incoming"><?=t('Add From Incoming')?></a></li>
        <li><a href="javascript:void(0)" id="ccm-file-add-remote"><?=t('Add Remote Files')?></a></li>
    </ul>
    <script type="text/javascript">
        var ccm_fiActiveTab = "ccm-file-add-computer";
        $("#ccm-file-import-tabs a").click(function() {

            $("li.active").removeClass('active');
            var activesection = ccm_fiActiveTab.substring(13);

            $("#" + ccm_fiActiveTab + "-tab").hide();
            ccm_fiActiveTab = $(this).attr('id');

            $(this).parent().addClass("active");
            $("#" + ccm_fiActiveTab + "-tab").show();
        });

    </script>
    <div id="ccm-file-add-computer-tab">
        <form method="post" class="form-inline" id="ccm-file-manager-replace-upload" data-dialog-form="replace-file" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/importers/single">
            <h4><?=t('Add From Computer')?></h4>
            <input type="file" name="Filedata" class="form-control" style="width: 195px" />
            <?=$valt->output('upload');?>
            <?= $form->hidden('fID', $f->getFileID()); ?>
            <button type="submit" class="btn btn-warning btn-sm"><?=t('Upload')?></button>
        </form>
    </div>


    <div id="ccm-file-add-incoming-tab" style="display: none">
        <h4><?=t('Add from Incoming Directory')?></h4>
        <div>
            <?php
            $contents = array();
            $con1 = array();
            $error = false;
            try {
                $con1 = $ch->getIncomingDirectoryContents();
            } catch(\Exception $e) {
                $error = t('Unable to get contents of incoming/ directory');
                $error .= '<br>';
                $error .= $e->getMessage();
            }
            foreach($con1 as $con) {
                $contents[$con['basename']] = $con['basename'];
            }
            if (count($contents) > 0) { ?>
                <form method="post" id="ccm-file-manager-replace-incoming" class="form-inline" data-dialog-form="replace-file" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/importers/incoming">
                    <?= $form->select('send_file', $contents, array('style' => 'width:195px'));?>
                    &nbsp;&nbsp;
                    <button type="submit" class="btn btn-default btn-sm"><?=t('Replace')?></button>
                    <?= $form->hidden('fID', $f->getFileID()); ?>
                    <?=$valt->output('import_incoming');?>
                </form>
            <?php } else {
                if($error) { ?>
                    <div class="alert alert-danger">
                        <?php echo $error;?>
                    </div>
                <?php } else {
                    echo t('No files found in %s for the storage location "%s".', REL_DIR_FILES_INCOMING, StorageLocation::getDefault()->getName());
                }
            } ?>
        </div>
    </div>
    <div id="ccm-file-add-remote-tab" style="display: none">
        <h4><?=t("Add from Remote URL")?></h4>


        <form method="post" id="ccm-file-manager-replace-remote" class="form-inline" data-dialog-form="replace-file" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/importers/remote">
            <?=$valt->output('import_remote');?>
            <input type="hidden" name="searchInstance" value="<?=$searchInstance?>" />
            <?= $form->hidden('fID', $f->getFileID()); ?>

            <?=$form->text('url_upload_1', array('style' => 'width:195px'))?>

            <button type="submit" class="btn btn-warning btn-sm"><?=t('Replace')?></button>


        </form>
    </div>
</div>

<script type="text/javascript">
    $(function() {
        $('#ccm-file-manager-replace-incoming,#ccm-file-manager-replace-remote,#ccm-file-manager-replace-upload').concreteAjaxForm();
        ConcreteEvent.subscribe('AjaxFormSubmitSuccess', function(e, data) {
            if (data.form == 'replace-file') {
                ConcreteEvent.publish('FileManagerReplaceFileComplete', {files: data.response.files});
            }
        });
    });
</script>