<?php

namespace Concrete\Core\Express\Form;

use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Express\BaseEntity;

interface RendererInterface
{

    public function render(Form $form, BaseEntity $entity = null);


}