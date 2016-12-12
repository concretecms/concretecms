<?php
namespace Concrete\Core\Express\Form\Control\SaveHandler;

use Concrete\Core\Entity\Express\Control\AssociationControl;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Association\Applier;
use Concrete\Core\Express\ObjectAssociationBuilder;
use Concrete\Core\Express\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Builder\AssociationBuilder;
use Symfony\Component\HttpFoundation\Request;


abstract class OneAssociationSaveHandler implements SaveHandlerInterface
{
    protected $entityManager;
    protected $applier;

    public function __construct(Applier $applier, EntityManager $manager)
    {
        $this->entityManager = $manager;
        $this->applier = $applier;
    }

    protected function getAssociatedEntryFromRequest(Control $control, Request $request)
    {
        /**
         * @var $control AssociationControl
         */
        $r = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Entry');
        $entryID = $request->request->get('express_association_' . $control->getId());
        $associatedEntry = $r->findOneById($entryID);
        return $associatedEntry;
    }

}
