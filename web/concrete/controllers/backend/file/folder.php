<?php
namespace Concrete\Controller\Backend\File;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\File\Filesystem;

class Folder extends AbstractController
{

    public function add()
    {
        $filesystem = new Filesystem();
        $folder = $filesystem->getRootFolder();
    }

}
