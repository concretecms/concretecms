<?php

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\File\Exception\InvalidDimensionException;
use Concrete\Core\File\File;
use Concrete\Core\File\Image\Thumbnail\Thumbnail;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\View\View;
use Symfony\Component\HttpFoundation\Request;

$view = new View('image-editor/editor');

$app = Application::getFacadeApplication();
/** @var Request $request */
$request = $app->make(Request::class);
/** @var Repository $config */
$config = $app->make(Repository::class);

$file = File::getByID((int)$request->request->get('fID', 1));
$file_version = $file->getVersion((int)$request->request->get('fvID', 1));
$handle = $request->request->get('thumbnail', '');

/* @var Thumbnail[] $thumbnails */

try {
    $thumbnails = $file_version->getThumbnails();
} catch (InvalidDimensionException $e) {

    $view = View::getInstance();
    $view->renderError(
        t('Invalid File Dimensions'),
        t(
            'The dimensions for this image are either unspecified or invalid. Please rescan this file or manually enter' .
            ' the correct dimensions.'));

    return;

} catch (Exception $e) {
    $view = View::getInstance();
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
    $view = View::getInstance();
    $view->renderError(
        t('Unable to find requested thumbnail'),
        t(
            'The thumbnail you requested was not included in the available thumbnails, is your source image smaller ' .
            'than the thumbnail?'));

    return;
}

$saveAreaBackgroundColor = $type_version->getSaveAreaBackgroundColor();

if (empty($saveAreaBackgroundColor)) {
    $saveAreaBackgroundColor = $config->get('concrete.file_manager.images.image_editor_save_area_background_color');
}

$view->addScopeItems([
    'fv' => $file_version,
    'no_bind' => true,
    'settings' => [
        'saveAreaBackgroundColor' => $saveAreaBackgroundColor,
        'saveHeight' => $height,
        'saveWidth' => $width,
        'saveUrl' => (string)Url::to('/tools/required/files/importers/thumbnail'),
        'saveData' => [
            'isThumbnail' => true,
            'fID' => $file_version->getFileID(),
            'fvID' => $file_version->getFileVersionID(),
            'handle' => $handle,
        ],
    ],
]);

echo $view->render();
