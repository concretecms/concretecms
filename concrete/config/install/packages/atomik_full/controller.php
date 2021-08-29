<?php

namespace Concrete\StartingPointPackage\AtomikFull;

use Concrete\Core\File\Filesystem;
use Concrete\Core\Package\StartingPointPackage;

class controller extends StartingPointPackage
{
    protected $pkgHandle = 'atomik_full';

    public function getPackageName()
    {
        return t('Atomik Theme');
    }

    public function getPackageDescription()
    {
        return t('Creates a full services agency site using the new Atomik theme.');
    }

    public function install_file_manager()
    {
        parent::install_file_manager();
        // Create documents node in file manager
        $filesystem = new Filesystem();
        $root = $filesystem->getRootFolder();
        $filesystem->addFolder($root, 'Documents');
    }
}
