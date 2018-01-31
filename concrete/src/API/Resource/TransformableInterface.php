<?php
namespace Concrete\Core\API\Resource;

use League\Fractal\TransformerAbstract;

interface TransformableInterface
{

    /**
     * @return TransformerAbstract
     */
    public function getTransformer();

}