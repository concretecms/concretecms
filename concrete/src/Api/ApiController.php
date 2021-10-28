<?php

namespace Concrete\Core\Api;


use Concrete\Core\Error\ErrorList\ErrorList;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class ApiController
{

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
     * 
     * @return Item
     */
    public function transform($object, TransformerAbstract $transformer)
    {
        return new Item($object, $transformer);
    }
}
