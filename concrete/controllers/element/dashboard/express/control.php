<?php
namespace Concrete\Controller\Element\Dashboard\Express;

use Concrete\Core\Controller\ElementController;

class Control extends ElementController
{
    protected $control;

    public function __construct(\Concrete\Core\Entity\Express\Control\Control $control)
    {
        parent::__construct();
        $this->control = $control;
    }

    public function getElement()
    {
        return 'dashboard/express/control';
    }

    public function view()
    {
        $this->set('control', $this->control);
    }
}
