<?php
namespace Concrete\Core\Attribute\Context;

use Concrete\Core\Attribute\Form\Control\ComposerView;

class ComposerContext extends StandardFormContext
{

    public function getFormControlView()
    {
        return new ComposerView($this);
    }
}
