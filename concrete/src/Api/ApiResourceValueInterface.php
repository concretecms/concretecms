<?php

namespace Concrete\Core\Api;

use League\Fractal\Resource\ResourceInterface;

interface ApiResourceValueInterface
{

    public function getApiValueResource(): ?ResourceInterface;


}