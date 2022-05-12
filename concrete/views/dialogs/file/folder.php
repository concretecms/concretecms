<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\View\View;
use HtmlObject\Input;
use Concrete\Controller\Dialog\File\Folder;

/** @var Folder $controller */
$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);

$folderID = null;
if (isset($f) && (is_object($f))) {
    $fileFolderObject = $f->getFileFolderObject();
    if ($fileFolderObject) {
        $folderID = $fileFolderObject->getTreeNodeID();
    }
}
?>

<form method="post" data-dialog-form="move-to-folder" action="<?php echo $controller->action('submit') ?>">
    <div class="ccm-ui">
        <?php
            /** @noinspection PhpUnhandledExceptionInspection */
            View::element('files/move_to_folder', ['folderID' => $folderID]);
        ?>
    </div>

    <div class="dialog-buttons">
        <button class="btn btn-secondary float-start" data-dialog-action="cancel">
            <?php echo t('Cancel') ?>
        </button>

        <button type="button" data-dialog-action="submit" class="btn btn-primary float-end">
            <?php echo t('Save') ?>
        </button>
    </div>
</form>

<script>
    (function ($) {
        $(function () {
            ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess.updateFolder');
            ConcreteEvent.subscribe('AjaxFormSubmitSuccess.updateFolder', function (e, data) {
                if (data.form === 'move-to-folder') {
                    ConcreteEvent.publish('FolderUpdateRequestComplete', {
                        'folder': data.response.folder
                    });
                }
            });
        });
    })(jQuery);
</script>
