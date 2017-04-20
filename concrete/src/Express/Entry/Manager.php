<?php
namespace Concrete\Core\Express\Entry;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Express\Event\Event;
use Concrete\Core\Express\Form\Control\SaveHandler\SaveHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Symfony\Component\HttpFoundation\Request;

class Manager implements EntryManagerInterface
{

    protected $entityManager;
    protected $request;

    public function __construct(EntityManagerInterface $entityManager, Request $request)
    {
        $this->request = $request;
        $this->entityManager = $entityManager;
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
        $query = $this->entityManager->createQuery('select max(e.exEntryDisplayOrder) as displayOrder from \Concrete\Core\Entity\Express\Entry e where e.entity = :entity');
        $query->setParameter('entity', $entity);
        $displayOrder = $query->getOneOrNullResult(Query::HYDRATE_SINGLE_SCALAR);
        if (!$displayOrder) {
            $displayOrder = 1;
        } else {
            ++$displayOrder;
        }
        return $displayOrder;
    }

    public function addEntry(Entity $entity)
    {
        $entry = new Entry();
        $entry->setEntity($entity);
        if ($entity->supportsCustomDisplayOrder()) {
            $entry->setEntryDisplayOrder($this->getNewDisplayOrder($entity));
        }
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

}