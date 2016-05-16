<?php
namespace Concrete\Core\File\ImportProcessor;

use Concrete\Core\Entity\File\Version;

interface ProcessorInterface
{
    public function shouldProcess(Version $version);
    public function process(Version $version);
}
