<?php

namespace Concrete\Core\Express\Association\Formatter;

use Concrete\Core\Entity\Express\Control\AssociationControl;
use Concrete\Core\Express\BaseEntity;

interface FormatterInterface
{

    public function getIcon();
    public function getDisplayName();
    public function getEntityDisplayName(AssociationControl $control, BaseEntity $entity);
    public function getTypeDisplayName();

}