<?php

namespace Concrete\Core\Csv;

use League\Csv\Writer;
use SplFileObject;

/**
 * Get an instance of a CSV Writer.
 */
class WriterFactory
{
    /**
     * @var \Concrete\Core\Csv\EscapeFormula
     */
    protected $formatter;

    /**
     * @var string
     */
    protected $writerClass;

    /**
     * @param \Concrete\Core\Csv\EscapeFormula $formatter
     * @param string $writerClass
     */
    public function __construct(EscapeFormula $formatter, $writerClass = Writer::class)
    {
        $this->formatter = $formatter;
        $this->writerClass = $writerClass;
    }

    /**
     * Create a CSV writer from a string.
     *
     * @param string $string
     *
     * @return \League\Csv\Writer
     *
     * @see \League\Csv\AbstractCsv::createFromString()
     */
    public function createFromString($string)
    {
        $class = $this->writerClass;

        return $this->prepare($class::createFromString($string));
    }

    /**
     * Create a CSV writer from a file object.
     *
     * @param \SplFileObject $fileObject
     *
     * @return \League\Csv\Writer
     *
     * @see \League\Csv\AbstractCsv::createFromFileObject()
     */
    public function createFromFileObject(SplFileObject $fileObject)
    {
        $class = $this->writerClass;

        return $this->prepare($class::createFromFileObject($fileObject));
    }

    /**
     * Create a CSV writer from a stream.
     *
     * @param resource $stream
     *
     * @return \League\Csv\Writer
     *
     * @see \League\Csv\AbstractCsv::createFromStream()
     */
    public function createFromStream($stream)
    {
        $class = $this->writerClass;

        return $this->prepare($class::createFromStream($stream));
    }

    /**
     * Create a CSV writer from a string.
     *
     * @param string $path
     * @param string $open_mode
     *
     * @return \League\Csv\Writer
     *
     * @see \League\Csv\AbstractCsv::createFromPath()
     */
    public function createFromPath($path, $open_mode = 'r+')
    {
        $class = $this->writerClass;

        return $this->prepare($class::createFromPath($path, $open_mode));
    }

    /**
     * Add extra details to a writer.
     *
     * @param \League\Csv\Writer $writer
     *
     * @return \League\Csv\Writer
     */
    protected function prepare(Writer $writer)
    {
        return $writer->addFormatter($this->formatter);
    }
}
