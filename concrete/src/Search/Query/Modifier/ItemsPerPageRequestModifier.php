<?php
namespace Concrete\Core\Search\Query\Modifier;

use Concrete\Core\Entity\Search\Query;
use Symfony\Component\HttpFoundation\Request;

class ItemsPerPageRequestModifier extends AbstractRequestModifier
{

    public function modify(Query $query)
    {
        $bag = $this->getParameterBag();
        if ($bag->has('itemsPerPage')) {
            $itemsPerPage = (int) $bag->get('itemsPerPage');
            if (in_array($itemsPerPage, $this->provider->getItemsPerPageOptions(), false)) {
                $query->setItemsPerPage($itemsPerPage);
            }
        }

    }

}
