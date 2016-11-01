<?php
namespace Concrete\Core\Express\Form\Control\Type;

use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Express\Form\Context\ContextInterface;
use Concrete\Core\Import\ImportableInterface;

interface TypeInterface extends ImportableInterface
{
    public function getType();
    public function getPluralDisplayName();
    public function getValidator();
    public function getDisplayName();
    public function getItems(Entity $entity);
    public function createControlByIdentifier($id);
    public function getSaveHandler(Control $control);
}
