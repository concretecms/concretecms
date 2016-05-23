<?php
namespace Concrete\Core\Search\Field;

use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Search\ItemList\ItemList;

class AttributeKeyField extends AbstractField
{

    protected $attributeKey;
    protected $akID;

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
        $this->akID = $attributeKey->getAttributeKeyID();
    }

    public function renderSearchField()
    {
        return $this->attributeKey->render('search', null, true);
    }

    public function filterList(ItemList $list)
    {
        $type = $this->attributeKey->getAttributeType();
        $cnt = $type->getController();
        $cnt->setRequestArray($this->data);
        $cnt->setAttributeKey($this->attributeKey);
        $cnt->searchForm($list);
    }

    public function loadDataFromRequest(array $request)
    {
        // We need to do this because of the request whitelist + the weird request
        // namespacing we do with attribute forms.
        $this->data['akID'][$this->attributeKey->getAttributeKeyID()]
            = $request['akID'][$this->attributeKey->getAttributeKeyID()];
    }

    public function __sleep()
    {
        return array('data', 'akID');
    }

    public function __wakeup()
    {
        $this->attributeKey = \Concrete\Core\Attribute\Key\Key::getByID($this->akID);
    }
}
