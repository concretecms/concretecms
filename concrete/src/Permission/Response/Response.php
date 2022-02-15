<?php
namespace Concrete\Core\Permission\Response;

use Concrete\Core\Cache\Level\RequestCache;
use Exception;
use Concrete\Core\User\User;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Permission\Category as PermissionKeyCategory;

class Response
{
    protected $app;
    /** @var \Concrete\Core\Permission\ObjectInterface */
    protected $object;
    /** @var PermissionKeyCategory */
    protected $category;
    public static $cache = array();

    public function __construct()
    {
        $this->app = Application::getFacadeApplication();
    }

    /**
     * Sets the current permission object to the object provided, this object should implement the Permission ObjectInterface.
     *
     * @param \Concrete\Core\Permission\ObjectInterface $object
     */
    public function setPermissionObject($object)
    {
        $this->object = $object;
    }

    /**
     * Retrieves the current permission object.
     */
    public function getPermissionObject()
    {
        return $this->object;
    }

    /**
     * Sets the current Permission Category object to an appropriate PermissionKeyCategory.
     *
     * @param PermissionKeyCategory $category
     */
    public function setPermissionCategoryObject($category)
    {
        $this->category = $category;
    }

    /**
     * Returns an error constant if an error is present, false if there are no errors.
     *
     * @return bool|int
     */
    public function testForErrors()
    {
        return false;
    }

    /**
     * Passing in any object that implements the ObjectInterface, retrieve the Permission Response object.
     *
     * @param \Concrete\Core\Permission\ObjectInterface $object
     *
     * @return \Concrete\Core\Permission\Response\Response
     */
    public static function getResponse($object)
    {
        $app = Application::getFacadeApplication();
        $cache = $app->make('cache/request');
        $identifier = sprintf('permission/response/%s/%s', get_class($object), $object->getPermissionObjectIdentifier());
        $item = $cache->getItem($identifier);
        if (!$item->isMiss()) {
            return $item->get();
        }

        $className = $object->getPermissionResponseClassName();
        /** @var \Concrete\Core\Permission\Response\Response $pr */
        $pr = $app->make($className);
        if ($object->getPermissionObjectKeyCategoryHandle()) {
            $category = PermissionKeyCategory::getByHandle($object->getPermissionObjectKeyCategoryHandle());
            $pr->setPermissionCategoryObject($category);
        }
        $pr->setPermissionObject($object);
        $cache->save($item->set($pr));

        return $pr;
    }

    /**
     * This function returns true if the user has permission to the object, or false if they do not have access.
     *
     * @param string $permissionHandle A Permission Key Handle
     * @param array $args Arguments to pass to the PermissionKey object's validate function
     *
     * @return bool
     *
     * @throws Exception
     */
    public function validate($permissionHandle, $args = array())
    {
        // If arguments is empty, we can cache result
        if (empty($args) && is_object($this->category) && is_object($this->object)) {
            /** @var RequestCache $cache */
            $cache = $this->app->make('cache/request');
            $identifier = sprintf(
                'permission/validate/%s/%s/%s',
                $this->category->getPermissionKeyCategoryHandle(),
                $this->object->getPermissionObjectIdentifier(),
                $permissionHandle
            );
            $cacheItem = $cache->getItem($identifier);
            if ($cacheItem->isHit()) {
                return $cacheItem->get();
            }
        }

        $u = $this->app->make(User::class);
        if ($u->isSuperUser()) {
            return true;
        }
        if (!is_object($this->category)) {
            throw new Exception(t('Unable to get category for permission %s', $permissionHandle));
        }
        $pk = $this->category->getPermissionKeyByHandle($permissionHandle);
        if (!$pk) {
            throw new Exception(t('Unable to get permission key for %s', $permissionHandle));
        }
        $pk->setPermissionObject($this->object);

        $result = call_user_func_array(array($pk, 'validate'), $args);
        if (isset($cache) && isset($cacheItem)) {
            $cache->save($cacheItem->set($result));
        }

        return $result;
    }

    public function __call($f, $a)
    {
        $permission = substr($f, 3);
        /** @var \Concrete\Core\Utility\Service\Text $textHelper */
        $textHelper = $this->app->make('helper/text');
        $permission = $textHelper->uncamelcase($permission);

        return $this->validate($permission, $a);
    }
}
