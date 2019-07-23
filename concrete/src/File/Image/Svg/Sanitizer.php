<?php

namespace Concrete\Core\File\Image\Svg;

use DOMDocument;
use DOMElement;
use Exception;
use Illuminate\Filesystem\Filesystem;
use Throwable;

class Sanitizer
{
    /**
     * The Filesystem instance to be used for file operations.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * Initialize the instance.
     *
     * @param \Illuminate\Filesystem\Filesystem $filesystem the Filesystem instance to be used for file operations
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Sanitize a file containing an SVG document.
     *
     * @param string $inputFilename the input filename
     * @param \Concrete\Core\File\Image\Svg\SanitizerOptions $options the sanitizer options (if NULL, we'll use the default ones)
     * @param string $outputFilename the output filename (if empty, we'll overwrite $inputFilename)
     *
     * @throws \Concrete\Core\File\Image\Svg\SanitizerException in case of errors
     */
    public function sanitizeFile($inputFilename, SanitizerOptions $options = null, $outputFilename = '')
    {
        $data = is_string($inputFilename) && $this->filesystem->isFile($inputFilename) ? $this->filesystem->get($inputFilename) : false;
        if ($data === false) {
            throw SanitizerException::create(SanitizerException::ERROR_FAILED_TO_READ_FILE);
        }
        $sanitizedData = $this->sanitizeData($data, $options);
        if ((string) $outputFilename === '') {
            $outputFilename = $inputFilename;
        }
        if ($outputFilename !== $inputFilename || $data !== $sanitizedData) {
            if ($this->filesystem->put($outputFilename, $sanitizedData) === false) {
                throw SanitizerException::create(SanitizerException::ERROR_FAILED_TO_WRITE_FILE);
            }
        }
    }

    /**
     * Sanitize a string containing an SVG document.
     *
     * @param string $data the input filename
     * @param \Concrete\Core\File\Image\Svg\SanitizerOptions $options the sanitizer options (if NULL, we'll use the default ones)
     *
     * @throws \Concrete\Core\File\Image\Svg\SanitizerException in case of errors
     *
     * @return string
     */
    public function sanitizeData($data, SanitizerOptions $options = null)
    {
        $xml = $this->dataToXml($data);

        if ($options === null) {
            $options = new SanitizerOptions();
        }
        $this->processNode($xml->documentElement, $options);

        return $this->xmlToData($xml);
    }

    /**
     * Get the flags to be used when loading the XML.
     *
     * @return int
     */
    protected function getLoadFlags()
    {
        $flags = LIBXML_NONET | LIBXML_NOWARNING;

        foreach ([
            'LIBXML_PARSEHUGE', //  libxml >= 2.7.0
            'LIBXML_HTML_NOIMPLIED', // libxml >= 2.7.7
            'LIBXML_HTML_NODEFDTD', // libxml >= 2.7.8
            'LIBXML_BIGLINES', // libxml >= 2.9.0
        ] as $flagName) {
            if (defined($flagName)) {
                $flags |= constant($flagName);
            }
        }

        return $flags;
    }

    /**
     * Create a DOMDocument instance from a string.
     *
     * @param string $data
     *
     * @throws \Concrete\Core\File\Image\Svg\SanitizerException in case of errors
     *
     * @return \DOMDocument
     */
    protected function dataToXml($data)
    {
        if (!is_string($data)) {
            throw SanitizerException::create(SanitizerException::ERROR_FAILED_TO_PARSE_XML);
        }
        $xml = new DOMDocument();
        $error = null;
        try {
            $loaded = $xml->loadXML($data, $this->getLoadFlags());
        } catch (Exception $x) {
            $error = $x;
        } catch (Throwable $x) {
            $error = $x;
        }
        if ($error !== null || $loaded === false) {
            throw SanitizerException::create(SanitizerException::ERROR_FAILED_TO_PARSE_XML, $error ? $error->getMessage() : '');
        }

        return $xml;
    }

    /**
     * Analyze an element (and all its children), removing selected elements/attributes.
     *
     * @param \DOMElement $element
     * @param \Concrete\Core\File\Image\Svg\SanitizerOptions $options
     */
    protected function processNode(DOMElement $element, SanitizerOptions $options)
    {
        $elementName = strtolower((string) $element->localName);
        if (!in_array($elementName, $options->getElementWhitelist(), true) && in_array($elementName, $options->getUnsafeElements(), true)) {
            $element->parentNode->removeChild($element);
        } else {
            foreach ($element->attributes as $attribute) {
                /* @var \DOMAttr $attribute */
                $attributeName = strtolower((string) $attribute->localName);
                if (!in_array($attributeName, $options->getAttributeWhitelist(), true) && in_array($attributeName, $options->getUnsafeAttributes(), true)) {
                    $element->removeAttribute($attribute->name);
                }
            }
            $childElements = [];
            foreach ($element->childNodes as $childNode) {
                if ($childNode instanceof DOMElement) {
                    $childElements[] = $childNode;
                }
            }
            foreach ($childElements as $childElement) {
                $this->processNode($childElement, $options);
            }
        }
    }

    /**
     * Render a DOMDocument instance as a string.
     *
     * @param \DOMDocument $xml
     *
     * @throws \Concrete\Core\File\Image\Svg\SanitizerException in case of errors
     *
     * @return string
     */
    protected function xmlToData(DOMDocument $xml)
    {
        $data = $xml->saveXML();
        if ($data === false) {
            throw SanitizerException::create(SanitizerException::ERROR_FAILED_TO_GENERATE_XML);
        }

        return $data;
    }
}
