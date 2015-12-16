<?php

namespace Concrete\Core\Express\Form\Control\Type\SaveHandler;


use Concrete\Core\Entity\Express\Control\Control;
use Symfony\Component\HttpFoundation\Request;

class ControlSaveHandler implements SaveHandlerInterface
{

    public function saveFromRequest(Control $control, Request $request)
    {
        $control->setCustomLabel($request->request("customLabel"));
        return $control;
    }

}