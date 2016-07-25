<?php
namespace Concrete\Core\Express\Form\Control\Type\SaveHandler;

use Concrete\Core\Entity\Express\Control\Control;
use Symfony\Component\HttpFoundation\Request;

class AssociationControlSaveHandler extends ControlSaveHandler
{
    public function saveFromRequest(Control $control, Request $request)
    {
        $control = parent::saveFromRequest($control, $request);
        $control->setAssociationEntityLabelMask($request->request('label_mask'));

        return $control;
    }
}
