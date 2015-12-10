<?php

namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Attribute\EntityInterface;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Entity\AttributeKey\AttributeKey;
use Symfony\Component\HttpFoundation\Request;

interface CategoryInterface
{

    public function setEntity(EntityInterface $entity);
    public function addFromRequest(Type $type, Request $request);
    public function updateFromRequest(AttributeKey $key, Request $request);
    public function delete(AttributeKey $key);

}