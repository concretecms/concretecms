<?php

namespace Concrete\Core\Console\Command;

use Concrete\Core\Console\Command;
use Concrete\Core\Entity\File\File;
use Concrete\Core\File\FileList;
use Concrete\Core\File\Image\Thumbnail\Path\Resolver as ThumbnailPathResolver;
use Concrete\Core\File\Image\Thumbnail\Type\Type as ThumbnailType;
use Concrete\Core\File\Importer;
use Concrete\Core\File\ImportProcessor\AutorotateImageProcessor;
use Concrete\Core\File\ImportProcessor\ConstrainImageProcessor;
use Concrete\Core\File\Type\Type as FileType;
use Concrete\Core\Search\Pagination\PaginationFactory;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Facade;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RescanFilesCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('c5:rescan-files')
            ->setDescription('Rescans all files in the file manager.')
            ->addOption('after',  'a',InputOption::VALUE_REQUIRED, 'Rescan files after a particular file ID.')
            ->addOption('limit',  'l',InputOption::VALUE_REQUIRED, 'Limit the number of files to scan in this batch');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Rescanning files... ');
        $count = 0;
        $list = new FileList();
        $list->ignorePermissions();
        if ($input->getOption('after') !== null) {
            $list->getQueryObject()->andWhere('f.fID > :after');
            $list->getQueryObject()->setParameter('after', $input->getOption('after'));
        }
        $list->sortBy('f.fID', 'asc');
        $app = Facade::getFacadeApplication();
        $config = $app->make('config');
        $em = $app->make(EntityManager::class);
        $paginationFactory = $app->make(PaginationFactory::class);
        $currentFilename = null;
        $currentID = 0;
        try {
            \Cache::disableAll();
            if ($input->getOption('limit') !== null) {
                $pagination = $paginationFactory->createPaginationObject($list);
                $pagination->setMaxPerPage($input->getOption('limit'));
                $results = $pagination->getCurrentPageResults();
            } else {
                $results = $list->getResults();
            }

            foreach ($results as $f) {
                $currentFilename = $f->getFilename();
                $currentID = $f->getFileID();
                $fv = $f->getApprovedVersion();
                $resp = $fv->refreshAttributes(false);
                switch ($resp) {
                    case Importer::E_FILE_INVALID:
                        $errorMessage = t('File could not be found.');
                        throw new \Exception($errorMessage);
                }
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
                $width = (int)$config->get('concrete.file_manager.restrict_max_width');
                $height = (int)$config->get('concrete.file_manager.restrict_max_height');
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
                $output->writeln(t('File rescanned: "%s" (ID: %s)', $currentFilename, $currentID));
                $count++;
                $em->clear();
            }
        } catch (\Exception $e) {
            $output->writeln(t('Unable to rescan file "%s" (ID: %s): %s', $currentFilename, $currentID, $e->getMessage()));
        }
        $output->writeln("{$count} files rescanned.");
    }
}
