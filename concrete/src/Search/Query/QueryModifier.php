<?php

namespace Concrete\Core\Search\Query;

use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Search\ProviderInterface;
use Concrete\Core\Search\Query\Modifier\ModifierInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class QueryModifier
 *
 * Responsible for modifying an in-progress search query based on various input sources, like auto sort columns
 * from request, items per page from request.
 *
 * @package Concrete\Core\Search\Query
 */
class QueryModifier
{

    protected $modifiers = [];

    public function addModifier(ModifierInterface $modifier)
    {
        $this->modifiers[] = $modifier;
    }

    public function process(Query $query): Query
    {
        $query = clone $query;
        foreach ($this->modifiers as $modifier) {
            $modifier->modify($query);
        }
        return $query;
    }


}
