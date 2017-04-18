<?php
namespace Concrete\Core\Express\Entry\Notifier;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\Form;

interface NotificationInterface
{

    const ENTRY_UPDATE_TYPE_ADD = 1;
    const ENTRY_UPDATE_TYPE_UPDATE = 2;

    function notify(Entry $entry, $updateType);

}