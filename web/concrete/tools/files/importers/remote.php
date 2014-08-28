<?php
defined('C5_EXECUTE') or die("Access Denied.");

use \Concrete\Core\File\EditResponse as FileEditResponse;

$u = new User();

$cf = Loader::helper('file');
$fp = FilePermissions::getGlobal();
if (!$fp->canAddFiles()) {
    die(t("Unable to add files."));
}

if (isset($_REQUEST['fID'])) {
    // we are replacing a file
    $fr = File::getByID($_REQUEST['fID']);
} else {
    $fr = false;
}

$r = new FileEditResponse();

$valt = Loader::helper('validation/token');
$file = Loader::helper('file');
Loader::helper('mime');

$error = Loader::helper('validation/error');

// load all the incoming fields into an array
$incoming_urls = array();

if (!function_exists('iconv_get_encoding')) {
    $error->add(t('Remote URL import requires the iconv extension enabled on your server.'));
}

if (!$error->has()) {
    for ($i = 1; $i < 6; $i++) {
        $this_url = trim($_REQUEST['url_upload_' .$i]);

        // did we get anything?
        if (!strlen($this_url))
            continue;

        // validate URL
        try {
            $request = new \Zend\Http\Request();
            $request->setUri($this_url);
            $client = new \Zend\Http\Client();
            $response = $client->dispatch($request);
            $incoming_urls[] = $this_url;
        } catch (\Exception $e) {
            $error->add($e->getMessage());
        }
    }

    if (!$valt->validate('import_remote')) {
        $$error->add($valt->getErrorMessage());
    }

    if (count($incoming_urls) < 1) {
        $error->add(t('You must specify at least one valid URL.'));
    }

}

$import_responses = array();

// if we haven't gotten any errors yet then try to process the form
if (!$error->has()) {
    // itterate over each incoming URL adding if relevant
    foreach ($incoming_urls as $this_url) {
        // try to D/L the provided file
        $request = new \Zend\Http\Request();
        $request->setUri($this_url);
        $client = new \Zend\Http\Client();
        $response = $client->dispatch($request);
        if ($response->isSuccess()) {
            $headers = $response->getHeaders();
            $contentType = $headers->get('ContentType')->getFieldValue();

            $fpath = $file->getTemporaryDirectory();

            // figure out a filename based on filename, mimetype, ???
            if (preg_match('/^.+?[\\/]([-\w%]+\.[-\w%]+)$/', $request->getUri(), $matches)) {
                // got a filename (with extension)... use it
                $fname = $matches[1];
            } else if ($contentType) {
                // use mimetype from http response
                $fextension = Core::make("helper/mime")->mimeToExtension($contentType);
                if ($fextension === false)
                    $error->add(t('Unknown mime-type: %s', $contentType));
                else {
                    // make sure we're coming up with a unique filename
                    do {
                        // make up a filename based on the current date/time, a random int, and the extension from the mime-type
                        $fname = date('Y-m-d_H-i_') . mt_rand(100, 999) . '.' . $fextension;
                    } while (file_exists($fpath.'/'.$fname));
                }
            } //else {
                // if we can't get the filename from the file itself OR from the mime-type I'm not sure there's much else we can do
            //}

            if (strlen($fname)) {
                // write the downloaded file to a temporary location on disk
                $handle = fopen($fpath.'/'.$fname, "w");
                fwrite($handle, $response->getBody());
                fclose($handle);

                // import the file into concrete
                if ($fp->canAddFileType($cf->getExtension($fname))) {
                    $fi = new FileImporter();
                    $resp = $fi->import($fpath.'/'.$fname, $fname, $fr);
                    $r->setMessage(t('File uploaded successfully.'));
                    if (is_object($fr)) {
                        $r->setMessage(t('File replaced successfully.'));
                    }
                } else {
                    $resp = FileImporter::E_FILE_INVALID_EXTENSION;
                }
                if (!($resp instanceof \Concrete\Core\File\Version)) {
                    $error->add($fname . ': ' . FileImporter::getErrorMessage($resp));
                } else {
                    $import_responses[] = $resp;

                    if (!is_object($fr)) {
                        // we check $fr because we don't want to set it if we are replacing an existing file
                        $respf = $resp->getFile();
                        $respf->setOriginalPage($_POST['ocID']);
                    } else {
                        $respf = $fr;
                    }

                }

                // clean up the file
                unlink($fpath.'/'.$fname);
            } else {
                // could not figure out a file name
                $error->add(t(/*i18n: %s is an URL*/'Could not determine the name of the file at %s', $this_url));
            }
        } else {
            // warn that we couldn't download the file
            $error->add(t(/*i18n: %s is an URL*/'There was an error downloading %s', $this_url));
        }
    }
}

$r->setError($error);
if (is_object($respf)) {
    $r->setFile($respf);
}
$r->outputJSON();
