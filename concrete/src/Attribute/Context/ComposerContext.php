<?php
namespace Concrete\Core\Attribute\Context;

use Concrete\Core\Attribute\Form\ComposerFormView;

class ComposerContext extends StandardFormContext
{

    public function getFormView()
    {
        return new ComposerFormView($this);
    }
}
