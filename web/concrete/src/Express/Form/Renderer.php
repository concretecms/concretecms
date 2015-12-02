<?php

namespace Concrete\Core\Express\Form;

use Concrete\Core\Entity\Express\Form;

class Renderer implements RendererInterface
{

    protected $form;

    public function __construct(Form $form)
    {
        $this->form = $form;
    }

    public function render()
    {


    }

}