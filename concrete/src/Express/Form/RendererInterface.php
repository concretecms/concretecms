<?php
namespace Concrete\Core\Express\Form;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\Form;

interface RendererInterface
{
    /**
     * @return FormInterface
     */
    public function getForm();

    public function render(Entry $entry = null);
}
