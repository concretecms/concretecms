<?php

namespace Concrete\Core\File\Command;

use Concrete\Core\Entity\File\File as FileEntity;
use Concrete\Core\Entity\File\Version as FileVersion;
use Concrete\Core\File\Image\Thumbnail\Type\Type as ThumbnailType;
use Concrete\Core\Entity\File\Image\Thumbnail\Type\Type as ThumbnailTypeEntity;
use Concrete\Core\File\Image\Thumbnail\Type\Version as ThumbnailVersion;
use Doctrine\ORM\EntityManagerInterface;

class GenerateThumbnailCommandHandler
{

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(
        EntityManagerInterface $em
    )
    {
        $this->entityManager = $em;
    }

    public function __invoke(GeneratedThumbnailCommand $command)
    {
        /** @var FileEntity $f */
        $fileEntity = $this->entityManager->find(FileEntity::class, $command->getFileID());

        if ($fileEntity instanceof FileEntity) {
            $fileVersion = $fileEntity->getVersion($command->getFileVersionID());

            if ($fileVersion instanceof FileVersion) {
                $thumbnailType = ThumbnailType::getByHandle($command->getThumbnailTypeHandle());

                if ($thumbnailType instanceof ThumbnailTypeEntity) {
                    foreach([$thumbnailType->getBaseVersion(), $thumbnailType->getDoubledVersion()] as $thumbnailTypeVersion) {
                        if ($thumbnailTypeVersion instanceof ThumbnailVersion) {
                            $image = $fileVersion->getImagineImage();

                            if ($image) {
                                $imageSize = $image->getSize();

                                unset($image);

                                if ($thumbnailTypeVersion->shouldExistFor($imageSize->getWidth(), $imageSize->getHeight(), $fileEntity)) {
                                    $location = $fileVersion->getFile()->getFileStorageLocationObject();
                                    $filesystem = $location->getFileSystemObject();

                                    if (!$filesystem->has($thumbnailTypeVersion->getFilePath($fileVersion))) {
                                        $fileVersion->generateThumbnail($thumbnailTypeVersion);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }


}