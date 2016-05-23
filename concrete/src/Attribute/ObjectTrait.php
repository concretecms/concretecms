<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Attribute\Category\CategoryInterface;
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
        }
        $this->reindexAttributes();
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

        $controller = $attributeValue->getAttributeKey()->getController();
        if (!($value instanceof Value)) {
            $value = $controller->saveValue($value);
        }

        $value->getAttributeValues()->add($attributeValue);
        $attributeValue->setValue($value);

        $orm = \Database::connection()->getEntityManager();
        $orm->persist($attributeValue);
        $orm->flush();

        $this->reindexAttributes();

        return $attributeValue;
    }

    /**
     * Reindex the attributes on this object.
     */
    public function reindexAttributes()
    {
        $category = $this->getObjectAttributeCategory();
        $indexer = $category->getSearchIndexer();
        if ($indexer) {
            $indexer->indexEntry($category, $this);
        }
    }
}
