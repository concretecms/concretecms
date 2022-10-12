<?php

namespace Concrete\Core\File\Image\Svg;

use DOMDocument;
use DOMElement;
use Exception;
use Illuminate\Filesystem\Filesystem;
use Throwable;
use enshrined\svgSanitize\Sanitizer as EnshrinedSvgSanitizer;

class Sanitizer
{
    /**
     * The Filesystem instance to be used for file operations.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * 3rd party SVG Sanitizer for additional checkups.
     *
     * @var EnshrinedSvgSanitizer
     */
    protected $enshrinedSvgSanitizer;

    /**
     * Initialize the instance.
     *
     * @param \Illuminate\Filesystem\Filesystem $filesystem the Filesystem instance to be used for file operations
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        $this->enshrinedSvgSanitizer = new EnshrinedSvgSanitizer();
    }

    /**
     * Check if a file is a valid XML file.
     *
     * @param string $filename
     *
     * @return bool
     */
    public function fileContainsValidXml($filename)
    {
        try {
            $this->fileToXml($filename);
        } catch (SanitizerException $x) {
            return false;
        }

        return true;
    }

    /**
     * Check if a string contains valid XML data.
     *
     * @param string $data
     *
     * @return bool
     */
    public function dataContainsValidXml($data)
    {
        try {
            $this->dataToXml($data);
        } catch (SanitizerException $x) {
            return false;
        }

        return true;
    }

    /**
     * Check if an SVG file contain nodes to be sanitized.
     *
     * @param string $inputFilename the input filename
     * @param \Concrete\Core\File\Image\Svg\SanitizerOptions|null $options the sanitizer options (if NULL, we'll use the default ones)
     *
     * @return array
     *
     * @example <pre><code>
     * [
     *     'attributes' => [
     *         'onload' => 1,
     *         'onclick => 3,
     *     ],
     *     'elements' => [
     *         'script' => 2,
     *     ],
     * ]
     * </code></pre>
     */
    public function checkFile($inputFilename, SanitizerOptions $options = null)
    {
        $data = $this->fileToData($inputFilename);

        return $this->checkData($data, $options);
    }

    /**
     * Check if a string containing an SVG document contains nodes to be sanitized.
     *
     * @param string $data the string containing an SVG document
     * @param \Concrete\Core\File\Image\Svg\SanitizerOptions|null $options the sanitizer options (if NULL, we'll use the default ones)
     *
     * @return array
     *
     * @example <pre><code>
     * [
     *     'attributes' => [
     *         'onload' => 1,
     *         'onclick => 3,
     *     ],
     *     'elements' => [
     *         'script' => 2,
     *     ],
     * ]
     * </code></pre>
     */
    public function checkData($data, SanitizerOptions $options = null)
    {
        $removedNodes = [];
        $this->sanitizeData($data, $options, $removedNodes);

        return $removedNodes;
    }

    /**
     * Sanitize a file containing an SVG document.
     *
     * @param string $inputFilename the name of the file containing an SVG document
     * @param \Concrete\Core\File\Image\Svg\SanitizerOptions|null $options the sanitizer options (if NULL, we'll use the default ones)
     * @param string $outputFilename the output filename (if empty, we'll overwrite $inputFilename)
     * @param array $removedNodes will contain the list removed elements/attributes
     *
     * @throws \Concrete\Core\File\Image\Svg\SanitizerException in case of errors
     */
    public function sanitizeFile($inputFilename, SanitizerOptions $options = null, $outputFilename = '', array &$removedNodes = [])
    {
        $data = $this->fileToData($inputFilename);
        $removedNodes = [];
        $sanitizedData = $this->sanitizeData($data, $options, $removedNodes);
        if ((string) $outputFilename === '') {
            $outputFilename = $inputFilename;
        }

        if ($this->filesystem->put($outputFilename, $sanitizedData) === false) {
            throw SanitizerException::create(SanitizerException::ERROR_FAILED_TO_WRITE_FILE);
        }
    }

