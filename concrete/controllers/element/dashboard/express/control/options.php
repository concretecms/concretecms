<?php
namespace Concrete\Controller\Element\Dashboard\Express\Control;

use Concrete\Core\Controller\ElementController;

class Options extends ElementController
{
    protected $control;

    public function __construct(\Concrete\Core\Entity\Express\Control\Control $control = null)
    {
        $this->control = $control;
        parent::__construct();
    }

    public function getElement()
    {
        if (isset($this->control)) {
            return 'dashboard/express/control/options/' . $this->control->getType();
        }
    }

    public function view()
    {
        $this->set('form', \Core::make('helper/form'));
        $this->set('control', $this->control);
    }
}
