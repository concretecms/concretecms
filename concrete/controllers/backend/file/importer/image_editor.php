<?php

namespace Concrete\Controller\Backend\File\Importer;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\File\Import\FileImporter;
use Concrete\Core\File\Import\ImportOptions;
use Concrete\Core\File\Service\File as FileService;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Concrete\Core\Validation\CSRF\Token;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class ImageEditor extends AbstractController
{
    public function view(): Response
    {
        $rf = $this->app->make(ResponseFactoryInterface::class);
        try {
            $this->checkCSRF();
            $file = $this->getFile();
            $imageData = $this->decodeRawImageData($this->getRawImageData());
            $this->importData($file, $imageData);

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

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function checkCSRF(): void
    {
        $token = $this->app->make(Token::class);
        if (!$token->validate()) {
            throw new UserMessageException($token->getErrorMessage(), 403);
        }
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

    protected function getFileID(): ?int
    {
        $fID = $this->request->request->get('fID', $this->request->query->get('fID'));

        return $this->app->make(Numbers::class)->integer($fID, 1) ? (int) $fID : null;
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
        if (!preg_match('%^data:image/(png|jpeg);base64,(.+)$%s', $rawImageData, $matches)) {
            throw new UserMessageException(t('Invalid image data'), 400);
        }
        $binaryData = base64_decode($matches[1]);
        if ($binaryData === false) {
            throw new UserMessageException(t('Invalid image data'), 400);
        }

        return $binaryData;
    }

    protected function importData(File $file, string $imageData)
    {
        $fileHelper = $this->app->make(FileService::class);
        $fs = $this->app->make(Filesystem::class);
        $tempFile = $fs->tempnam($fileHelper->getTemporaryDirectory(), 'img');
        try {
            $fileHelper->append($tempFile, $imageData);
            $importer = $this->app->make(FileImporter::class);
            $importer->importLocalFile(
                $tempFile,
                $file->getApprovedVersion()->getFileName(),
                $this->app->make(ImportOptions::class)
                    ->setAddNewVersionTo($file)
                    ->setCanChangeLocalFile(true)
            );
        } finally {
            $fs->remove($tempFile);
        }
    }
}
