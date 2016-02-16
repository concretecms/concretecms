<?php
namespace Concrete\Core\Express\Form\Control\Type\SaveHandler;

use Concrete\Core\Entity\Express\Control\Control;
use Symfony\Component\HttpFoundation\Request;

interface SaveHandlerInterface
{
    public function saveFromRequest(Control $control, Request $request);
}
