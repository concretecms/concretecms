<?php
namespace Concrete\Core\Service\Configuration;

use Exception;

interface StorageInterface
{
    /**
     * Determine whether the configuration can be read.
     *
     * @return bool
     */
    public function canRead();

    /**
     * Read the configuration.
     *
     * @return string
     *
     * @throws Exception Throws an exception in case of errors.
     */
    public function read();

    /**
     * Determine whether this configuration can be written.
     *
     * @return bool
     */
    public function canWrite();

    /**
     * Read the configuration.
     *
     * @param string $configuration
     *
     * @throws Exception Throws an exception in case of errors.
     */
    public function write($configuration);
}
