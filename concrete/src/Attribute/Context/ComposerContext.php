<?php
namespace Concrete\Core\Attribute\Context;

use Concrete\Core\Attribute\Form\Group\ComposerView;
use Concrete\Core\Form\Control\ControlInterface;

class ComposerContext extends BasicFormContext
{

    public function getFormGroupView(ControlInterface $control)
    {
        return new ComposerView($control, $this);
    }
}
