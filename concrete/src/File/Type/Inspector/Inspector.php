<?php
namespace Concrete\Core\File\Type\Inspector;

use Concrete\Core\Entity\File\Version;

abstract class Inspector
{
    abstract public function inspect(Version $fv);
}
