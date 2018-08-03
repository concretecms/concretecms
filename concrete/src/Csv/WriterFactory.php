<?php

namespace Concrete\Core\Csv;

use Concrete\Core\Application\Application;
use League\Csv\Writer;

/**
 * Get an instance of a CSV Writer
 */
class WriterFactory
{

    protected $writerClass;
    protected $formatter;

    public function __construct(EscapeFormula $formatter, $writerClass = Writer::class)
    {
        $this->formatter = $formatter;
        $this->writerClass = $writerClass;
    }

    /**
     * Create a CSV writer from a string
     *
     * @see Writer::createFromString
     * @param string $string
     * @return Writer
     */
    public function createFromString($string)
    {
        $class = $this->writerClass;
        return $this->prepare($class::createFromString($string));
    }

    /**
     * Create a CSV writer from a file object
     *
     * @see Writer::createFromFileObject
     * @param SplFileObject $fileObject
     * @return Writer
     */
    public function createFromFileObject(SplFileObject $fileObject)
    {
        $class = $this->writerClass;
        return $this->prepare($class::createFromFileObject($fileObject));
    }

    /**
     * Create a CSV writer from a stream
     *
     * @see Writer::createFromStream
     * @param resource $stream
     * @return Writer
     */
    public function createFromStream($stream)
    {
        $class = $this->writerClass;
        return $this->prepare($class::createFromStream($stream));
    }

    /**
     * Create a CSV writer from a string
     *
     * @see Writer::createFromPath
     * @param string $path
     * @param string $open_mode
     * @return Writer
     */
    public function createFromPath($path, $open_mode = 'r+')
    {
        $class = $this->writerClass;
        return $this->prepare($class::createFromPath($path, $open_mode));
    }

    /**
     * Add extra details to a writer
     *
     * @param \League\Csv\Writer $writer
     * @return Writer
     */
    protected function prepare(Writer $writer)
    {
        return $writer->addFormatter($this->formatter);
    }
}
