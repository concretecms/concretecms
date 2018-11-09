<?php

namespace Concrete\Core\File\ImportProcessor;

use Concrete\Core\Entity\File\Version;
use Concrete\Core\File\Image\Svg\Sanitizer;
use Concrete\Core\File\Image\Svg\SanitizerOptions;
use Concrete\Core\File\Type\Type;

class SvgSanitizerProcessor implements ProcessorInterface
{
    /**
     * SVG sanitizer.
     *
     * @var \Concrete\Core\File\Image\Svg\Sanitizer
     */
    protected $sanitizer;

    /**
     * SVG sanitizer options.
     *
     * @var \Concrete\Core\File\Image\Svg\SanitizerOptions|null
     */
    private $sanitizerOptions;

    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\File\Image\Svg\Sanitizer $sanitizer
     */
    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    /**
     * Set the sanitizer options.
     *
     * @param \Concrete\Core\File\Image\Svg\SanitizerOptions $options
     *
     * @return $this
     */
    public function setSanitizerOptions(SanitizerOptions $options)
    {
        $this->sanitizerOptions = $options;

        return $this;
    }

    /**
     * Get the sanitizer options.
     * If the options weren't previously set, we'll create a new options instance with the default values.
     *
     * @return \Concrete\Core\File\Image\Svg\SanitizerOptions
     */
    public function getSanitizerOptions()
    {
        if ($this->sanitizerOptions === null) {
            $this->sanitizerOptions = SanitizerOptions::create();
        }

        return $this->sanitizerOptions;
    }

    /**
     * Check if a file version should be processed (it must be an SVG image).
     *
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\ImportProcessor\ProcessorInterface::shouldProcess()
     */
    public function shouldProcess(Version $version)
    {
        $versionTypeObject = $version->getTypeObject();

        return $versionTypeObject->getGenericType() == Type::T_IMAGE && $versionTypeObject->isSVG();
    }

    /**
     * Remove potentially harmful elements and attributes from the SVG image.
     *
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\ImportProcessor\ProcessorInterface::process()
     */
    public function process(Version $version)
    {
        $resource = $version->getFileResource();
        $originalData = $resource->read();
        $sanitizedData = $this->sanitizer->sanitizeData($originalData, $this->getSanitizerOptions());
        if ($sanitizedData !== $originalData) {
            $version->updateContents($sanitizedData);
        }
    }
}
