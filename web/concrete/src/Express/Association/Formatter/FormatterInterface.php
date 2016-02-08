<?php
namespace Concrete\Core\Express\Association\Formatter;

use Concrete\Core\Entity\Express\Control\AssociationControl;
use Concrete\Core\Entity\Express\Entity;

interface FormatterInterface
{
    public function getIcon();
    public function getDisplayName();
    public function getEntityDisplayName(AssociationControl $control, Entity $entity);
    public function getTypeDisplayName();
}
