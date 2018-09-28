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
     * @return \Concrete\Core\Entity\Attribute\Type
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

    /**
     * This method is called any time an attribute key is created or updated.
     * $data is simply the POST values from the form.
     *
     * @param array $data
     */
    public function saveKey($data);

    /**
     * This value will be used by the search index.
     *
     * @return string|int
     */
    public function getSearchIndexValue();

    /**
     * Is run when an attribute is saved through the standard user interfaces
     * like the sitemap attributes dialog, the attributes panel, or the user attributes slideouts.
     *
     * @return AttributeValueInterface
     */
    public function createAttributeValueFromRequest();

    /**
     * Is run whenever $object->setAttribute('my_property_location_attribute', $value)
     * is run through code, with whatever you happen to pass through.
     *
     * @param $mixed
     *
     * @return AttributeValueInterface
     */
    public function createAttributeValue($mixed);

    /**
     * Is used to determine the name of the entity used to store the attribute value.
     * You can reuse this throughout your controllers, but it's used by the getAttributeValueObject()
     * method in the base controller to retrieve the relevant attribute data value object.
     *
     * @return string
     */
    public function getAttributeValueClass();
}