    /**
     * Sanitize a string containing an SVG document.
     *
     * @param string $data the data to be sanitized
     * @param \Concrete\Core\File\Image\Svg\SanitizerOptions|null $options the sanitizer options (if NULL, we'll use the default ones)
     * @param array $removedNodes will contain the list removed elements/attributes
     *
     * @throws \Concrete\Core\File\Image\Svg\SanitizerException in case of errors
     *
     * @return string
     */
    public function sanitizeData($data, SanitizerOptions $options = null, array &$removedNodes = [])
    {
        $xml = $this->dataToXml($data);
        $removedNodes = [];
        $this->sanitizeXml($xml, $removedNodes, $options);

        return $this->enshrinedSvgSanitizer->sanitize($this->xmlToData($xml));
    }

    /**
     * Sanitize a DOMDocument instance.
     *
     * @param \DOMDocument $xml
     * @param array $removedNodes will contain the list removed elements/attributes
     * @param \Concrete\Core\File\Image\Svg\SanitizerOptions|null $options the sanitizer options (if NULL, we'll use the default ones)
     *
     * @throws \Concrete\Core\File\Image\Svg\SanitizerException in case of errors
     */
    protected function sanitizeXml(DOMDocument $xml, array &$removedNodes, SanitizerOptions $options = null)
    {
        if ($options === null) {
            $options = new SanitizerOptions();
        }
        $this->processNode($xml->documentElement, $options, $removedNodes);
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
     * Reads a file.
     *
     * @param string $filename
     *
     * @throws \Concrete\Core\File\Image\Svg\SanitizerException in case of errors
     *
     * @return string
     */
    protected function fileToData($filename)
    {
        $data = is_string($filename) && $this->filesystem->isFile($filename) ? $this->filesystem->get($filename) : false;
        if ($data === false) {
            throw SanitizerException::create(SanitizerException::ERROR_FAILED_TO_READ_FILE);
        }

        return $data;
    }

    /**
     * Create a DOMDocument instance from a file name.
     *
     * @param string $filename
     *
     * @throws \Concrete\Core\File\Image\Svg\SanitizerException in case of errors
     *
     * @return \DOMDocument
     */
    protected function fileToXml($filename)
    {
        return $this->dataToXml($this->fileToData($filename));
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

        $disabled = libxml_disable_entity_loader(true);
        $xml = new DOMDocument();

        $error = null;
        try {
            $loaded = $xml->loadXML($data, $this->getLoadFlags());
        } catch (Exception $x) {
            $error = $x;
        } catch (Throwable $x) {
            $error = $x;
        } finally {
            libxml_disable_entity_loader($disabled);
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
     * @param array $removedNodes tracks the removed elements/attributes
     */
    protected function processNode(DOMElement $element, SanitizerOptions $options, array &$removedNodes)
    {
        $elementName = strtolower((string) $element->localName);
        if (!in_array($elementName, $options->getElementAllowlist(), true) && in_array($elementName, $options->getUnsafeElements(), true)) {
            $element->parentNode->removeChild($element);
            if (isset($removedNodes['elements'][$elementName])) {
                ++$removedNodes['elements'][$elementName];
            } else {
                $removedNodes['elements'][$elementName] = 1;
            }
        } else {
            foreach ($element->attributes as $attribute) {
                /* @var \DOMAttr $attribute */
                $attributeName = strtolower((string) $attribute->localName);
                if (!in_array($attributeName, $options->getAttributeAllowlist(), true) && in_array($attributeName, $options->getUnsafeAttributes(), true)) {
                    $element->removeAttribute($attribute->name);
                    if (isset($removedNodes['attributes'][$attributeName])) {
                        ++$removedNodes['attributes'][$attributeName];
                    } else {
                        $removedNodes['attributes'][$attributeName] = 1;
                    }
                }
            }
            $childElements = [];
            foreach ($element->childNodes as $childNode) {
                if ($childNode instanceof DOMElement) {
                    $childElements[] = $childNode;
                }
            }
            foreach ($childElements as $childElement) {
                $this->processNode($childElement, $options, $removedNodes);
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
