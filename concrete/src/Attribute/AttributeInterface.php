<?php
namespace Concrete\Core\Attribute;

interface AttributeInterface
{
    /**
     * AttributeInterface constructor.
     *
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function __construct(\Doctrine\ORM\EntityManager $entityManager);

    /**
     * Get Attribute Value object.
     *
     * @return AttributeValueInterface
     */
    public function getAttributeValue();

    /**
     * For a DateTime implementation this could for example mean
     * that the value is formatted and localized.
     *
     * @return mixed
     */
    public function getDisplayValue();

    /**
     * Returns a Type entity.
     *
     * The Type object is mapped with a row from the AttributeTypes table.
     * Use the object for example to retrieve the current attribute type handle (e.g. 'date_time').
     *
     * return \Concrete\Core\Entity\Attribute\Type.
     */
    public function getAttributeType();

    /**
     * @param \Concrete\Core\Entity\Attribute\Type $type
     */
    public function setAttributeType($type);

    /**
     * Returns a Key entity.
     *
     * A key is mapped with a row from the AttributeKeys table.
     * Use the object for example to retrieve the current attribute handle (e.g. 'meta_title').
     *
     * @return AttributeKeyInterface
     */
    public function getAttributeKey();

    /**
     * @param $key AttributeKeyInterface
     *
     * @return mixed
     */
    public function setAttributeKey($key);

    /**
     * Return a formatter object that provides an icon that will be shown in the list of attributes.
     *
     * Example implementation:
     * return new FontAwesomeIconFormatter('check-square');
     *
     * @return IconFormatterInterface
     */
    public function getIconFormatter();
}
