<?php

namespace Concrete\Core\File\Document\Xml;

use DOMDocument;
use DOMXPath;
use Exception;
use Illuminate\Filesystem\Filesystem;
use Throwable;

/**
 * Sanitizes XML documents that are not otherwise treated as a specific known
 * format (eg SVG), removing constructs that a browser could use to execute
 * active content when the file is opened directly (same-origin) instead of
 * being processed by the application.
 *
 * In particular, this strips:
 * - processing instructions (eg <?xml-stylesheet?>), which browsers can use to
 *   apply an (attacker-controlled) XSLT stylesheet client-side and emit HTML
 *   and JavaScript in the origin serving the file;
 * - DOCTYPE declarations, which can declare external entities or reference
 *   external DTD subsets.
 */
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
     * Check if an XML file contains nodes to be sanitized.
     *
     * @param string $inputFilename the input filename
     *
     * @return array
     *
     * @example <pre><code>
     * [
     *     'processing_instructions' => [
     *         'xml-stylesheet' => 1,
     *     ],
     *     'doctype' => 1,
     * ]
     * </code></pre>
     */
    public function checkFile($inputFilename)
    {
        $data = $this->fileToData($inputFilename);

        return $this->checkData($data);
    }

    /**
     * Check if a string containing an XML document contains nodes to be sanitized.
     *
     * @param string $data the string containing an XML document
     *
     * @return array
     */
    public function checkData($data)
    {
        $removedNodes = [];
        $this->sanitizeData($data, $removedNodes);

        return $removedNodes;
    }

    /**
     * Sanitize a file containing an XML document.
     *
     * @param string $inputFilename the name of the file containing an XML document
     * @param string $outputFilename the output filename (if empty, we'll overwrite $inputFilename)
     * @param array $removedNodes will contain the list of removed nodes
     *
     * @throws \Concrete\Core\File\Document\Xml\SanitizerException in case of errors
     */
    public function sanitizeFile($inputFilename, $outputFilename = '', array &$removedNodes = [])
    {
        $data = $this->fileToData($inputFilename);
        $removedNodes = [];
        $sanitizedData = $this->sanitizeData($data, $removedNodes);
        if ((string) $outputFilename === '') {
            $outputFilename = $inputFilename;
        }

        if ($this->filesystem->put($outputFilename, $sanitizedData) === false) {
            throw SanitizerException::create(SanitizerException::ERROR_FAILED_TO_WRITE_FILE);
        }
    }

    /**
     * Sanitize a string containing an XML document.
     *
     * @param string $data the data to be sanitized
     * @param array $removedNodes will contain the list of removed nodes
     *
     * @throws \Concrete\Core\File\Document\Xml\SanitizerException in case of errors
     *
     * @return string
     */
    public function sanitizeData($data, array &$removedNodes = [])
    {
        $xml = $this->dataToXml($data);
        $removedNodes = [];
        $this->sanitizeXml($xml, $removedNodes);

        return $this->xmlToData($xml);
    }

    /**
     * Sanitize a DOMDocument instance.
     *
     * @param \DOMDocument $xml
     * @param array $removedNodes will contain the list of removed nodes
     */
    protected function sanitizeXml(DOMDocument $xml, array &$removedNodes)
    {
        // Remove every processing instruction found anywhere in the document.
        // This covers the xml-stylesheet instruction, which browsers use to locate an XSLT
        // (or CSS) stylesheet to apply client-side: an attacker-controlled
        // stylesheet referenced this way can emit arbitrary HTML/JavaScript
        // that executes in the origin serving this XML file.
        $xpath = new DOMXPath($xml);
        $instructions = $xpath->query('//processing-instruction()');
        if ($instructions !== false) {
            foreach ($instructions as $instruction) {
                /** @var \DOMProcessingInstruction $instruction */
                $target = strtolower((string) $instruction->target);
                if (isset($removedNodes['processing_instructions'][$target])) {
                    ++$removedNodes['processing_instructions'][$target];
                } else {
                    $removedNodes['processing_instructions'][$target] = 1;
                }
                if ($instruction->parentNode !== null) {
                    $instruction->parentNode->removeChild($instruction);
                }
            }
        }

        // Remove any DOCTYPE declaration: it can declare (or reference) external
        // entities or DTD subsets that could be abused to affect rendering.
        if ($xml->doctype !== null) {
            $xml->removeChild($xml->doctype);
            if (isset($removedNodes['doctype'])) {
                ++$removedNodes['doctype'];
            } else {
                $removedNodes['doctype'] = 1;
            }
        }
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
     * @throws \Concrete\Core\File\Document\Xml\SanitizerException in case of errors
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
     * @throws \Concrete\Core\File\Document\Xml\SanitizerException in case of errors
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
     * @throws \Concrete\Core\File\Document\Xml\SanitizerException in case of errors
     *
     * @return \DOMDocument
     */
    protected function dataToXml($data)
    {
        if (!is_string($data)) {
            throw SanitizerException::create(SanitizerException::ERROR_FAILED_TO_PARSE_XML);
        }

        // In PHP 8.0 and later, PHP uses libxml versions from 2.9.0, libxml_disable_entity_loader is deprecated.
        // (it's safe to not call it because we don't set LIBXML_NOENT)
        $disabled = PHP_VERSION_ID >= 80000 ? null : libxml_disable_entity_loader(true);
        $xml = new DOMDocument();

        $error = null;
        try {
            $loaded = $xml->loadXML($data, $this->getLoadFlags());
        } catch (Exception $x) {
            $error = $x;
        } catch (Throwable $x) {
            $error = $x;
        } finally {
            if ($disabled !== null) {
                libxml_disable_entity_loader($disabled);
            }
        }

        if ($error !== null || $loaded === false) {
            throw SanitizerException::create(SanitizerException::ERROR_FAILED_TO_PARSE_XML, $error ? $error->getMessage() : '');
        }

        return $xml;
    }

    /**
     * Render a DOMDocument instance as a string.
     *
     * @param \DOMDocument $xml
     *
     * @throws \Concrete\Core\File\Document\Xml\SanitizerException in case of errors
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