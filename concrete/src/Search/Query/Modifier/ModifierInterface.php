<?php
namespace Concrete\Core\Search\Query\Modifier;

use Concrete\Core\Entity\Search\Query;

interface ModifierInterface
{

    public function modify(Query $query);

}
