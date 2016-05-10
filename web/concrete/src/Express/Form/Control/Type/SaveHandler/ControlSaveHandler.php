<?php
namespace Concrete\Core\Express\Form\Control\Type\SaveHandler;

use Concrete\Core\Entity\Express\Control\Control;
use Symfony\Component\HttpFoundation\Request;

class ControlSaveHandler implements SaveHandlerInterface
{
    public function saveFromRequest(Control $control, Request $request)
    {
        $control->setIsRequired((bool) $request->request("isRequired"));
        $control->setCustomLabel($request->request("customLabel"));

        return $control;
    }
}
