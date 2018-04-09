<?php

namespace Concrete\Core\Console\Command;

use Concrete\Core\Console\Command;
use Concrete\Core\Entity\File\File;
use Concrete\Core\File\FileList;
use Concrete\Core\File\Image\Thumbnail\Path\Resolver as ThumbnailPathResolver;
use Concrete\Core\File\Image\Thumbnail\Type\Type as ThumbnailType;
use Concrete\Core\File\Type\Type as FileType;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FillThumbnailsTableCommand extends Command
{
    protected function configure()
    {
        $errExitCode = static::RETURN_CODE_ON_FAILURE;
        $this
            ->setName('c5:fill-thumbnails-table')
            ->setDescription('Populate the thumbnail table with all the files.')
            ->addEnvOption()
            ->setHelp(<<<EOT
Returns codes:
  0 operation completed successfully
  $errExitCode errors occurred
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write('Populating thumbnail table... ');
        $count = 0;
        $app = Application::getFacadeApplication();
        $thumbnailPathResolver = $app->make(ThumbnailPathResolver::class);
        $thumbnailTypeVersions = ThumbnailType::getVersionList();
        foreach ($this->generateFileList() as $file) {
            $fileVersion = $file->getApprovedVersion();
            if ($fileVersion !== null) {
                if ($fileVersion->getTypeObject()->supportsThumbnails()) {
                    $imageWidth = (int) $fileVersion->getAttribute('width');
                    $imageHeight = (int) $fileVersion->getAttribute('height');
                    foreach ($thumbnailTypeVersions as $thumbnailTypeVersion) {
                        if ($thumbnailTypeVersion->shouldExistFor($imageWidth, $imageHeight, $file)) {
                            $thumbnailPathResolver->getPath($fileVersion, $thumbnailTypeVersion);
                            ++$count;
                        }
                    }
                }
            }
        }
        $output->writeln("{$count} thumbnail paths processed.");
    }

    /**
     * @return \Generator|\Concrete\Core\Entity\File\File[]
     */
    protected function generateFileList()
    {
        $app = Application::getFacadeApplication();
        $em = $app->make(EntityManagerInterface::class);
        $list = new FileList();
        $list->filterByType(FileType::T_IMAGE);
        $q = $list->deliverQueryObject()->execute();
        while (false !== ($row = $q->fetch())) {
            $fID = (int) $row['fID'];
            $file = $em->find(File::class, $fID);
            if ($file !== null) {
                yield $file;
            }
        }
    }
}
