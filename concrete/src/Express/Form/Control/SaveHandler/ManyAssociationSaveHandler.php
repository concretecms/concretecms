<?php
namespace Concrete\Core\Express\Form\Control\SaveHandler;

use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Express\Association\Applier;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

abstract class ManyAssociationSaveHandler implements ManySaveHandlerInterface
{
    protected $entityManager;
    protected $applier;

    public function __construct(Applier $applier, EntityManager $manager)
    {
        $this->entityManager = $manager;
        $this->applier = $applier;
    }

    public function getAssociatedEntriesFromRequest(Control $control, Request $request)
    {
        $r = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Entry');
        $entryIDs = $request->request->get('express_association_' . $control->getId());
        $vals = Application::getFacadeApplication()->make('helper/validation/strings');
        if (!is_array($entryIDs) && $vals->notempty($entryIDs)) {
            $entryIDs = explode(',', $entryIDs);
        }
        $associatedEntries = [];
        if (is_array($entryIDs)) {
            foreach ($entryIDs as $entryID) {
                $associatedEntry = $r->findOneById($entryID);
                $target = $control->getAssociation()->getTargetEntity();
                if (is_object($associatedEntry) && $associatedEntry->getEntity()->getID() == $target->getID()) {
                    $associatedEntries[] = $associatedEntry;
                }
            }
        }

        return $associatedEntries;
    }
}
