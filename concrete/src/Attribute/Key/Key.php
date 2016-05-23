<?php
namespace Concrete\Core\Attribute\Key;

use Concrete\Core\Attribute\Category\LegacyCategory;
use Concrete\Core\Support\Facade\Facade;

class Key extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'Concrete\Core\Attribute\Key\Factory';
    }

    protected $legacyAttributeKey;

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

}
