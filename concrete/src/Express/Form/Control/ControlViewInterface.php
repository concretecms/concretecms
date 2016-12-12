<?php
namespace Concrete\Core\Express\Form\Control;

use Concrete\Core\Entity\Express\Control\Control;

interface ControlViewInterface
{
    public function render(Control $control, $template);
    public function field($name);
}
