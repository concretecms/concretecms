<?php
namespace Concrete\Core\Search\Query\Modifier;

use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Search\ProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class CustomItemsPerPageRequestModifier extends AbstractRequestModifier
{

    /**
     * @var int
     */
    protected $maxPerPage;

    public function __construct(int $maxPerPage, Request $request, $method = Request::METHOD_POST)
    {
        $this->maxPerPage = $maxPerPage;
        $this->method = $method;
        $this->request = $request;
    }

    public function modify(Query $query)
    {
        $bag = $this->getParameterBag();
        if ($bag->has('itemsPerPage')) {
            $itemsPerPage = (int) $bag->get('itemsPerPage');
            if ($itemsPerPage <= $this->maxPerPage) {
                $query->setItemsPerPage($itemsPerPage);
            }
        }
    }

}
