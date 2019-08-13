<?php

namespace Concrete\Core\File\ImportProcessor;

use Concrete\Core\Entity\File\Version;

/**
 * The interface that file processors should implement.
 * @since 5.7.5.2
 */
interface ProcessorInterface
{
    /**
     * Should this processor process a specific file version?
     *
     * @param \Concrete\Core\Entity\File\Version $version
     *
     * @return bool
     */
    public function shouldProcess(Version $version);

    /**
     * Process a specific file version.
     *
     * @param \Concrete\Core\Entity\File\Version $version
     *
     * @throws \Exception in case of errors
     */
    public function process(Version $version);
}
