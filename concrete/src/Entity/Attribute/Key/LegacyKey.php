<?php
namespace Concrete\Core\Entity\Attribute\Key;

use Concrete\Core\Attribute\Category\LegacyCategory;
use Concrete\Core\Attribute\Value\EmptyRequestAttributeValue;
use Concrete\Core\Entity\Attribute\Value\LegacyValue;
use Concrete\Core\Entity\Attribute\Value\Value\Value;
use Doctrine\ORM\Mapping as ORM;

/**
 * @deprecated
 * @ORM\Entity
 * @ORM\Table(name="LegacyAttributeKeys")
 */
class LegacyKey extends Key
{

    public function getAttributeKeyCategoryHandle()
    {
        return 'legacy';
    }

    public function getAttributeKeyIconSRC()
    {
        $type = $this->getAttributeType();
        $env = \Environment::get();
        $url = $env->getURL(
            implode('/', array(DIRNAME_ATTRIBUTES . '/' . $type->getAttributeTypeHandle() . '/' . FILENAME_BLOCK_ICON)),
            $type->getPackageHandle()
        );
        return $url;
    }

    public function update($args)
    {
        $category = $this->getAttributeCategory();
        $controller = $category->getController();
        return $controller->updateFromRequest($this, \Request::getInstance());
    }

    public function delete()
    {
        $category = $this->getAttributeCategory();
        $controller = $category->getController();
        $controller->deleteKey($this);
    }

    public function setAttribute($o, $value)
    {
        /*
        // Clear attribute
        $orm = \Database::connection()->getEntityManager();

        $attributeValue = $o->getAttributeValueObject($this);
        if (is_object($attributeValue)) {
            $controller = $this->getObjectAttributeCategory();
            $controller->deleteValue($attributeValue);
            $category = $this->getAttributeCategory()->getController();
            $indexer = $category->getSearchIndexer();
            if ($indexer) {
                $indexer->clearIndexEntry($category, $value, $this);
            }
        }

        $attributeValue = new LegacyValue();
        $attributeValue->setAttributeKey($this);
        $controller = $this->getController();

        $orm->persist($attributeValue);
        $orm->flush();

        $controller->setAttributeValue($attributeValue);

        if (!($value instanceof Value)) {
            if ($value instanceof EmptyRequestAttributeValue) {
                $controller->saveForm($controller->post());
                unset($value);
            } else {
                $value = $controller->createAttributeValue($value);
            }
        }

        if ($value) {
            $value->getAttributeValues()->add($attributeValue);
            $attributeValue->setValue($value);
        }

        $orm->persist($attributeValue);
        $orm->flush();

        $category = $this->getAttributeCategory()->getController();
        $indexer = $category->getSearchIndexer();
        if ($indexer) {
            $indexer->indexEntry($category, $attributeValue, $this);
        }
        */

    }


}
