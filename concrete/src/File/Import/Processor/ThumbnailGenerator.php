<?php

namespace Concrete\Core\File\Import\Processor;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\File\Import\ImportingFile;
use Concrete\Core\File\Import\ImportOptions;
use Concrete\Core\File\Type\Type as FileType;

class ThumbnailGenerator implements PostProcessorInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Import\Processor\PostProcessorInterface::getPostProcessPriority()
     */
    public function getPostProcessPriority()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Import\Processor\ProcessorInterface::readConfiguration()
     */
    public function readConfiguration(Repository $config)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Import\Processor\PostProcessorInterface::shouldPostProcess()
     */
    public function shouldPostProcess(ImportingFile $file, ImportOptions $options, Version $importedVersion)
    {
        return $file->getFileType()->getGenericType() === FileType::T_IMAGE && $file->getFileType()->isSVG() !== true && $options->isSkipThumbnailGeneration() === false;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Import\Processor\PostProcessorInterface::postProcess()
     */
    public function postProcess(ImportingFile $file, ImportOptions $options, Version $importedVersion)
    {
        $importedVersion->refreshThumbnails(true);
    }
}
