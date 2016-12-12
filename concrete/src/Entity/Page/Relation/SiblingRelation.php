<?php
namespace Concrete\Core\Entity\Page\Relation;

use Concrete\Core\Page\Relation\Formatter\SiblingFormatter;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="SiblingPageRelations"
 * )
 */
class SiblingRelation extends Relation
{

    public function getFormatter()
    {
        return new SiblingFormatter($this);
    }

}
