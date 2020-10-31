<?php
namespace Concrete\Core\Search\Query\Modifier;

use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Search\ProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class ConfigurableItemsPerPageRequestModifier extends AbstractRequestModifier
{

    /**
     * @var array
     */
    protected $itemsPerPageOptions = [];

    public function __construct(array $itemsPerPageOptions, Request $request, $method = Request::METHOD_POST)
    {
        $this->itemsPerPageOptions = $itemsPerPageOptions;
        $this->method = $method;
        $this->request = $request;
    }

    public function modify(Query $query)
    {
        $bag = $this->getParameterBag();
        if ($bag->has('itemsPerPage')) {
            $itemsPerPage = (int) $bag->get('itemsPerPage');
            if (in_array($itemsPerPage, $this->itemsPerPageOptions, false)) {
                $query->setItemsPerPage($itemsPerPage);
            }
        }

    }

}
