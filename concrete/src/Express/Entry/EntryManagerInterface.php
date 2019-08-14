<?php
namespace Concrete\Core\Express\Entry;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\Form;

/**
 * @since 8.2.0
 */
interface EntryManagerInterface
{
    /**
     * Method that create empty entry.
     *
     * @return mixed
     * @since 8.4.4
     */
    public function createEntry(Entity $entity);

    public function addEntry(Entity $entity);

    public function deleteEntry(Entry $entry);

    public function saveEntryAttributesForm(Form $form, Entry $entry);

    /**
     * @since 8.4.4
     */
    public function getEntryAttributeValuesForm(Form $form, Entry $entry);
}
