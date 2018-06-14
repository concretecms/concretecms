<?php

namespace Concrete\Core\Page\Sitemap;

use Concrete\Core\Application\Application;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Page\Sitemap\Element\SitemapHeader;
use Concrete\Core\Page\Sitemap\Element\SitemapPage;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class SitemapWriter
{
    /**
     * Write mode: automatic.
     *
     * @var int
     */
    const MODE_AUTO = 0;

    /**
     * Write mode: use less memory (the on_sitemap_xml_ready event won't be called).
     *
     * @var int
     */
    const MODE_LOWMEMORY = 1;

    /**
     * Write mode: use more memory (the on_sitemap_xml_ready event will be called).
     *
     * @var int
     */
    const MODE_HIGHMEMORY = 2;

    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * The write mode.
     *
     * @var int
     */
    protected $mode = self::MODE_AUTO;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $director;

    /**
     * @var string
     */
    protected $indenter = '  ';

    /**
     * @var string
     */
    protected $lineTerminator = "\n";

    /**
     * @var \Concrete\Core\Page\Sitemap\SitemapGenerator|null
     */
    private $sitemapGenerator = null;

    /**
     * @var string
     */
    private $outputFilename = '';

    /**
     * @var string
     */
    private $temporaryDirectory = '';

    public function __construct(Application $app, Filesystem $filesystem, EventDispatcherInterface $director)
    {
        $this->app = $app;
        $this->filesystem = $filesystem;
        $this->director = $director;
    }

    /**
     * Get the write mode.
     *
     * @return int One of the SitemapWriter::MODE_... constants.
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Set the write mode.
     *
     * @param int $mode One of the SitemapWriter::MODE_... constants.
     *
     * @return $this
     */
    public function setMode($mode)
    {
        $this->mode = (int) $mode;

        return $this;
    }

    /**
     * Get the string used to indent the XML when the mode is set to MODE_LOWMEMORY.
     *
     * @return string
     */
    public function getIndenter()
    {
        return $this->indenter;
    }

    /**
     * Set the string used to indent the XML when the mode is set to MODE_LOWMEMORY.
     *
     * @param string $indenter
     *
     * @return $this
     */
    public function setIndenter($indenter)
    {
        $this->indenter = (string) $indenter;

        return $this;
    }

    /**
     * Get the string used as new-line terminator in the XML when the mode is set to MODE_LOWMEMORY.
     *
     * @return string
     */
    public function getLineTerminator()
    {
        return $this->lineTerminator;
    }

    /**
     * Get the string used as new-line terminator in the XML when the mode is set to MODE_LOWMEMORY.
     *
     * @param string $lineTerminator
     *
     * @return $this
     */
    public function setLineTerminator($lineTerminator)
    {
        $this->lineTerminator = (string) $lineTerminator;

        return $this;
    }

    /**
     * Get the SitemapGenerator instance to be used.
     *
     * @return \Concrete\Core\Page\Sitemap\SitemapGenerator
     */
    public function getSitemapGenerator()
    {
        if ($this->sitemapGenerator === null) {
            $this->sitemapGenerator = $this->app->make(SitemapGenerator::class);
        }

        return $this->sitemapGenerator;
    }

    /**
     * Set the SitemapGenerator instance to be used.
     *
     * @param SitemapGenerator $sitemapGenerator
     *
     * @return $this
     */
    public function setSitemapGenerator(SitemapGenerator $sitemapGenerator)
    {
        $this->sitemapGenerator = $sitemapGenerator;

        return $this;
    }

    /**
     * Set the path to the sitemap to be generated.
     *
     * @param string $outputFilename
     *
     * @return $this
     */
    public function setOutputFilename($outputFilename)
    {
        $this->outputFilename = (string) $outputFilename;

        return $this;
    }

    /**
     * Get the path to the sitemap to be generated.
     *
     * @return string
     */
    public function getOutputFilename()
    {
        $result = $this->outputFilename;
        if ($result === '') {
            $config = $this->app->make('config');
            $relativeName = '/' . ltrim(str_replace(DIRECTORY_SEPARATOR, '/', (string) $config->get('concrete.sitemap_xml.file')), '/');
            if ($relativeName === '/') {
                $relativeName = '/sitemap.xml';
            }
            $result = rtrim(DIR_BASE, '/') . $relativeName;
        }

        return $result;
    }

    /**
     * Get the URL of the sitemap corresponding to the output file name.
     *
     * @return string returns an empty string if the output file name is not under the web root
     */
    public function getSitemapUrl()
    {
        $outputFilename = $this->getOutputFilename();
        if (strpos($outputFilename, DIR_BASE . '/') === 0) {
            $result = (string) $this->getSitemapGenerator()->resolveUrl([substr($outputFilename, strlen(DIR_BASE))]);
        } else {
            $result = '';
        }

        return $result;
    }

    /**
     * Generate the sitemap.
     *
     * @param callable|null $pulse a callback function to be called every time a new sitemap element will be processed
     */
    public function generate(callable $pulse = null)
    {
        $indenter = $this->getIndenter();
        $lineTerminator = $this->getLineTerminator();
        $dispatchXmlNewPage = $this->director->hasListeners('on_sitemap_xml_addingpage');
        $dispatchXmlReady = $this->director->hasListeners('on_sitemap_xml_ready');
        $mode = $this->getMode();
        if ($mode !== static::MODE_HIGHMEMORY && $mode !== static::MODE_LOWMEMORY) {
            $mode = $dispatchXmlReady ? static::MODE_HIGHMEMORY : static::MODE_LOWMEMORY;
        }
        $outputFilename = $this->getOutputFilename();
        $this->checkOutputFilename($outputFilename);
        $allGood = false;
        $fd = null;
        $tempFilename = @tempnam($this->getTemporaryDirectory(), 'sitemap');
        if ($tempFilename === false) {
            throw new UserMessageException(t('Unable to create a temporary file.'));
        }
        try {
            // fopen/fwrite/fclose is one order of magnitude faster than $this->filesystem->append
            $fd = fopen($tempFilename, 'wb');
            $xmlDocument = null;
            foreach ($this->sitemapGenerator->generateContents() as $element) {
                if ($pulse !== null) {
                    $pulse($element);
                }
                if ($dispatchXmlNewPage && $element instanceof SitemapPage) {
                    $this->director->dispatch('on_sitemap_xml_addingpage', new GenericEvent(['sitemapPage' => $element]));
                }
                if ($mode === static::MODE_HIGHMEMORY) {
                    if ($element instanceof SitemapHeader) {
                        $xmlDocument = $element->toXmlElement();
                    } else {
                        $element->toXmlElement($xmlDocument);
                    }
                } else {
                    $lines = $element->toXmlLines($indenter);
                    if ($lines !== null) {
                        $lines = implode($lineTerminator, $lines) . $lineTerminator;
                        if (@fwrite($fd, $lines) === false) {
                            throw new UserMessageException(t('Failed to write to a temporary file.'));
                        }
                    }
                }
            }
            if ($mode === static::MODE_HIGHMEMORY) {
                if ($dispatchXmlReady) {
                    $this->director->dispatch('on_sitemap_xml_addingpage', new GenericEvent(['xmlDoc' => $xmlDocument]));
                }
                $dom = dom_import_simplexml($xmlDocument)->ownerDocument;
                unset($xmlDocument);
                $dom->formatOutput = true;
                if (@fwrite($fd, $dom->saveXML()) === false) {
                    throw new UserMessageException(t('Failed to write to a temporary file.'));
                }
            }
            fflush($fd);
            fclose($fd);
            $fd = null;
            if (!$this->filesystem->move($tempFilename, $outputFilename)) {
                throw new UserMessageException(t('Failed to create the sitemap file.'));
            }
            $allGood = true;
        } finally {
            if (!$allGood) {
                if ($fd !== null) {
                    @fclose($fd);
                    $fd = null;
                }
                $this->filesystem->delete([$tempFilename]);
            }
        }
        @chmod($outputFilename, 0644);
    }

    /**
     * Get the temporary directory to be used during the sitemap generation.
     *
     * @return string
     */
    protected function getTemporaryDirectory()
    {
        $result = $this->temporaryDirectory;
        if ($result === '') {
            $fileHelper = $this->app->make('helper/file');
            $temporaryDirectory = (string) $fileHelper->getTemporaryDirectory();
            if ($temporaryDirectory === '' || !$this->filesystem->isDirectory($temporaryDirectory)) {
                throw new UserMessageException(t('Unable to determine the temporary directory.'));
            }
            $this->temporaryDirectory = $temporaryDirectory;
        }

        return $result;
    }

    /**
     * @param string $outputFilename
     *
     * @throws UserMessageException
     */
    protected function checkOutputFilename($outputFilename)
    {
        $outputFilename = str_replace(DIRECTORY_SEPARATOR, '/', $outputFilename);
        if (strpos($outputFilename, DIR_BASE . '/') === 0) {
            $displayFilename = substr($outputFilename, strlen(DIR_BASE));
        } else {
            $displayFilename = $outputFilename;
        }
        if ($this->filesystem->isFile($outputFilename)) {
            if (!$this->filesystem->isWritable($outputFilename)) {
                throw new UserMessageException(t('The file %s is not writable', $displayFilename));
            }
        } else {
            $p = strrpos($outputFilename, '/');
            if ($p === false) {
                $outputFolder = '.';
            } else {
                $outputFolder = substr($outputFilename, strrpos($outputFilename, '/'));
            }
            if (!$this->filesystem->isDirectory($outputFolder)) {
                throw new UserMessageException(t('The directory containing %s does not exist', $displayFilename));
            }
            if (!$this->filesystem->isWritable($outputFolder)) {
                throw new UserMessageException(t('The directory containing %s is not writable', $displayFilename));
            }
        }
    }
}
