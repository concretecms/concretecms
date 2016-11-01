<?php
namespace Concrete\Core\Express\Form;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\FieldSet;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Express\Form\Context\DashboardContext;
use Concrete\Core\Express\Form\Context\DashboardViewContext;
use Concrete\Core\Express\Form\Context\ViewContext;

class DashboardRenderer extends StandardViewRenderer
{

    protected function getContext()
    {
        return new DashboardContext($this->application, $this);
    }


}
