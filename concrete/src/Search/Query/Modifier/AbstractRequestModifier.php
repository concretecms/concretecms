<?php
namespace Concrete\Core\Search\Query\Modifier;

use Concrete\Core\Search\ProviderInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractRequestModifier implements ModifierInterface
{

    /**
     * @var ProviderInterface
     */
    protected $provider;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var int
     */
    protected $method;

    public function __construct(ProviderInterface $provider, Request $request, $method = Request::METHOD_POST)
    {
        $this->request = $request;
        $this->method = $method;
        $this->provider = $provider;
    }

    protected function getParameterBag()
    {
        $bag = $this->method === Request::METHOD_POST ? $this->request->request : $this->request->query;
        return $bag;
    }

}
