<?php

namespace Concrete\Core\Entity\Express\Control;

use Concrete\Core\Express\Form\Control\NameEntityPropertyControlRenderer;
use Concrete\Core\Foundation\Environment;

/**
 * @Entity
 */
class EntityNameControl extends Control
{

    public function getFormRenderer()
    {
        return new NameEntityPropertyControlRenderer();
    }


}