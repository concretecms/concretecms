<?php

namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Attribute\EntityInterface;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Entity\Attribute\Category;
use Concrete\Core\Entity\Attribute\Key\Key as AttributeKey;
use Symfony\Component\HttpFoundation\Request;

interface CategoryInterface
{

    public function setCategoryEntity(Category $entity);
    public function setEntity(EntityInterface $entity);
    public function addFromRequest(\Concrete\Core\Entity\Attribute\Type $type, Request $request);
    public function updateFromRequest(AttributeKey $key, Request $request);
    public function delete(AttributeKey $key);
    public function associateAttributeKeyType(\Concrete\Core\Entity\Attribute\Type $type);

}