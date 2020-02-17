<?php
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;

if (isset($entity, $entry) && $entity instanceof Entity && $entry instanceof Entry) {
    echo $entrySelector->selectEntry($entity, $this->field('value'), $entry);
}
