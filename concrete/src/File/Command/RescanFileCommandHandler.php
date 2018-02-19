<?php

namespace Concrete\Core\File\Command;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\File\Importer;
use Concrete\Core\File\ImportProcessor\AutorotateImageProcessor;
use Concrete\Core\File\ImportProcessor\ConstrainImageProcessor;
use Doctrine\ORM\EntityManager;
use Concrete\Core\Entity\File\File as FileEntity;
class RescanFileCommandHandler
{

    protected $entityManager;

    protected $config;

    public function __construct(Repository $config, EntityManager $em)
    {
        $this->config = $config;
        $this->entityManager = $em;
    }

    public function handle(RescanFileCommand $command)
    {
        $f = $this->entityManager->find(FileEntity::class, $command->getFileID());
        $fv = $f->getApprovedVersion();
        $resp = $fv->refreshAttributes(false);
        switch ($resp) {
            case Importer::E_FILE_INVALID:
                $errorMessage = t('File %s could not be found.', $fv->getFilename()) . '<br/>';
                throw new UserMessageException($errorMessage, 404);
        }
        $config = $this->config;
        $newFileVersion = null;
        if ($config->get('concrete.file_manager.images.use_exif_data_to_rotate_images')) {
            $processor = new AutorotateImageProcessor();
            if ($processor->shouldProcess($fv)) {
                if ($newFileVersion === null) {
                    $fv = $newFileVersion = $f->createNewVersion(true);
                }
                $processor->setRescanThumbnails(false);
                $processor->process($newFileVersion);
            }
        }
        $width = (int) $config->get('concrete.file_manager.restrict_max_width');
        $height = (int) $config->get('concrete.file_manager.restrict_max_height');
        if ($width > 0 || $height > 0) {
            $processor = new ConstrainImageProcessor($width, $height);
            if ($processor->shouldProcess($fv)) {
                if ($newFileVersion === null) {
                    $fv = $newFileVersion = $f->createNewVersion(true);
                }
                $processor->setRescanThumbnails(false);
                $processor->process($newFileVersion);
            }
        }
        $fv->rescanThumbnails();
        $fv->releaseImagineImage();
    }


}