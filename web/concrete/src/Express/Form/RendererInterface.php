<?php
namespace Concrete\Core\Express\Form;

use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Entity\Express\Entity;

interface RendererInterface
{
    public function render(Form $form, Entity $entity = null);
}
