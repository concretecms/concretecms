<?php

namespace Concrete\Core\File\Import\Processor;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\File\Import\ImportingFile;
use Concrete\Core\File\Import\ImportOptions;
use Imagine\Filter\Basic\Autorotate;
use Imagine\Filter\Transformation;
use Imagine\Image\Metadata\ExifMetadataReader;

class ImageAutorotator implements PreProcessorInterface
{
    /**
     * Is this pre-processor enabled?
     *
     * @var bool
     */
    private $enabled = true;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Import\Processor\ProcessorInterface::readConfiguration()
     */
    public function readConfiguration(Repository $config)
    {
        $this->setEnabled($config->get('concrete.file_manager.images.use_exif_data_to_rotate_images'));

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Import\Processor\PreProcessorInterface::getPreProcessPriority()
     */
    public function getPreProcessPriority()
    {
        // This needs to run before the preprocessor that resizes images
        return ImageSizeConstrain::PREPROCESSOR_PRIORITY + 10;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Import\Processor\PreProcessorInterface::shouldPreProcess()
     */
    public function shouldPreProcess(ImportingFile $file, ImportOptions $options)
    {
        if (!$this->isEnabled()) {
            return false;
        }
        if (!ExifMetadataReader::isSupported()) {
            return false;
        }
        switch ($file->getFileType()->getName()) {
            case 'JPEG':
                break;
            default:
                return false;
        }
        $image = $file->getImage();
        if ($image === null) {
            return false;
        }
        $autorotateFilter = new Autorotate();
        $rotationArray = $autorotateFilter->getTransformations($image);

        return count($rotationArray) > 0;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Import\Processor\PreProcessorInterface::preProcess()
     */
    public function preProcess(ImportingFile $file, ImportOptions $options)
    {
        $transformation = new Transformation();
        $transformation->applyFilter($file->getImage(), new Autorotate());
        $file->saveImage();
    }

    /**
     * Is this pre-processor enabled?
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Is this pre-processor enabled?
     *
     * @param bool $value
     *
     * @return $this
     */
    public function setEnabled($value)
    {
        $this->enabled = (bool) $value;

        return $this;
    }
}
