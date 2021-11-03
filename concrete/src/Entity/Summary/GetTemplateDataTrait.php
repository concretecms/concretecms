<?php

namespace Concrete\Core\Entity\Summary;

use Concrete\Core\Entity\Express\EntityRepository;
use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Concrete\Core\Summary\Data\Collection;
use Concrete\Core\Support\Facade\Facade;

trait GetTemplateDataTrait
{

    public function getData() : Collection
    {
        if ($this->data instanceof Collection) {
            return $this->data;
        }
        $app = Facade::getFacadeApplication();
        $serializer = $app->make(JsonSerializer::class);
        return $serializer->denormalize($this->data, Collection::class, 'json');
    }
    
}
