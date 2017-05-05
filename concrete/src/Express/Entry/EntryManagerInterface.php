<?php
namespace Concrete\Core\Express\Entry;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\Form;

interface EntryManagerInterface
{
    function addEntry(Entity $entity);
    function deleteEntry(Entry $entry);
    function saveEntryAttributesForm(Form $form, Entry $entry);

}