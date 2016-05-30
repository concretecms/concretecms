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

        $this->clearAttribute($ak);

        $attributeValue = $this->getAttributeValueObject($ak, true);
        $orm = \Database::connection()->getEntityManager();

        $controller = $attributeValue->getAttributeKey()->getController();

        if (!($value instanceof Value)) {
            if ($value instanceof EmptyRequestAttributeValue) {
                // If the passed $value object == EmptyRequestAttributeValue, we know we are dealing
                // with a legacy attribute type that's not using Doctrine. We have not returned an 
                // attribute value object. And that means that we need to create our OWN empty
                // attribute value object, and persist it first, before passing it to saveValue.
                $value = new AttributeValue\LegacyValue();
                $orm->persist($attributeValue);
                $orm->persist($value);
                $orm->flush();

                // Now that we have a legitimate attribute value value, we pass it to the the controller
                // which will then use it to populate the at* tables that old-school attributes use.
                $controller->setAttributeValue($value);
                $controller->saveForm($controller->post());
            } else {
                $value = $controller->createAttributeValue($value);
            }
        }

        $value->getAttributeValues()->add($attributeValue);
        $attributeValue->setValue($value);

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
