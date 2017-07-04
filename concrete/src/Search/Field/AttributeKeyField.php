<?php
namespace Concrete\Core\Search\Field;

use Concrete\Core\Attribute\Context\BasicSearchContext;
use Concrete\Core\Attribute\View;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Search\ItemList\ItemList;

class AttributeKeyField extends AbstractField
{
    /**
     * The attribute key instance.
     *
     * @var Key|null
     */
    protected $attributeKey;

    /**
     * The attribute key ID.
     *
     * @var int|null
     */
    protected $akID;

    /**
     * {@inheritdoc}
     *
     * @see FieldInterface::getKey()
     */
    public function getKey()
    {
        if ($this->attributeKey !== null) {
            return 'attribute_key_' . $this->attributeKey->getAttributeKeyHandle();
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see FieldInterface::getDisplayName()
     */
    public function getDisplayName()
    {
        if ($this->attributeKey !== null) {
            return $this->attributeKey->getAttributeKeyDisplayName();
        }
    }

    /**
     * Initialize the instance.
     *
     * @param Key $attributeKey the attribute key instance
     */
    public function __construct(Key $attributeKey)
    {
        $this->attributeKey = $attributeKey;
        $this->akID = $attributeKey->getAttributeKeyID();
    }

    /**
     * {@inheritdoc}
     *
     * @see FieldInterface::renderSearchField()
     */
    public function renderSearchField()
    {
        if ($this->attributeKey !== null) {
            $type = $this->attributeKey->getAttributeType();
            $cnt = $type->getController();
            $cnt->setRequestArray($this->data);
            $cnt->setAttributeKey($this->attributeKey);
            $view = new View($this->attributeKey);
            $view->controller = $cnt;

            return $view->render(new BasicSearchContext());
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see FieldInterface::filterList()
     */
    public function filterList(ItemList $list)
    {
        if ($this->attributeKey !== null) {
            $type = $this->attributeKey->getAttributeType();
            $cnt = $type->getController();
            $cnt->setRequestArray($this->data);
            $cnt->setAttributeKey($this->attributeKey);
            $cnt->searchForm($list);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see FieldInterface::loadDataFromRequest()
     */
    public function loadDataFromRequest(array $request)
    {
        if ($this->attributeKey !== null) {
            // We need to do this because of the request whitelist + the weird request
            // namespacing we do with attribute forms.
            $this->data['akID'][$this->attributeKey->getAttributeKeyID()]
                = $request['akID'][$this->attributeKey->getAttributeKeyID()];
        }
    }

    /**
     * Return an array with the names of the properties to be serialized.
     *
     * @return string[]
     */
    public function __sleep()
    {
        return ['data', 'akID'];
    }

    /**
     * Initialize the instance once it has been deserialized.
     */
    public function __wakeup()
    {
        $this->attributeKey = \Concrete\Core\Attribute\Key\Key::getByID($this->akID);
    }
}
