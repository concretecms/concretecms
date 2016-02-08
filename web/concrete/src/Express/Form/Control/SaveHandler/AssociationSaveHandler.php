<?php
namespace Concrete\Core\Express\Form\Control\SaveHandler;

use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

class AssociationSaveHandler implements SaveHandlerInterface
{
    protected $entityManager;

    public function __construct(EntityManager $manager)
    {
        $this->entityManager = $manager;
    }

    public function saveFromRequest(Control $control, Entry $entry, Request $request)
    {
        $r = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Entry');
        $entityId = $request->request->get('express_association_' . $control->getId());
        $associatedEntry = $r->findOneById($entityId);
        $target = $control->getAssociation()->getTargetEntity();
        if (is_object($associatedEntry) && $associatedEntry->getEntity()->getID() == $target->getID()) {

        }
    }
}
