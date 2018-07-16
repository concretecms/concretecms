<?php
namespace Concrete\Core\Attribute;

/**
 * The interface that any object thay may have attributes must implement.
 */
interface ObjectInterface
{
    /**
     * @param \Concrete\Core\Attribute\AttributeKeyInterface|string $ak An attribute key instance (or its handle)
     * @param bool $createIfNotExists Shall the generic attribute value be created if it does not already exist?
     *
     * @return \Concrete\Core\Attribute\AttributeValueInterface|null
     */
    public function getAttributeValueObject($ak, $createIfNotExists = false);

    /**
     * Alias of getAttributeValueObject (assuming $createIfNotExists is false).
     *
     * @param \Concrete\Core\Attribute\AttributeKeyInterface|string $ak An attribute key instance (or its handle)
     *
     * @return \Concrete\Core\Attribute\AttributeValueInterface|null
     */
    public function getAttributeValue($ak);

    /**
     * @param \Concrete\Core\Attribute\AttributeKeyInterface|string $ak An attribute key instance (or its handle)
     * @param string|false $mode
     *
     * @return mixed
     */
    public function getAttribute($ak, $mode = false);

    /**
     * @return \Concrete\Core\Attribute\Category\CategoryInterface
     */
    public function getObjectAttributeCategory();

    /**
     * @param \Concrete\Core\Attribute\AttributeKeyInterface|string $ak An attribute key instance (or its handle)
     */
    public function clearAttribute($ak);

    /**
     * Sets the attribute of of the ObjectInterface instance to the specified value, and persists it.
     *
     * @param \Concrete\Core\Attribute\AttributeKeyInterface|string $ak An attribute key instance (or its handle)
     * @param \Concrete\Core\Entity\Attribute\Value\Value\AbstractValue|\Concrete\Core\Attribute\Value\EmptyRequestAttributeValue|array $value
     *
     * @return \Concrete\Core\Attribute\AttributeValueInterface
     */
    public function setAttribute($ak, $value);
}
