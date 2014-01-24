<?php
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

$fh = Loader::helper('file');
$tmpName = tempnam($fh->getTemporaryDirectory(), 'img');

$fh->append($tmpName, base64_decode(str_replace('data:image/png;base64,', '', $imgData)));

$fi = new FileImporter;
$fi->import($tmpName, $f->getFileName(), $f);
unlink($tmpName);

die('{"error":0}');
