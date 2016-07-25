<?php
namespace Concrete\Core\Express\Form;

use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\Form;

interface ViewRendererInterface
{
    public function render(Form $form, Entry $entity = null);
}
