<?php
namespace Concrete\Core\Express\Form\Control;

use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Form\Context\ContextInterface;
use Concrete\Core\Express\Form\RendererFactory;

interface RendererInterface
{
    public function render(ContextInterface $context, Control $control, Entry $entry = null);
}
