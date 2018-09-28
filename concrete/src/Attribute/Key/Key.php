<?php
namespace Concrete\Core\Attribute\Key;

use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Attribute\Category\LegacyCategory;
use Concrete\Core\Attribute\Value\EmptyRequestAttributeValue;
use Concrete\Core\Entity\Attribute\Key\LegacyKey;
use Concrete\Core\Entity\Attribute\Value\LegacyValue;
use Concrete\Core\Entity\Attribute\Value\Value\AbstractValue;
use Concrete\Core\Entity\Attribute\Value\Value\Value;
use Concrete\Core\Support\Facade\Facade;

class Key extends Facade implements AttributeKeyInterface
{
    public static function getFacadeAccessor()
    {
        return 'Concrete\Core\Attribute\Key\Factory';
    }

    /**
     * @deprecated
     * Move to new location.
     */
    public static function exportTranslations()
    {
        $factory = static::getFacadeRoot();
        $translations = $factory->exportTranslations();
        return $translations;
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
        if (isset($this->legacyAttributeKey)) {
            return $this->legacyAttributeKey->getController();
        }
    }

    public function __toString()
    {
        if (isset($this->legacyAttributeKey)) {
            return (string) $this->legacyAttributeKey->getAttributeKeyID();
        }
    }

    /**
     * @deprecated
     */
    public function getAttributeKeyID()
    {
        if (isset($this->legacyAttributeKey)) {
            return $this->legacyAttributeKey->getAttributeKeyID();
        }
    }

    /**
     * @deprecated
     */
    public function getAttributeKeyHandle()
    {
        if (isset($this->legacyAttributeKey)) {
            return $this->legacyAttributeKey->getAttributeKeyHandle();
        }
    }

    /**
     * @deprecated
     */
    public function getAttributeType()
    {
        if (isset($this->legacyAttributeKey)) {
            return $this->legacyAttributeKey->getAttributeType();
        }
    }

    /**
     * @deprecated
     */
    public function isAttributeKeySearchable()
    {
        if (isset($this->legacyAttributeKey)) {
            return $this->legacyAttributeKey->isAttributeKeySearchable();
        }
    }

    public function getSearchIndexer()
    {
        if (isset($this->legacyAttributeKey)) {
            return $this->legacyAttributeKey->getSearchIndexer();
        }
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
        if (isset($this->legacyAttributeKey) && is_object($this->legacyAttributeKey)) {
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
        $controller = $this->getController();
        $orm = \Database::connection()->getEntityManager();

        $genericValue = $orm->find('Concrete\Core\Entity\Attribute\Value\Value\Value', $attributeValue->getAttributeValueID());

        if (is_object($genericValue)) {
            // delete the attribute value value
            $legacyValue = new LegacyValue();
            $legacyValue->setAttributeKey($this->legacyAttributeKey);
            $legacyValue->setGenericValue($genericValue);
            $valueValue = $legacyValue->getValueObject();
            if (is_object($valueValue)) {
                $orm->remove($valueValue);
            }
            $orm->flush();
        }

        if ($passedValue) {
            $value = $controller->createAttributeValue($passedValue);
        } else {
            $value = $controller->createAttributeValueFromRequest();
        }

        /**
         * @var $value AbstractValue
         */
        if (!($value instanceof EmptyRequestAttributeValue)) {
            // This is a new v8 attribute type

            $value->setGenericValue($genericValue);
            $orm->persist($value);
            $orm->flush();

            $category = $this->legacyAttributeKey->getAttributeCategory();
            $indexer = $category->getSearchIndexer();
            if ($indexer) {
                $indexer->indexEntry($category, $attributeValue, $this);
            }

            return $attributeValue;
        }
    }


    /**
     * @deprecated
     */
    public function addAttributeValue()
    {
        $orm = \Database::connection()->getEntityManager();
        $genericValue = new Value();
        $genericValue->setAttributeKey($this->legacyAttributeKey);
        $orm->persist($genericValue);
        $orm->flush();

        $value = new LegacyValue();
        $value->setAttributeKey($this->legacyAttributeKey);
        $value->setGenericValue($genericValue);
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

    /**
     * @deprecated
     */
    public function reindex($table, $columnHeaders, $attribs, $rs = null)
    {
        /** @var \Concrete\Core\Database\Connection $db */
        $db = \Database::connection();
        $sm = $db->getSchemaManager();

        /** @var \Doctrine\DBAL\Schema\Column[] $columns */
        $columns = $sm->listTableColumns($table);

        $attribs->rewind();
        while ($attribs->valid()) {
            $column = 'ak_' . $attribs->key();
            if (is_array($attribs->current())) {
                foreach ($attribs->current() as $key => $value) {
                    $col = $column . '_' . $key;
                    if (isset($columns[strtolower($col)])) {
                        $columnHeaders[$col] = $value;
                    }
                }
            } else {
                if (isset($columns[strtolower($column)])) {
                    $columnHeaders[$column] = $attribs->current();
                }
            }

            $attribs->next();
        }

        $db->insert($table, $columnHeaders);
    }


}
