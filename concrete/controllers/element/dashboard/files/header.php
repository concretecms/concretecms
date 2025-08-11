<?php
namespace Concrete\Controller\Element\Dashboard\Files;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Permission\Checker;

class Header extends ElementController
{

    protected $file;

    public function getElement()
    {
        return 'dashboard/files/header';
    }

    public function __construct(File $file)
    {
        parent::__construct();
        $this->file = $file;
    }

    public function view()
    {
        $this->set('token', app('token'));
        $this->set('file', $this->file);
        $this->set('filePermissions', new Checker($this->file));
    }

}
