<?php

namespace Concrete\Core\Express\Form\Control;

use Concrete\Core\Express\Form\RendererFactory;

interface RendererInterface
{

    public function build(RendererFactory $factory);
    public function getControlLabel();
    public function render();


}