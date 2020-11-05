<?php

namespace Concrete\Core\Express\Entry;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Attribute\Value\ExpressValue;
use Concrete\Core\Entity\Express\Control\AttributeKeyControl;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Entity\User\User as UserEntity;
use Concrete\Core\Express\Event\Event;
use Concrete\Core\Express\Form\Control\SaveHandler\SaveHandlerInterface;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Symfony\Component\HttpFoundation\Request;

class Manager implements EntryManagerInterface
{
    protected $entityManager;
    protected $request;


    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    public function __construct(EntityManagerInterface $entityManager, Request $request, Application $app)
    {
        $this->request = $request;
        $this->entityManager = $entityManager;
        $this->app = $app;
    }

    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @TODO This function needs to do a better job of computing display order when in use by an
     * owned entity. The problem is if you have an entity like Service owned by Category then if you add
     * four categories with 40 services total, you'll get display order from 0 to 39; when in reality it should
     * be split between the owned entities so that categories bound to service 1 go from 0 to 9, from service 2
     * go from 0 to 10, etc...
     */
    protected function getNewDisplayOrder(Entity $entity)
    {
        $query = $this->entityManager->createQuery(
            'select max(e.exEntryDisplayOrder) as displayOrder from \Concrete\Core\Entity\Express\Entry e where e.entity = :entity'
        );
        $query->setParameter('entity', $entity);
        $displayOrder = $query->getOneOrNullResult(Query::HYDRATE_SINGLE_SCALAR);
        if (!$displayOrder) {
            $displayOrder = 1;
        } else {
            ++$displayOrder;
        }

        return $displayOrder;
    }

    public function createEntry(Entity $entity, UserEntity $author = null, Site $site = null)
    {
        $entry = new Entry();
        if (!$author) {
            $u = $this->app->make(User::class);
            if ($u->isRegistered()) {
                $author = $u->getUserInfoObject()->getEntityObject();
                $entry->setAuthor($author);
            }
        }
        $generator = $this->app->make(PublicIdentifierGenerator::class);
        $entry->setPublicIdentifier($generator->generate());
        $entry->setEntity($entity);
        if ($entity->supportsCustomDisplayOrder()) {
            $entry->setEntryDisplayOrder($this->getNewDisplayOrder($entity));
        }

        $resultsNode = $entity->getEntityResultsNodeObject($site);
        if ($resultsNode) {
            $entry->setResultsNodeID($resultsNode->getTreeNodeID());
        }

        return $entry;
    }

    public function addEntry(Entity $entity, Site $site = null)
    {
        $entry = $this->createEntry($entity, null, $site);
        $this->entityManager->persist($entry);
        $this->entityManager->flush();

        return $entry;
    }

    public function deleteEntry(Entry $entry)
    {
        // Get all associations that reference this entry.
        $this->entityManager->remove($entry);
        $this->entityManager->flush();
    }

    public function saveEntryAttributesForm(Form $form, Entry $entry)
    {
        foreach ($form->getControls() as $control) {
            $type = $control->getControlType();
            $saver = $type->getSaveHandler($control);
            if ($saver instanceof SaveHandlerInterface) {
                $saver->saveFromRequest($control, $entry, $this->request);
            }
        }
        $this->entityManager->flush();

        $ev = new Event($entry);
        $ev->setEntityManager($this->entityManager);
        \Events::dispatch('on_express_entry_saved', $ev);

        return $ev->getEntry();
    }

    public function getEntryAttributeValuesForm(Form $form, Entry $entry)
    {
        $submittedAttributeValues = [];
        foreach ($form->getControls() as $control) {
            if ($control instanceof AttributeKeyControl) {
                $attributeKey = $control->getAttributeKey();
                $genericValue = $attributeKey->getController()->createAttributeValueFromRequest();
                $attributeValue = new ExpressValue();
                $attributeValue->setAttributeKey($attributeKey);
                $attributeValue->setEntry($entry);
                $attributeValue->setAttributeValueObject($genericValue);
                $submittedAttributeValues[] = $attributeValue;
            }
        }
        //TODO implement for other express entities controls as TextControl And Association control

        return $submittedAttributeValues;
    }

}
