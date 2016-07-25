<?php
namespace Concrete\Core\Express\Entry;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Express\Event\Event;
use Concrete\Core\Express\Form\Control\SaveHandler\SaveHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class Manager
{

    protected $entityManager;
    protected $request;

    public function __construct(EntityManagerInterface $entityManager, Request $request)
    {
        $this->request = $request;
        $this->entityManager = $entityManager;
    }

    public function addEntry(Entity $entity)
    {
        $entry = new Entry();
        $entry->setEntity($entity);
        $this->entityManager->persist($entry);
        return $entry;
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