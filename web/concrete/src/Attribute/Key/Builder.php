<?php

namespace Concrete\Core\Attribute\Key;

use Concrete\Core\Attribute\AttributeKeyFactory;
use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Entity\AttributeKey\AttributeKey;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

class Builder
{

    protected $attributeKeyFactory;

    public function __construct(AttributeKeyFactory $factory)
    {
        $this->attributeKeyFactory = $factory;
    }

    protected function loadFromRequest(AttributeKey $key, Request $request)
    {
        $key->setName($request->request->get('akName'));
        $key->setHandle($request->request->get('akHandle'));
        $key->setIsIndexed((bool) $request->request->get('akIsSearchableIndexed'));
        $key->setIsSearchable((bool) $request->request->get('akIsSearchable'));
    }

    public function addFromRequest(CategoryInterface $category, Type $type, Request $request)
    {
        $key = $this->attributeKeyFactory->make($type->getAttributeTypeHandle());
        $this->loadFromRequest($key, $request);

        // Take our newly minted TextAttributeKey, SelectAttributeKey, etc... and pass it to the
        // category so it can be properly assigned in whatever way the category chooses to do so

        $category->add($key);
    }

}
