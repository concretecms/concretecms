<?php

namespace Concrete\Core\Package\Packer\Writer;

use Concrete\Core\Package\Packer\PackerFile;

/**
 * Interface that persists package files after the filters have been applied.
 */
interface WriterInterface
{
    /**
     * Add a file/directory.
     *
     * @param \Concrete\Core\Package\Packer\PackerFile $file
     *
     * @return $this
     */
    public function add(PackerFile $file);

    /**
     * Called after all the files have been processed succesfully.
     */
    public function completed();
}
