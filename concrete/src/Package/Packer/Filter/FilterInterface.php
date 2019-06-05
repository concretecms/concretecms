<?php

namespace Concrete\Core\Package\Packer\Filter;

use Concrete\Core\Package\Packer\PackerFile;

interface FilterInterface
{
    /**
     * Process a file/directory.
     *
     * @param \Concrete\Core\Package\Packer\PackerFile $file
     *
     * @return \Concrete\Core\Package\Packer\PackerFile[]
     */
    public function apply(PackerFile $file);
}
