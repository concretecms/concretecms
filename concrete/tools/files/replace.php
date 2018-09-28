<?php defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\File\StorageLocation\StorageLocation;
$u = new User();
$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
$ch = $app->make('helper/concrete/file');
$valt = $app->make('helper/validation/token');
$form = $app->make('helper/form');

$f = File::getByID($_REQUEST['fID']);
$fp = new Permissions($f);
if (!$fp->canEditFileContents()) {
    die(t('Access Denied.'));
}

$searchInstance = isset($_REQUEST['searchInstance']) ? $app->make('helper/text')->entities($_REQUEST['searchInstance']) : '';
?>

<div class="ccm-ui">
    <ul class="nav nav-tabs" id="ccm-file-import-tabs">
        <li class="active"><a href="javascript:void(0)" id="ccm-file-add-computer"><?=t('Add From Computer')?></a></li>
        <li><a href="javascript:void(0)" id="ccm-file-add-incoming"><?=t('Add From Incoming')?></a></li>
        <li><a href="javascript:void(0)" id="ccm-file-add-remote"><?=t('Add Remote Files')?></a></li>
    </ul>

    <script>
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
            <br/>
            <table style="width: 100%;">
                <tr>
                    <td style="width: 100%;">
                        <input type="file" name="Filedata" class="form-control" style="width: 100%; height: auto;">
                        <?=$valt->output('upload');?>
                        <?= $form->hidden('fID', $f->getFileID()); ?>
                    </td>
                    <td>
                        <button type="submit" class="btn btn-warning" style="margin-left: 4px;"><?=t('Upload')?></button>
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <div id="ccm-file-add-incoming-tab" style="display: none">

        <br/>
        <div>
            <?php
            $contents = array();
            $con1 = array();
            $error = false;
            try {
                $con1 = $ch->getIncomingDirectoryContents();
            } catch (\Exception $e) {
                $error = t('Unable to get contents of incoming/ directory');
                $error .= '<br>';
                $error .= $e->getMessage();
            }
            foreach ($con1 as $con) {
                $contents[$con['basename']] = $con['basename'];
            }
            if (count($contents) > 0) { ?>
                <form method="post" id="ccm-file-manager-replace-incoming" class="form-inline" data-dialog-form="replace-file" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/importers/incoming">
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 100%;">
                                <?= $form->select('send_file', $contents, array('style' => 'width: 100%;'));?>
                            </td>
                            <td>
                                <button type="submit" class="btn btn-warning" style="margin-left: 4px;"><?=t('Replace')?></button>
                                <?= $form->hidden('fID', $f->getFileID()); ?>
                                <?=$valt->output('import_incoming'); ?>
                            </td>
                        </tr>
                    </table>
                </form>
            <?php
            } else {
                if ($error) { ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php
                } else {
                    echo t('No files found in %s for the storage location "%s".', REL_DIR_FILES_INCOMING, StorageLocation::getDefault()->getName());
                }
            }
            ?>
        </div>
    </div>

    <div id="ccm-file-add-remote-tab" style="display: none">

        <form method="post" id="ccm-file-manager-replace-remote" class="form-inline" data-dialog-form="replace-file" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/importers/remote">
            <br/>
            <table style="width: 100%;">
                <tr>
                    <td style="width: 100%;">
                        <?=$valt->output('import_remote');?>
                        <input type="hidden" name="searchInstance" value="<?=$searchInstance?>" />
                        <?= $form->hidden('fID', $f->getFileID()); ?>
                        <?=$form->text('url_upload_1', array('style' => 'width: 100%;'))?>
                    </td>
                    <td>
                        <button type="submit" class="btn btn-warning" style="margin-left: 4px;"><?=t('Replace')?></button>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>

<script>
$(function() {
    $('#ccm-file-manager-replace-incoming,#ccm-file-manager-replace-remote,#ccm-file-manager-replace-upload').concreteAjaxForm();
    ConcreteEvent.subscribe('AjaxFormSubmitSuccess', function(e, data) {
        if (data.form == 'replace-file') {
            ConcreteEvent.publish('FileManagerReplaceFileComplete', {files: data.response.files});
        }
    });
});
</script>
