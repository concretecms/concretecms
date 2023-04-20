<?php

namespace Concrete\Core\Api;

use League\Fractal\Resource\ResourceInterface;

interface ApiResourceValueInterface
{

    /**
     * @return mixed
     */
    public function getApiValueResource(): ?ResourceInterface;


}