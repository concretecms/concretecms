<?php

namespace Concrete\Core\Command\Task\Controller;

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Command\Batch\Batch;
use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Command\Task\Runner\BatchProcessTaskRunner;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Command\Task\TaskInterface;
use Concrete\Core\Entity\File\File;
use Concrete\Core\File\Command\GeneratedThumbnailCommand;
use Concrete\Core\File\FileList;
use Concrete\Core\File\Image\Thumbnail\Type\Type;

class GenerateThumbnailsController extends AbstractController
{
    public function getName(): string
    {
        return t('Generate thumbnails');
    }

    public function getDescription(): string
    {
        return t('Recomputes all thumbnails for a file.');
    }

    public function getTaskRunner(TaskInterface $task, InputInterface $input): TaskRunnerInterface
    {
        $fileList = new FileList();

        $thumbnailTypes = Type::getVersionList();
        $batch = Batch::create();

        foreach ($fileList->getResults() as $file) {
            if ($file instanceof File) {
                foreach ($file->getFileVersions() as $fileVersion) {
                    foreach ($thumbnailTypes as $thumbnailType) {
                        if ($fileVersion->getTypeObject()->supportsThumbnails()) {
                            $batch->add(new GeneratedThumbnailCommand((int)$file->getFileID(), (int)$fileVersion->getFileVersionID(), $thumbnailType->getHandle()));
                        }
                    }
                }
            }
        }

        return new BatchProcessTaskRunner($task, $batch, $input, t('Thumbnail generation beginning...'));
    }
}
