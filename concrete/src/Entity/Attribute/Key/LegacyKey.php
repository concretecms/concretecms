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
        $controller = $this->getAttributeCategory();
        return $controller->updateFromRequest($this, \Request::getInstance());
    }

    public function delete()
    {
        $em = \Database::connection()->getEntityManager();
        $em->remove($this);
        $em->flush();
    }



}
