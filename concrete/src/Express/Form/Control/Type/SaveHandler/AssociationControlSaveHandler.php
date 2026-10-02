<?php
namespace Concrete\Core\Express\Form\Control\Type\SaveHandler;

use Concrete\Core\Entity\Express\Control\AssociationControl;
use Concrete\Core\Entity\Express\Control\Control;
use Symfony\Component\HttpFoundation\Request;

class AssociationControlSaveHandler extends ControlSaveHandler
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Express\Form\Control\Type\SaveHandler\ControlSaveHandler::saveFromRequest()
     *
     * @param \Concrete\Core\Http\Request $request
     */
    public function saveFromRequest(Control $control, Request $request)
    {
        $control = parent::saveFromRequest($control, $request);
        /**
         * @var $control AssociationControl
         */
        $control->setAssociationEntityLabelMask($request->get('label_mask'));
        $control->setEntrySelectorMode($request->get('mode'));
        $control->setEnableEntryReordering($request->get('enable_entry_reordering') ? true : false);

        return $control;
    }
}
