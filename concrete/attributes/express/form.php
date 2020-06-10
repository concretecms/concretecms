<?php
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Entity\Express\Entity;

if (isset($entity) && $entity instanceof Entity) {
    echo $entrySelector->selectEntry($entity, $this->field('value'), $entry);
}
