<?php

namespace Concrete\Core\Package\Packer\Filter;

use Concrete\Core\Package\Packer\PackerFile;

/**
 * Interface that filters that change/exclude package files must implement.
 */
interface FilterInterface
{
    /**
     * Process a file/directory.
     *
     * @param \Concrete\Core\Package\Packer\PackerFile $file the file to be processed
     *
     * @return \Concrete\Core\Package\Packer\PackerFile[] the resulting list of files after processing
     */
    public function apply(PackerFile $file);
}
