<?php

namespace Concrete\Core\Package\Packer\Writer;

use Concrete\Core\Package\Packer\PackerFile;

interface WriterInterface
{
    /**
     * Process a file/directory.
     *
     * @param \Concrete\Core\Package\Packer\PackerFile $file
     */
    public function processFile(PackerFile $file);

    /**
     * Called after all the files have been processed succesfully.
     */
    public function completed();
}
