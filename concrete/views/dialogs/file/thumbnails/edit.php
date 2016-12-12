<?php

use Concrete\Core\File\Exception\InvalidDimensionException;
use Concrete\Core\File\Image\Thumbnail\Thumbnail;

$view = new View('image-editor/editor');

$file = File::getByID(intval(Request::request('fID', 1)));
/** @var FileVersion $file_version */
$file_version = $file->getVersion(intval(Request::request('fvID', 1)));

$handle = Request::request('thumbnail', '');

/* @var Thumbnail[] $thumbnails */
try {
    $thumbnails = $file_version->getThumbnails();
} catch (InvalidDimensionException $e) {
    $view = \View::getInstance();
    $view->renderError(
        t('Invalid File Dimensions'),
        t(
            'The dimensions for this image are either unspecified or invalid. Please rescan this file or manually enter' .
            ' the correct dimensions.'));

    return;
} catch (\Exception $e) {
    $view = \View::getInstance();
    $view->renderError(
        t('Unknown Error'),
        t('An unknown error occurred while trying to find the thumbnails!'));

    return;
}
$type_version = null;
$temp_version = false;
foreach ($thumbnails as $thumb) {
    $temp_version = $thumb->getThumbnailTypeVersionObject();
    if ($temp_version->getHandle() === $handle) {
        $type_version = $temp_version;
        break;
    }
}
if ($type_version) {
    $height = $type_version->getHeight();
    $width = $type_version->getWidth();
} else {
    $view = \View::getInstance();
    $view->renderError(
        t('Unable to find requested thumbnail'),
        t(
            'The thumbnail you requested was not included in the available thumbnails, is your source image smaller ' .
            'than the thumbnail?'));

    return;
}

$view->addScopeItems(
    array(
        'fv' => $file_version,
        'no_bind' => true,
        'settings' => array(
            'saveHeight' => $height,
            'saveWidth' => $width,
            'saveUrl' => (string) URL::to('/tools/required/files/importers/thumbnail'),
            'saveData' => array(
                'isThumbnail' => true,
                'fID' => $file_version->getFileID(),
                'fvID' => $file_version->getFileVersionID(),
                'handle' => $handle,
            ), ),
    ));
echo $view->render();
