<?php

namespace Concrete\Core\File\Import\Processor;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\File\Import\ImportingFile;
use Concrete\Core\File\Import\ImportOptions;
use Concrete\Core\File\ImportProcessor\ProcessorInterface as LegacyPostProcessorInterface;

final class LegacyPostProcessor implements PostProcessorInterface
{
    /**
     * @var \Concrete\Core\File\ImportProcessor\ProcessorInterface
     */
    private $implementation;

    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\File\ImportProcessor\ProcessorInterface $implementation
     */
    public function __construct(LegacyPostProcessorInterface $implementation)
    {
        $this->implementation = $implementation;
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
     * @see \Concrete\Core\File\Import\Processor\PostProcessorInterface::getPostProcessPriority()
     */
    public function getPostProcessPriority()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Import\Processor\PostProcessorInterface::shouldPostProcess()
     */
    public function shouldPostProcess(ImportingFile $file, ImportOptions $options, Version $importedVersion)
    {
        return $this->implementation->shouldProcess($importedVersion);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Import\Processor\PostProcessorInterface::postProcess()
     */
    public function postProcess(ImportingFile $file, ImportOptions $options, Version $importedVersion)
    {
        $this->implementation->process($importedVersion);
    }
}
