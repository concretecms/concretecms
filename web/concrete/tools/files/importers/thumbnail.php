<?php

use Concrete\Core\File\Image\Thumbnail\Thumbnail;
use Concrete\Core\File\Version;

defined("C5_EXECUTE") or die("Access Denied.");
$fID = isset($_REQUEST['fID']) ? intval($_REQUEST['fID']) : 0;
if ($fID < 1) {
    die('{"error":1,"code":401,"message":"Invalid File"}');
}

$f = File::getByID($fID);
$fp = new Permissions($f);
if (!$fp->canWrite()) {
    die('{"error":1,"code":401,"message":"Access Denied"}');
}

$imgData = isset($_REQUEST['imgData']) ? $_REQUEST['imgData'] : false;
if (!$imgData) {
    die('{"error":1,"code":400,"message":"No Data"}');
}

/** @var Version $file_version */
$file_version = $f->getVersion(intval(Request::request('fvID', 1)));

$handle = Request::request('handle', '');

/** @var Thumbnail[] $thumbnails */
$thumbnails = $file_version->getThumbnails();
$thumbnail = null;
foreach ($thumbnails as $thumb) {
    $type_version = $thumb->getThumbnailTypeVersionObject();
    if ($type_version->getHandle() === $handle) {
        $thumbnail = $thumb;
        break;
    }
}

if ($thumbnail) {
    /** @var StorageLocation $fsl */
    $fsl = $f->getFileStorageLocationObject();

    /**
     * Clear out the old image, and replace it with this data. This is destructive and not versioned, it definitely needs to
     * be revised.
     */
    $filesystem = $fsl->getFileSystemObject();
    $filesystem->update($type_version->getFilePath($file_version), base64_decode(str_replace('data:image/png;base64,', '', $imgData)));

    die('{"error":0}');
}
die('{"error":1,"code":400,"message":"Invalid thumbnail handle"}');
