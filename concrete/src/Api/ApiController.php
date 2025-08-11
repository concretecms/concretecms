<?php

namespace Concrete\Core\Api;


use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Url\Resolver\PathUrlResolver;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;
use Concrete\Core\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /**
     * @var Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Returns an error response in the standard Concrete error response format.
     *
     * @param $message
     * @param int $code
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function error($message, $code = 400)
    {
        $list = new ErrorList();
        $list->add($message);
        return $list->createResponse($code);
    }

    /**
     * Transforms an object using the API transformer
     *
     * @param $object
     * @param TransformerAbstract $transformer
     * @param string|null $resourceKey
     *
     * @return Item
     */
    public function transform($object, TransformerAbstract $transformer, ?string $resourceKey = null)
    {
        return new Item($object, $transformer, $resourceKey);
    }

    public function deleted(string $objectType, string $id)
    {
        return new JsonResponse([
            'id' => $id,
            'object' => $objectType,
            'deleted' => true
        ]);
    }
}
