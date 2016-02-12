<?php
namespace Concrete\Core\Express\Association\Formatter;

use Concrete\Core\Entity\Express\Control\AssociationControl;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;

interface FormatterInterface
{
    public function getIcon();
    public function getDisplayName();
    public function getEntryDisplayName(AssociationControl $control, Entry $entry);
    public function getTypeDisplayName();
}
