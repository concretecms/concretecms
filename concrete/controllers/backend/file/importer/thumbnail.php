<?php

namespace Concrete\Controller\Backend\File\Importer;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\File\Image\Thumbnail\Thumbnail as ImageThumbnail;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class Thumbnail extends AbstractController
{
    public function view(): Response
    {
        $rf = $this->app->make(ResponseFactoryInterface::class);
        try {
            $fileVersion = $this->getFileVersion();
            $thumbnail = $this->getThumbnail($fileVersion);
            $imageData = $this->decodeRawImageData($this->getRawImageData());
            $this->setThumbnailData($fileVersion, $thumbnail, $imageData);

            return $rf->json([
                'error' => 0,
            ]);
        } catch (UserMessageException $x) {
            return $rf->json([
                'error' => 1,
                'code' => $x->getCode() ?: -1,
                'message' => $x->getMessage(),
            ]);
        }
    }

    protected function getFileID(): ?int
    {
        $fID = $this->request->request->get('fID', $this->request->query->get('fID'));

        return $this->app->make(Numbers::class)->integer($fID, 1) ? (int) $fID : null;
    }

    protected function getFileVersionID(): ?int
    {
        $fID = $this->request->request->get('fvID', $this->request->query->get('fvID'));

        return $this->app->make(Numbers::class)->integer($fID, 1) ? (int) $fID : null;
    }

    protected function getThumbnailHandle(): string
    {
        $handle = $this->request->request->get('handle', $this->request->query->get('handle'));

        return is_string($handle) ? $handle : '';
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function getFile(): File
    {
        $fID = $this->getFileID();
        if ($fID === null) {
            throw new UserMessageException(t('Invalid file'), 401);
        }
        $file = $this->app->make(EntityManagerInterface::class)->find(File::class, $fID);
        if ($file === null) {
            throw new UserMessageException(t('Invalid file'), 401);
        }
        $fp = new Checker($file);
        if (!$fp->canWrite()) {
            throw new UserMessageException(t('Access Denied'), 401);
        }
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function getFileVersion(): Version
    {
        $file = $this->getFile();
        $fileVersion = $file->getVersion($this->getFileVersionID());
        if ($fileVersion === null) {
            throw new UserMessageException(t('Invalid file version'), 401);
        }

        return $fileVersion;
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function getThumbnail(Version $fileVersion): ImageThumbnail
    {
        $handle = $this->getThumbnailHandle();
        if ($handle !== '') {
            foreach ($fileVersion->getThumbnails() as $thumbnail) {
                $thumbnailTypeVersion = $thumbnail->getThumbnailTypeVersionObject();
                if ($thumbnailTypeVersion->getHandle() === $handle) {
                    return $thumbnail;
                }
            }
        }
        throw new UserMessageException(t('Invalid thumbnail handle'), 400);
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function getRawImageData(): string
    {
        $rawImageData = $this->request->request->get('imgData', $this->request->query->get('imgData'));
        if (!is_string($rawImageData) || $rawImageData === '') {
            throw new UserMessageException(t('No Data'), 400);
        }

        return $rawImageData;
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function decodeRawImageData(string $rawImageData): string
    {
        $matches = null;
        if (!preg_match('%^data:image/(png|jpeg|gif|xbm|wbmp);base64,(.+)$%s', $rawImageData, $matches)) {
            throw new UserMessageException(t('Invalid image data'), 400);
        }
        $binaryData = base64_decode($matches[1]);
        if ($binaryData === false) {
            throw new UserMessageException(t('Invalid image data'), 400);
        }

        return $binaryData;
    }

    protected function setThumbnailData(Version $fileVersion, ImageThumbnail $thumbnail, string $imageData)
    {
        $fsl = $fileVersion->getFile()->getFileStorageLocationObject();
        /*
         * Clear out the old image, and replace it with this data. This is destructive and not versioned, it definitely needs to
         * be revised.
         */
        $filesystem = $fsl->getFileSystemObject();
        $filesystem->update($thumbnail->getThumbnailTypeVersionObject()->getFilePath($fileVersion), $imageData);
    }
}
