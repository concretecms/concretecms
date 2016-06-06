<?php
namespace Concrete\Core\Entity\Attribute\Key;

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

    public function addAttributeValue()
    {

    }

    public function setAttribute($o, $value)
    {

        $attributeValue = $o->getAttributeValueObject($this);
        if (is_object($attributeValue)) {
            $controller = $this->getObjectAttributeCategory();
            $controller->deleteValue($attributeValue);
        }


        $attributeValue = $o->getAttributeValueObject($this, true);

        $controller = $attributeValue->getAttributeKey()->getController();
        $controller->saveValue($value);

        //$this->reindexAttributes();

    }


}
