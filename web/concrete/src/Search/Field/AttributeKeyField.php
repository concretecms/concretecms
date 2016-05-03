<?php
namespace Concrete\Core\Search\Field;

use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Search\ItemList\ItemList;

class AttributeKeyField extends AbstractField
{

    protected $attributeKey;

    public function getKey()
    {
        return 'attribute_key_' . $this->attributeKey->getAttributeKeyHandle();
    }

    public function getDisplayName()
    {
        return $this->attributeKey->getAttributeKeyDisplayName();
    }

    public function __construct(Key $attributeKey)
    {
        $this->attributeKey = $attributeKey;
    }

    public function renderSearchField()
    {
        return $this->attributeKey->render('search', null, true);
    }

    public function filterList(ItemList $list, $request)
    {
        $type = $this->attributeKey->getAttributeType();
        $cnt = $type->getController();
        $cnt->setRequestArray($request);
        $cnt->setAttributeKey($this->attributeKey);
        $cnt->searchForm($list);
    }
}
