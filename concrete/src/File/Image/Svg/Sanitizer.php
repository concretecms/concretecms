<?php

namespace Concrete\Core\File\Image\Svg;

use DOMDocument;
use DOMElement;
use Exception;
use Throwable;

class Sanitizer
{
    /**
     * Sanitize a file containing a SVG document.
     *
     * @param string $inputFilename The input filename
     * @param \Concrete\Core\File\Image\Svg\SanitizerOptions $options the sanitizer options (if NULL, we'll use the default ones)
     * @param string $outputFilename The output filename (if empty, we'll overwrite $inputFilename)
     * @param throws \Concrete\Core\File\Image\Svg\SanitizerException in case of errors
     */
    public function sanitizeFile($inputFilename, SanitizerOptions $options = null, $outputFilename = '')
    {
        $data = is_string($inputFilename) && is_file($inputFilename) ? @file_get_contents($inputFilename) : false;
        if ($data === false) {
            throw SanitizerException::create(SanitizerException::ERROR_FAILED_TO_READ_FILE);
        }
        $sanitizedData = $this->sanitizeData($data, $options);
        if ((string) $outputFilename === '') {
            $outputFilename = $inputFilename;
        }
        if ($outputFilename !== $inputFilename || $data !== $sanitizedData) {
            if (@file_put_contents($outputFilename, $sanitizedData) === false) {
                throw SanitizerException::create(SanitizerException::ERROR_FAILED_TO_WRITE_FILE);
            }
        }
    }

    /**
     * Sanitize a string containing a SVG document.
     *
     * @param string $data The input filename
     * @param \Concrete\Core\File\Image\Svg\SanitizerOptions $options the sanitizer options (if NULL, we'll use the default ones)
     * @param throws \Concrete\Core\File\Image\Svg\SanitizerException in case of errors
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
        $flags = LIBXML_NONET | LIBXML_NOWARNING | LIBXML_PARSEHUGE | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD;
        if (defined('LIBXML_BIGLINES')) {
            $flags |= LIBXML_BIGLINES;
        }

        return $flags;
    }

    /**
     * @param string $data
     * @param throws \Concrete\Core\File\Image\Svg\SanitizerException in case of errors
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
     * @param \DOMElement $element
     * @param SanitizerOptions $options
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
     * @param \DOMDocument $xml
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
