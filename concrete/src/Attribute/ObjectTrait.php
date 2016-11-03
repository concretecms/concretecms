<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Attribute\Value\EmptyRequestAttributeValue;
use Concrete\Core\Entity\Attribute\Value\Value as AttributeValue;
use Concrete\Core\Entity\Attribute\Value\Value\Value;

trait ObjectTrait
{
    /**
     * @return CategoryInterface
     */
    abstract public function getObjectAttributeCategory();

    /**
     * @param $ak
     * @param bool $createIfNotExists
     *
     * @return AttributeValue
     */
    abstract public function getAttributeValueObject($ak, $createIfNotExists = false);

    public function getAttribute($ak, $mode = false)
    {
        $value = $this->getAttributeValueObject($ak);
        if (is_object($value)) {
            return $value->getValue($mode);
        }
    }

    /**
     * @param $ak
     * @return \Concrete\Core\Entity\Attribute\Value\Value
     */
    public function getAttributeValue($ak)
    {
        $value = $this->getAttributeValueObject($ak);
        if (is_object($value)) {
            return $value;
        }
    }

    /**
     * @param AttributeKeyInterface | string $ak
     */
    public function clearAttribute($ak)
    {
        $value = $this->getAttributeValueObject($ak);
        if (is_object($value)) {
            $controller = $this->getObjectAttributeCategory();
            $controller->deleteValue($value);
            $category = $this->getObjectAttributeCategory();
            $indexer = $category->getSearchIndexer();
            if ($indexer) {
                $indexer->clearIndexEntry($category, $value, $this);
            }
        }
    }

    /**
     * Sets the attribute of a user info object to the specified value, and saves it in the database.
     *
     * @param AttributeKeyInterface | string $ak
     * @param mixed $value
     */
    public function setAttribute($ak, $value)
    {
        $orm = \Database::connection()->getEntityManager();

        $this->clearAttribute($ak);

        // Create the attribute value. Note, normally it would not be necessary to persist
        // This right after creating, but legacy attributes need the attribute value object in their
        // controller in order to save their data.
        $attributeValue = $this->getAttributeValueObject($ak, true);
        $controller = $attributeValue->getAttributeKey()->getController();
        $orm->persist($attributeValue);
        $orm->flush();
        $controller->setAttributeValue($attributeValue);

        if (!($value instanceof Value)) {
            if ($value instanceof EmptyRequestAttributeValue) {
                // LEGACY SUPPORT
                // If the passed $value object == EmptyRequestAttributeValue, we know we are dealing
                // with a legacy attribute type that's not using Doctrine. We have not returned an 
                // attribute value value object.
                $controller->saveForm($controller->post());
                unset($value);
            } else {
                $value = $controller->createAttributeValue($value);
            }
        }

        if ($value) {
            $attributeValue->setValue($value);
        }

        $orm->persist($attributeValue);
        $orm->flush();

        $category = $this->getObjectAttributeCategory();
        $indexer = $category->getSearchIndexer();
        if ($indexer) {
            $indexer->indexEntry($category, $attributeValue, $this);
        }

        return $attributeValue;
    }

}
