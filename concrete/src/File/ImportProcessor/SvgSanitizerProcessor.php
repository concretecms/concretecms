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
     * @var \Concrete\Core\File\Image\Svg\SanitizerOptions
     */
    protected $sanitizerOptions;

    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\File\Image\Svg\Sanitizer $sanitizer
     * @param SanitizerOptions $sanitizerOptions
     */
    public function __construct(Sanitizer $sanitizer, SanitizerOptions $sanitizerOptions)
    {
        $this->sanitizer = $sanitizer;
        $this->sanitizerOptions = $sanitizerOptions;
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
        $sanitizedData = $this->sanitizer->sanitizeData($originalData, $this->sanitizerOptions);
        if ($sanitizedData !== $originalData) {
            $version->updateContents($sanitizedData);
        }
    }
}
