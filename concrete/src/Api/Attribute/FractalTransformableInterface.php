<?php

namespace Concrete\Core\Api\Attribute;

use League\Fractal\TransformerAbstract;

interface FractalTransformableInterface
{

    public function getApiDataTransformer(): TransformerAbstract;


}