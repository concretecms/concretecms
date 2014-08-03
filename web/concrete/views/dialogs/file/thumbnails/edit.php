<?php
use Concrete\Core\File\Image\Thumbnail\Thumbnail;

$view = new View('image-editor/editor');

$file = File::getByID(intval(Request::request('fID', 1)));
$file_version = $file->getVersion(intval(Request::request('fvID', 1)));

$handle = Request::request('thumbnail', '');

/** @var Thumbnail[] $thumbnails */
$thumbnails = $file_version->getThumbnails();
$type_version = null;
foreach ($thumbnails as $thumb) {
    $type_version = $thumb->getThumbnailTypeVersionObject();
    if ($type_version->getHandle() === $handle) {
        break;
    }
}
$height = $type_version->getHeight();
$width = $type_version->getWidth();

$view->addScopeItems(array('fv' => $file_version, 'no_bind' => true, 'settings' => array(
    'saveHeight' => $height,
    'saveWidth' => $width,
    'saveUrl' => BASE_URL . DIR_REL . '/index.php/tools/required/files/importers/thumbnail',
    'saveData' => array(
        'isThumbnail' => true,
        'fID' => $file_version->getFileID(),
        'fvID' => $file_version->getFileVersionID(),
        'handle' => $handle
    ))
));
echo $view->render();
