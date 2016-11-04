<?php
namespace Concrete\Core\Entity\Attribute\Key;

use Concrete\Core\Attribute\Category\LegacyCategory;
use Concrete\Core\Attribute\Value\EmptyRequestAttributeValue;
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



}
