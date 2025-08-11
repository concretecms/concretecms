<?php
namespace Concrete\Core\Import\Item\Express\Control;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Export\ExportableInterface;
use Concrete\Core\Import\ImportableInterface;
use Concrete\Core\Import\Item\Express\ItemInterface;
use Concrete\Core\Utility\Service\Xml;
use Doctrine\ORM\EntityManager;

defined('C5_EXECUTE') or die("Access Denied.");

class AssociationControl implements ItemInterface
{

    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function import(\SimpleXMLElement $xml, Entity $entity)
    {
        $xmlService = app(Xml::class);
        $control = new \Concrete\Core\Entity\Express\Control\AssociationControl();
        $control->setCustomLabel((string) $xml['custom-label']);
        $control->setAssociationEntityLabelMask((string) $xml['label-mask']);
        $control->setIsRequired($xmlService->getBool($xml['required']));
        $control->setId((string) $xml['id']);
        $association = $this->entityManager->find('Concrete\Core\Entity\Express\Association', (string) $xml['association']);
        $control->setAssociation($association);
        return $control;
    }

}
