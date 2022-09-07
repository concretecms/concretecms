<?php

namespace Concrete\Controller\SinglePage;

use Concrete\Core\Entity\File\Version;
use Concrete\Core\Http\Response;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Controller\PageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Entity\File\File as FileEntity;
use Concrete\Core\File\File;
use Concrete\Core\Routing\RedirectResponse;
use League\Flysystem\FileNotFoundException;
use Exception;

class DownloadFile extends PageController
{
    protected $force = 0;
    /** @var ResponseFactory */
    protected $responseFactory;

    public function on_start()
    {
        parent::on_start();

        $this->responseFactory = $this->app->make(ResponseFactory::class);
    }

    /**
     * @param int $fID File ID
     * @param null|int $rcID
     * @noinspection PhpDocMissingReturnTagInspection
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    public function view($fID = 0, $rcID = null)
    {
        $fUUID = null;

        if (is_string($fID) && uuid_is_valid($fID)) {
            $fUUID = $fID;
            $file = File::getByUUID($fID);
            if ($file instanceof FileEntity) {
                $fID = $file->getFileID();
            } else {
                $fID = null;
            }
        }

        // get the file
        if ($this->app->make('helper/validation/numbers')->integer($fID, 1)) {
            $file = File::getByID($fID);

            if ($file instanceof FileEntity && $file->getFileID() > 0) {
                $rcID = $this->app->make('helper/security')->sanitizeInt($rcID);

                if ($rcID > 0) {
                    $rc = Page::getByID($rcID, 'ACTIVE');

                    if ($rc instanceof Page && !$rc->isError()) {
                        $permissionChecker = new Checker($rc);
                        $responseObject = $permissionChecker->getResponseObject();

                        try {
                            if ($responseObject->validate("view_page")) {
                                $this->set('rc', $rc);
                            }
                        } catch (Exception $err) {
                            // Do Nothing
                        }
                    }
                }

                $permissionChecker = new Checker($file);
                $responseObject = $permissionChecker->getResponseObject();

                try {
                    if (!$responseObject->validate("view_file")) {
                        return false;
                    }
                } catch (Exception $err) {
                    return false;
                }

                if ($file->hasFileUUID() && $file->getFileUUID() !== $fUUID) {
                    // the given uuid is invalid
                    return false;
                }

                // if file password is blank, download
                if (!$file->getPassword()) {
                    if ($this->force) {
                        return $this->force_download($file, $rcID);
                    } else {
                        return $this->download($file, $rcID);
                    }
                }

                $approvedVersion = $file->getApprovedVersion();

                if ($approvedVersion instanceof Version) {
                    // otherwise show the form
                    $this->set('force', $this->force);
                    $this->set('rcID', $rcID);
                    $this->set('fID', $fID);
                    $this->set('filename', $approvedVersion->getFilename());

                    try {
                        $this->set('filesize', $approvedVersion->getFileResource()->getSize());
                    } catch (FileNotFoundException $e) {
                        $this->set('filesize', 0);
                    }
                }
            }
        }
    }

    /**
     * @param int $fID File ID
     * @param null|int $rcID
     * @noinspection PhpDocMissingReturnTagInspection
     */
    public function force($fID = 0, $rcID = null)
    {
        $this->force = true;

        return $this->view($fID, $rcID);
    }

    /**
     * @param int $fID File ID
     * @return bool
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    public function view_inline($fID = 0)
    {
        $fUUID = null;

        if (is_string($fID) && uuid_is_valid($fID)) {
            $fUUID = $fID;
            $file = File::getByUUID($fID);
            if ($file instanceof FileEntity) {
                $fID = $file->getFileID();
            } else {
                $fID = null;
            }
        }

        if ($this->app->make('helper/validation/numbers')->integer($fID, 1)) {
            $file = File::getByID($fID);

            $permissionChecker = new Checker($file);
            $responseObject = $permissionChecker->getResponseObject();

            try {
                if (!$responseObject->validate("view_file")) {
                    return false;
                }
            } catch (Exception $err) {
                return false;
            }

            if ($file->hasFileUUID() && $file->getFileUUID() !== $fUUID) {
                // the given uuid is invalid
                return false;
            }

            if ($file->getPassword()) {
                return false;
            }

            $approvedVersion = $file->getApprovedVersion();

            if ($approvedVersion instanceof Version) {
                $mimeType = $approvedVersion->getMimeType();
                if (is_string($mimeType) &&
                    (
                        $mimeType === "text/plain" || $mimeType === "application/pdf" ||
                        (strpos($mimeType, "/") > 0 && in_array(explode("/", $mimeType)[0], ["image", "video"]))
                    )
                ) {
                    header("Content-type: $mimeType");
                    echo $approvedVersion->getFileContents();
                    $this->app->shutdown();
                } else {
                    $this->force_download($file);
                }
            }
        }
    }

    /**
     * @param int $fID File ID
     * @return RedirectResponse|false|\Symfony\Component\HttpFoundation\Response|void
     */
    public function submit_password($fID = 0)
    {
        $fUUID = null;

        if (is_string($fID) && uuid_is_valid($fID)) {
            $fUUID = $fID;
            $file = File::getByUUID($fID);
            if ($file instanceof FileEntity) {
                $fID = $file->getFileID();
            } else {
                $fID  = null;
            }
        }

        if ($this->app->make('helper/validation/numbers')->integer($fID, 1)) {
            $f = File::getByID($fID);

            $rcID = $this->post('rcID');
            $rcID = $this->app->make('helper/security')->sanitizeInt($rcID);

            if ($f->getPassword() == $this->post('password')) {
                if ($this->post('force')) {
                    return $this->force_download($f);
                } else {
                    return $this->download($f);
                }
            }

            $this->set('error', t("Password incorrect. Please try again."));
            $this->set('force', ($this->post('force') ? 1 : 0));

            if ($fUUID !== null) {
                $this->view($fUUID, $rcID);
            } else {
                $this->view($fID, $rcID);
            }
        }
    }

    /**
     * @param FileEntity $file
     * @param null|int $rcID
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response|void
     */
    protected function download(FileEntity $file, $rcID = null)
    {
        $file->trackDownload($rcID);
        $fsl = $file->getFileStorageLocationObject();
        $configuration = $fsl->getConfigurationObject();
        $fv = $file->getVersion();

        if ($configuration->hasPublicURL()) {
            return $this->responseFactory->redirect($fv->getURL(), Response::HTTP_TEMPORARY_REDIRECT)->send();
        } else {
            /** @noinspection PhpDeprecationInspection */
            return $fv->forceDownload();
        }
    }

    /**
     * Forces the download of a file and shuts down.
     * Returns null if approved version wasn't found.
     *
     * @param FileEntity $file
     * @param null|int $rcID
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    protected function force_download(FileEntity $file, $rcID = null)
    {
        $file->trackDownload($rcID);

        $approvedVersion = $file->getApprovedVersion();

        if ($approvedVersion instanceof Version) {
            /** @noinspection PhpDeprecationInspection */
            return $approvedVersion->forceDownload();
        }
    }
}
