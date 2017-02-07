<?php
namespace Concrete\Core\Attribute\Form\Control;

use Concrete\Core\Attribute\Context\ComposerContext;
use Concrete\Core\Attribute\Context\ContextInterface;
use Concrete\Core\Attribute\Key\Key;

class StandardView extends View
{

    public function __construct(ContextInterface $context)
    {
        parent::__construct($context);
        $this->setFieldWrapperTemplate('bootstrap3');
    }

    public function enableGroupedTemplate()
    {
        $this->setFieldWrapperTemplate('bootstrap3_grouped');
    }
}
