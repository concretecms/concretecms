<?php

namespace Concrete\Core\Express\Form\Control\Type\SaveHandler;

use Concrete\Core\Entity\Express\Control\Control;
use Symfony\Component\HttpFoundation\Request;

class TextControlSaveHandler extends ControlSaveHandler
{

    public function saveFromRequest(Control $control, Request $request)
    {
        $control = parent::saveFromRequest($control, $request);
        $control->setText($request->request('text'));
        return $control;
    }


}