<?php defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<?php
$c = Page::getCurrentPage();
use Concrete\Core\Tree\Node\Type\File as FileNode;
use Concrete\Core\Tree\Node\Type\FileFolder as FileFolderNode;

?>

<?php
$view->inc('view_header.php');
?>

<div class="ccm-block-document-library-icon-grid">

<?php if (count($results)) {?>

    <div class="container">
        <div class="row gx-md-8">
            <?php foreach($results as $result) {
                if (isset($file)) {
                    unset($file);
                }
                if ($result instanceof FileNode) {
                    $file = $result->getTreeNodeFileObject();
                    $type = $file->getTypeObject();
                    if ($type->supportsThumbnails()) {
                        $thumbnail = new \HtmlObject\Image(
                            $file->getThumbnailURL('resource_list_entry'),
                            '', ['class' => 'ccm-image-thumbnail']
                        );
                    } else {
                        $thumbnail = new \HtmlObject\Image(
                            $type->getThumbnail(false),
                            '', ['class' => 'ccm-file-type-icon']
                        );
                    }
                    if ($downloadFileMethod == 'force') {
                        $tagURL = $file->getForceDownloadURL();
                    } else {
                        $tagURL = $file->getDownloadURL();
                    }
                } else if ($result instanceof FileFolderNode) {
                    $thumbnail = new \HtmlObject\Element('i', '', ['class' => 'fas fa-folder']);
                    $tagURL = $controller->getActionURL('navigate', $result->getTreeNodeID());
                }
                ?>
                <div class="col-md-3">
                    <a href="<?=$tagURL?>">
                        <div class="ccm-block-document-library-icon-grid-image">
                            <?=$thumbnail?>
                        </div>
                        <div class="ccm-block-document-library-icon-grid-title"><?=$result->getTreeNodeDisplayName()?></div>
                    </a>

                </div>
            <?php } ?>
        </div>
    </div>

    <?php if (isset($pagination)) { ?>
        <?=$pagination?>
    <?php } ?>

<?php } else { ?>
    <p><?=t('No files found.')?></p>
<?php } ?>

</div>
