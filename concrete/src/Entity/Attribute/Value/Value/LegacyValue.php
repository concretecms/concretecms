<?php
namespace Concrete\Core\Entity\Attribute\Value\Value;

use Doctrine\ORM\Mapping as ORM;
use Concrete\Core\Support\Facade\Application;

/**
 * @ORM\Entity
 * @ORM\Table(name="atLegacy")
 */
class LegacyValue extends Value
{

    public function __toString()
    {
        return '';
    }

}
