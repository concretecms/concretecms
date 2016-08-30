<?php
namespace Concrete\Core\Express\Attribute;

use Concrete\Core\Attribute\AttributeKeyHandleGeneratorInterface;
use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Entity\Attribute\Key\ExpressKey;
use Concrete\Core\Entity\Attribute\Key\Key;

class AttributeKeyHandleGenerator implements AttributeKeyHandleGeneratorInterface
{

    protected $category;

    public function __construct(ExpressCategory $category)
    {
        $this->category = $category;
    }

    protected function handleIsAvailable($handleToTest, Key $existingKey)
    {
        /*
        $q = $this->category->getEntityManager()->createQuery(
            'select ek from Concrete\Core\Entity\Attribute\Key\ExpressKey ek where ek.akHandle = :akHandle and ek.akID <> :akID'
        );
        $q->setParameter('akHandle', $handleToTest);
        $q->setParameter('akID', $existingKey->getAttributeKeyID());
        $q->setMaxResults(1);
        $result = $q->getOneOrNullResult();
        return is_object($result);
        */

        $key = $this->category->getByHandle($handleToTest);
        if (is_object($key)) {
            if ($key->getAttributeKeyID() != $existingKey->getAttributeKeyID()) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    /**
     * @param ExpressKey $key
     * @return string
     */
    public function generate(Key $key)
    {
        $name = $key->getAttributeKeyName();
        $entity = $key->getEntity();

        /**
         * @var $text Text
         */
        $text = \Core::make('helper/text');
        $baseHandle = $text->handle($entity->getName()) .
            '_' . $text->handle($name);

        if ($this->handleIsAvailable($baseHandle, $key)) {
            return $baseHandle;
        }
        $suffix = 2;
        $handle = $baseHandle . '_' . $suffix;
        while (!$this->handleIsAvailable($handle, $key)) {
            $suffix++;
            $handle = $baseHandle . '_' . $suffix;
        }

        return $handle;
    }

}
