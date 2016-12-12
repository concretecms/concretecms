<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Entity\Attribute\Category;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Set as SetEntity;
use Concrete\Core\Entity\Attribute\SetKey;
use Doctrine\ORM\EntityManager;

/**
 * Handles adding and removing keys from attribute sets.
 */
interface SetManagerInterface
{

    function allowAttributeSets();
    function getAttributeSets();
    function getUnassignedAttributeKeys();
    function updateAttributeSetDisplayOrder($sets);

}
