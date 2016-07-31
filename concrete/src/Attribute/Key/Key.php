<?php
namespace Concrete\Core\Attribute\Key;

use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Attribute\Category\LegacyCategory;
use Concrete\Core\Attribute\Value\EmptyRequestAttributeValue;
use Concrete\Core\Entity\Attribute\Key\LegacyKey;
use Concrete\Core\Entity\Attribute\Value\LegacyValue;
use Concrete\Core\Support\Facade\Facade;

class Key extends Facade implements AttributeKeyInterface
{
    public static function getFacadeAccessor()
    {
        return 'Concrete\Core\Attribute\Key\Factory';
    }


    // EVERYTHING BELOW THIS IS DEPRECATED AND WILL BE REMOVED AT SOME POINT
    // THE ONLY REASON IT IS HERE IS TO FACILITATE CUSTOM ATTRIBUTE KEY CATEGORIES
    // IN 5.7 THAT EXTEND THIS FILE.

    /**
     * @var LegacyKey
     */
    protected $legacyAttributeKey;

    /**
     * @deprecated
     */
    public function getController()
    {
        return $this->legacyAttributeKey->getController();
    }

    public function __toString()
    {
        return (string) $this->legacyAttributeKey->getAttributeKeyID();
    }

    /**
     * @deprecated
     */
    public function getAttributeKeyID()
    {
        return $this->legacyAttributeKey->getAttributeKeyID();
    }

    /**
     * @deprecated
     */
    public function getAttributeKeyHandle()
    {
        return $this->legacyAttributeKey->getAttributeKeyHandle();
    }

    /**
     * @deprecated
     */
    public function getAttributeType()
    {
        return $this->legacyAttributeKey->getAttributeType();
    }

    /**
     * @deprecated
     */
    public function isAttributeKeySearchable()
    {
        return $this->legacyAttributeKey->isAttributeKeySearchable();
    }

    public function getSearchIndexer()
    {
        return $this->legacyAttributeKey->getSearchIndexer();
    }

    /**
     * This is how old attribute keys used to install themselves. They extended
     * this class and would call parent::add(). Do NOT use this method. It is here
     * for backward compatibility.
     * @deprecated
     */
    public static function add($handle, $type, $args, $pkg = false)
    {
        $category = Category::getByHandle($handle);
        $controller = $category->getController();
        if (!($controller instanceof LegacyCategory)) {
            throw new \Exception(t('You cannot use the legacy attribute add method with any category but the legacy category.'));
        }

        return $controller->addAttributeKey($type, $args, $pkg);
    }

    /**
     * In 5.7 and earlier, if a subclassed Key object called load, it was loading the
     * core data of an attribute key. we're going to load that data into an internal
     * legacy key object that we can keep around to pass calls to for attribute keys that
     * incorrectly subclass this Key object.
     * @deprecated
     */
    public function load($akID)
    {
        $em = $this->getFacadeApplication()->make('Doctrine\ORM\EntityManager');
        $this->legacyAttributeKey = $em->find('Concrete\Core\Entity\Attribute\Key\LegacyKey', $akID);
    }

    /**
     * This is here to fulfill this type of code
     * $key = StoreOrderKey::getByID(10); Which then calls $ak = new self(); $ak->load(10);
     * if ($ak->getAttributeKeyID()) {...}
     * @deprecated
     */
    public function __call($name, $arguments)
    {
        if (is_object($this->legacyAttributeKey)) {
            return call_user_func_array([$this->legacyAttributeKey, $name], $arguments);
        } else {
            throw new \Exception(t('Unable to retrieve legacy attribute key for method: %s', $name));
        }
    }

    /**
     * @deprecated
     */
    public function setPropertiesFromArray($array)
    {
        return array_to_object($this, $array);
    }

    /**
     * @deprecated
     */
    public function saveAttributeForm($o)
    {
        return $this->saveAttribute($o);
    }

    /**
     * @deprecated
     */
    protected function saveAttribute($attributeValue, $passedValue = false)
    {
        /** @var \Concrete\Core\Attribute\Type $at */
        $at = $this->getAttributeType();
        $at->getController()->setAttributeKey($this);
        $at->getController()->setAttributeValue($attributeValue);
        if ($passedValue) {
            $at->getController()->saveValue($passedValue);
        } else {
            $controller = $this->getController();
            $value = $controller->createAttributeValueFromRequest();
            if (!($value instanceof EmptyRequestAttributeValue)) {
                // This is a new v8 attribute type

                $attributeValue->setValue($value);

                $orm = \Database::connection()->getEntityManager();
                $orm->persist($value);
                $orm->flush();

                $category = $this->legacyAttributeKey->getAttributeCategory()->getController();
                $indexer = $category->getSearchIndexer();
                if ($indexer) {
                    $indexer->indexEntry($category, $attributeValue, $this);
                }

                return $attributeValue;
            }
        }
    }


    /**
     * @deprecated
     */
    public function addAttributeValue()
    {
        $value = new LegacyValue();
        $value->setAttributeKey($this->legacyAttributeKey);
        $orm = \Database::connection()->getEntityManager();
        $orm->persist($value);
        $orm->flush();
        return $value;
    }

    /**
     * @deprecated
     */
    public function getSearchIndexFieldDefinition()
    {
        return $this->searchIndexFieldDefinition;
    }

    /**
     * @deprecated
     */
    public function getIndexedSearchTable()
    {
        return false;
    }

    /**
     * @deprecated
     */
    public function setAttribute($o, $value)
    {
        $this->saveAttribute($o, $value);
    }


}
