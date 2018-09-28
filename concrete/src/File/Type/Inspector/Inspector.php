<?php

namespace Concrete\Core\File\Type\Inspector;

use Concrete\Core\Entity\File\Version;

/**
 * Abstract class that all the file inspectors must extend.
 */
abstract class Inspector
{
    /**
     * This method is called when a File\Version class refreshes its attributes.
     * This can be used to update the File\Version attributes as well as its contents.
     *
     * @param \Concrete\Core\Entity\File\Version $fv
     */
    abstract public function inspect(Version $fv);
}
