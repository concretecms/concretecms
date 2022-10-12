<?php

namespace Concrete\Core\Express\Command;

use Concrete\Core\Api\Attribute\AttributeValueMapFactory;
use Concrete\Core\Api\Express\Association\AssociationMap;
use Concrete\Core\Api\Express\Association\AssociationMapEntry;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\ManyToManyAssociation;
use Concrete\Core\Entity\Express\ManyToOneAssociation;
use Concrete\Core\Entity\Express\OneToManyAssociation;
use Concrete\Core\Entity\Express\OneToOneAssociation;
use Concrete\Core\Express\ObjectManager;
use Symfony\Component\HttpFoundation\Request;

class ExpressEntryCommandFactory
{

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var AttributeValueMapFactory
     */
    protected $attributeValueMapFactory;

    public function __construct(ObjectManager $objectManager, AttributeValueMapFactory $attributeValueMapFactory)
    {
        $this->objectManager = $objectManager;
        $this->attributeValueMapFactory = $attributeValueMapFactory;
    }

    private function getMapsFromRequest(Entity $entity, Request $request)
    {
        $category = $entity->getAttributeKeyCategory();
        $body = json_decode($request->getContent(), true);
        $attributeMap = $this->attributeValueMapFactory->createFromRequestData($category, $body);
        $associationMap = new AssociationMap();
        foreach ($body as $key => $data) {
            if (!$attributeMap->containsKey($key)) {
                // Check associations
                $association = $entity->getAssociation($key);
                if ($association) {
                    $entryIds = [];
                    $entries = null;
                    if ($data === null) {
                        $entryIds = null;
                    } else if ($association instanceof ManyToOneAssociation || $association instanceof OneToOneAssociation) {
                        $entryIds[] = (string) $data;
                    } else if ($association instanceof OneToManyAssociation || $association instanceof ManyToManyAssociation) {
                        $entryIds = $data;
                    }

                    if (is_array($entryIds)) {
                        foreach ($entryIds as $entryId) {
                            $entry = $this->objectManager->getEntry($entryId);
                            if (!$entry || !$entry->is($association->getTargetEntity()->getHandle())) {
                                throw new \Exception(
                                    t(
                                        'Invalid entry %s found for association %s in request.',
                                        h($entryId),
                                        $association->getTargetPropertyName()
                                    )
                                );
                            } else {
                                if ($entry) {
                                    $entries[] = $entry;
                                }
                            }
                        }
                    }

                    $associationMapEntry = new AssociationMapEntry($association, $entries);
                    $associationMap->addEntry($associationMapEntry);
                }
            }
        }

        return [$attributeMap, $associationMap];
    }

    public function createAddEntryCommand(Entity $entity, Request $request)
    {
        $command = new AddExpressEntryCommand($entity);
        list($attributeMap, $associationMap) = $this->getMapsFromRequest($entity, $request);
        $command->setAttributeMap($attributeMap);
        $command->setAssociationMap($associationMap);
        return $command;
    }

    public function createUpdateEntryCommand(Entity $entity, Entry $entry, Request $request)
    {
        $command = new UpdateExpressEntryCommand($entry);
        list($attributeMap, $associationMap) = $this->getMapsFromRequest($entity, $request);
        $command->setAttributeMap($attributeMap);
        $command->setAssociationMap($associationMap);
        return $command;
    }

}
