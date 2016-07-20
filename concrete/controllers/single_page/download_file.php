<?php
namespace Concrete\Controller\SinglePage;

use PageController;
use Core;
use Page;
use Permissions;
use Concrete\Core\Entity\File\File as FileEntity;
use Concrete\Core\File\File;

class DownloadFile extends PageController
{
    protected $force = 0;

    /**
     * @param int $fID File ID
     * @param null|int $rcID
     */
    public function view($fID = 0, $rcID = null)
    {
        // get the file
        if ($fID > 0 && $this->app->make('helper/validation/numbers')->integer($fID)) {
            $file = File::getByID($fID);
            if ($file instanceof FileEntity && $file->getFileID() > 0) {
                $rcID = $this->app->make('helper/security')->sanitizeInt($rcID);
                if ($rcID > 0) {
                    $rc = Page::getByID($rcID, 'ACTIVE');
                    if (is_object($rc) && !$rc->isError()) {
                        $rcp = new Permissions($rc);
                        if ($rcp->canViewPage()) {
                            $this->set('rc', $rc);
                        }
                    }
                }
                $fp = new Permissions($file);
                if (!$fp->canViewFile()) {
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
                // otherwise show the form
                $this->set('force', $this->force);
                $this->set('rcID', $rcID);
                $this->set('fID', $fID);
                $this->set('filename', $file->getFilename());
                $fre = $file->getFileResource();
                $this->set('filesize', $fre->getSize());
            }
        }
    }

    /**
     * @param int $fID File ID
     * @param null|int $rcID
     */
    public function force($fID = 0, $rcID = null)
    {
        $this->force = true;

        return $this->view($fID, $rcID);
    }

    /**
     * @param int $fID File ID
     */
    public function view_inline($fID = 0)
    {
        if ($fID > 0 && $this->app->make('helper/validation/numbers')->integer($fID)) {
            $file = File::getByID($fID);
            $fp = new Permissions($file);
            if (!$fp->canViewFile()) {
                return false;
            }

            $fre = $file->getFileResource();
            $fsl = $file->getFileStorageLocationObject()->getFileSystemObject();
            $mimeType = $file->getMimeType();
            header("Content-type: $mimeType");
            echo $file->getFileContents();
            $this->app->shutdown();
        }
    }

    /**
     * @param int $fID File ID
     */
    public function submit_password($fID = 0)
    {
        if ($fID > 0 && $this->app->make('helper/validation/numbers')->integer($fID)) {
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

            $this->view($fID, $rcID);
        }
    }

    /**
     * @param \Concrete\Core\Entity\File\File $file
     * @param null|int $rcID
     */
    protected function download(\Concrete\Core\Entity\File\File $file, $rcID = null)
    {
        $filename = $file->getFilename();
        $file->trackDownload($rcID);
        $fsl = $file->getFileStorageLocationObject();
        $configuration = $fsl->getConfigurationObject();
        $fv = $file->getVersion();
        if ($configuration->hasPublicURL()) {
            return \Redirect::url($fv->getURL())->send();
        } else {
            return $fv->forceDownload();
        }
    }

    /**
     * Forces the download of a file and shuts down.
     * Returns null if approved version wasn't found.
     *
     * @param File $file
     * @param null|int $rcID
     */
    protected function force_download($file, $rcID = null)
    {
        $file->trackDownload($rcID);

        // Magic call to approved FileVersion
        return $file->forceDownload();
    }
}
