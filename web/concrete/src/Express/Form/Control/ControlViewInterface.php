<?php

namespace Concrete\Core\Express\Form\Control;

interface ControlViewInterface
{

    public function render($template);
    public function field($name);

}