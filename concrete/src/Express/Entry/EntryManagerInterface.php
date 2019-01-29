<?php
namespace Concrete\Core\Express\Entry;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\Form;

interface EntryManagerInterface
{
    /**
     * Method that create empty entry.
     *
     * @return mixed
     */
    public function createEntry(Entity $entity);

    public function addEntry(Entity $entity);

    public function deleteEntry(Entry $entry);

    public function saveEntryAttributesForm(Form $form, Entry $entry);

    public function getEntryAttributeValuesForm(Form $form, Entry $entry);
}
