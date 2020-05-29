<?php

namespace Concrete\Core\Permission\Key;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Foundation\ConcreteObject;
use Concrete\Core\Package\PackageList;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Permission\Assignment\Assignment as PermissionAssignment;
use Concrete\Core\Permission\Category as PermissionKeyCategory;
use Concrete\Core\Support\Facade\Application;
use Gettext\Translations;
use PDO;
use SimpleXMLElement;
use Concrete\Core\User\User;

/**
 * @property bool|int|string $pkID
 * @property string $pkName
 * @property string $pkHandle
 * @property string $pkDescription
 * @property bool|int|string $pkCategoryID
 * @property string $pkCategoryHandle
 * @property bool|int|string|null $pkgID
 * @property bool|int|string $pkCanTriggerWorkflow
 * @property bool|int|string $pkHasCustomClass
 * @property \Concrete\Core\Permission\ObjectInterface|null $permissionObjectToCheck
 */
abstract class Key extends ConcreteObject
{
    /**
     * Access type: inclusive.
     *
     * @var int
     */
    const ACCESS_TYPE_INCLUDE = 10;

    /**
     * Access type: exclusive.
     *
     * @var int
     */
    const ACCESS_TYPE_EXCLUDE = -1;

    /**
     * Access type: any.
     *
     * @var int
     */
    const ACCESS_TYPE_ALL = 0;

    /**
     * The object for which this permission is for (for example, a Page instance).
     *
     * @var \Concrete\Core\Permission\ObjectInterface|null
     */
    protected $permissionObject = null;

    /**
     * Get the identifiers (keys) and descriptions (values) of the access types.
     *
     * @return array
     */
    public function getSupportedAccessTypes()
    {
        $types = [
            self::ACCESS_TYPE_INCLUDE => t('Included'),
            self::ACCESS_TYPE_EXCLUDE => t('Excluded'),
        ];

        return $types;
    }

    /**
     * Returns whether a permission key can start a workflow.
     *
     * @return bool
     */
    public function canPermissionKeyTriggerWorkflow()
    {
        return !empty($this->pkCanTriggerWorkflow);
    }

    /**
     * Returns whether a permission key has a custom class.
     *
     * @return bool
     */
    public function permissionKeyHasCustomClass()
    {
        return !empty($this->pkHasCustomClass);
    }

    /**
     * Returns the name for this permission key.
     *
     * @return string
     */
    public function getPermissionKeyName()
    {
        return isset($this->pkName) ? $this->pkName : null;
    }

    /**
     * Returns the display name for this permission key (localized and escaped accordingly to $format).
     *
     * @param string $format = 'html'
     *    Escape the result in html format (if $format is 'html').
     *    If $format is 'text' or any other value, the display name won't be escaped.
     *
     * @return string
     */
    public function getPermissionKeyDisplayName($format = 'html')
    {
        $value = tc('PermissionKeyName', $this->getPermissionKeyName());
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    /**
     * Returns the handle for this permission key.
     *
     * @return string
     */
    public function getPermissionKeyHandle()
    {
        return isset($this->pkHandle) ? $this->pkHandle : null;
    }

    /**
     * Returns the description for this permission key.
     *
     * @return string
     */
    public function getPermissionKeyDescription()
    {
        return isset($this->pkDescription) ? $this->pkDescription : null;
    }

    /**
     * Returns the display description for this permission key (localized and escaped accordingly to $format).
     *
     * @param string $format = 'html'
     *    Escape the result in html format (if $format is 'html').
     *    If $format is 'text' or any other value, the display description won't be escaped.
     *
     * @return string
     */
    public function getPermissionKeyDisplayDescription($format = 'html')
    {
        $value = tc('PermissionKeyDescription', $this->getPermissionKeyDescription());
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    /**
     * Returns the ID for this permission key.
     *
     * @return int
     */
    public function getPermissionKeyID()
    {
        return isset($this->pkID) ? (int) $this->pkID : null;
    }

    /**
     * Returns the ID for the category of this permission key.
     *
     * @return int
     */
    public function getPermissionKeyCategoryID()
    {
        return isset($this->pkCategoryID) ? (int) $this->pkCategoryID : null;
    }

    /**
     * Returns the handle for the category of this permission key.
     *
     * @return string
     */
    public function getPermissionKeyCategoryHandle()
    {
        return isset($this->pkCategoryHandle) ? $this->pkCategoryHandle : null;
    }

    /**
     * Set the object for which this permission is for (for example, a Page instance).
     *
     * @param \Concrete\Core\Permission\ObjectInterface|null $object
     */
    public function setPermissionObject($object)
    {
        $this->permissionObject = $object;
    }

    /**
     * Set the actual object that should be checked for this permission (for example, a Page instance).
     *
     * @param \Concrete\Core\Permission\ObjectInterface|null $object
     */
    public function getPermissionObjectToCheck()
    {
        if (isset($this->permissionObjectToCheck) && is_object($this->permissionObjectToCheck)) {
            return $this->permissionObjectToCheck;
        } else {
            return $this->permissionObject;
        }
    }

    /**
     * Get the object for which this permission is for (for example, a Page instance).
     *
     * @return \Concrete\Core\Permission\ObjectInterface|null
     */
    public function getPermissionObject()
    {
        return $this->permissionObject;
    }

    /**
     * Get the list of all the defined permission keys.
     *
     * @return \Concrete\Core\Permission\Key\Key[]
     */
    public static function loadAll()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $permissionkeys = [];
        $txt = $app->make('helper/text');
        $e = $db->executeQuery(<<<'EOT'
select pkID, pkName, pkDescription, pkHandle, pkCategoryHandle, pkCanTriggerWorkflow, pkHasCustomClass, PermissionKeys.pkCategoryID, PermissionKeyCategories.pkgID
from PermissionKeys
inner join PermissionKeyCategories on PermissionKeyCategories.pkCategoryID = PermissionKeys.pkCategoryID
EOT
        );
        while (($r = $e->fetch(PDO::FETCH_ASSOC)) !== false) {
            if ($r['pkHasCustomClass']) {
                $class = '\\Core\\Permission\\Key\\' . $txt->camelcase($r['pkHandle'] . '_' . $r['pkCategoryHandle']) . 'Key';
            } else {
                $class = '\\Core\\Permission\\Key\\' . $txt->camelcase($r['pkCategoryHandle']) . 'Key';
            }
            $pkgHandle = $r['pkgID'] ? PackageList::getHandle($r['pkgID']) : null;
            $class = core_class($class, $pkgHandle);
            $pk = $app->make($class);
            $pk->setPropertiesFromArray($r);
            $permissionkeys[$r['pkHandle']] = $pk;
            $permissionkeys[$r['pkID']] = $pk;
        }

        $cache = $app->make('cache/request');
        if ($cache->isEnabled()) {
            $cache->getItem('permission_keys')->set($permissionkeys)->save();
        }

        return $permissionkeys;
    }

    /**
     * Does this permission key have a form (located at elements/permission/keys/<handle>.php)?
     *
     * @return bool
     */
    public function hasCustomOptionsForm()
    {
        $app = Application::getFacadeApplication();
        $locator = $app->make(FileLocator::class);
        $pkgHandle = $this->getPackageHandle();
        if ($pkgHandle) {
            $locator->addLocation(new FileLocator\PackageLocation($pkgHandle));
        }
        $record = $locator->getRecord(DIRNAME_ELEMENTS . '/' . DIRNAME_PERMISSIONS . '/' . DIRNAME_KEYS . '/' . $this->getPermissionKeyHandle() . '.php');

        return $record ? file_exists($record->getFile()) : false;
    }

    /**
     * Get the ID of the package that defines this permission key.
     *
     * @return int|null
     */
    public function getPackageID()
    {
        return isset($this->pkgID) ? (int) $this->pkgID : null;
    }

    /**
     * Get the handle of the package that defines this permission key.
     *
     * @return string|null
     */
    public function getPackageHandle()
    {
        $pkgID = $this->getPackageID();

        return $pkgID ? PackageList::getHandle($this->pkgID) : null;
    }

    /**
     * Returns the list of all permissions of this category.
     *
     * @param string $pkCategoryHandle
     * @param array $filters An array containing of field name => value (to be used directly in the SQL query)
     *
     * @return \Concrete\Core\Permission\Key\Key[]
     */
    public static function getList($pkCategoryHandle, $filters = [])
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $q = 'select pkID from PermissionKeys inner join PermissionKeyCategories on PermissionKeys.pkCategoryID = PermissionKeyCategories.pkCategoryID where pkCategoryHandle = ?';
        foreach ($filters as $key => $value) {
            $q .= ' and ' . $key . ' = ' . $value . ' ';
        }
        $r = $db->executeQuery($q, [$pkCategoryHandle]);
        $list = [];
        while (($pkID = $r->fetchColumn()) !== false) {
            $pk = self::load($pkID);
            if ($pk) {
                $list[] = $pk;
            }
        }

        return $list;
    }

    /**
     * Export this permission key to a SimpleXMLElement instance.
     *
     * @param \SimpleXMLElement $axml
     *
     * @return \SimpleXMLElement
     */
    public function export($axml)
    {
        $category = PermissionKeyCategory::getByID($this->pkCategoryID)->getPermissionKeyCategoryHandle();
        $pkey = $axml->addChild('permissionkey');
        $pkey->addAttribute('handle', $this->getPermissionKeyHandle());
        $pkey->addAttribute('name', $this->getPermissionKeyName());
        $pkey->addAttribute('description', $this->getPermissionKeyDescription());
        $pkey->addAttribute('package', $this->getPackageHandle());
        $pkey->addAttribute('category', $category);
        $this->exportAccess($pkey);

        return $pkey;
    }

    /**
     * Export the list of all permissions of this category to a SimpleXMLElement instance.
     *
     * @param \SimpleXMLElement $xml
     */
    public static function exportList($xml)
    {
        $categories = PermissionKeyCategory::getList();
        $pxml = $xml->addChild('permissionkeys');
        foreach ($categories as $cat) {
            $permissions = static::getList($cat->getPermissionKeyCategoryHandle());
            foreach ($permissions as $p) {
                $p->export($pxml);
            }
        }
    }

    /**
     * Get the list of permission keys defined by a package.
     * Note, this queries both the pkgID found on the PermissionKeys table AND any permission keys of a special type
     * installed by that package, and any in categories by that package.
     *
     * @param \Concrete\Core\Entity\Package|\Concrete\Core\Package\Package $pkg
     *
     * @return \Concrete\Core\Permission\Key\Key[]
     */
    public static function getListByPackage($pkg)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);

        $kina = ['-1'];
        $rs = $db->executeQuery('select pkCategoryID from PermissionKeyCategories where pkgID = ?', [$pkg->getPackageID()]);
        while (($pkCategoryID = $rs->fetchColumn()) !== false) {
            $kina[] = $pkCategoryID;
        }
        $kinstr = implode(',', $kina);

        $categories = [];
        /* @var \Concrete\Core\Permission\Category[] $categories */
        $list = [];
        $r = $db->executeQuery('select pkID, pkCategoryID from PermissionKeys where (pkgID = ? or pkCategoryID in (' . $kinstr . ')) order by pkID asc', [$pkg->getPackageID()]);
        while (($row = $r->fetch(PDO::FETCH_ASSOC)) !== false) {
            if (!isset($categories[$row['pkCategoryID']])) {
                $categories[$row['pkCategoryID']] = PermissionKeyCategory::getByID($row['pkCategoryID']);
            }
            $pkc = $categories[$row['pkCategoryID']];
            $pk = $pkc->getPermissionKeyByID($row['pkID']);
            $list[] = $pk;
        }

        return $list;
    }

    /**
     * Import a permission key from a SimpleXMLElement element.
     *
     * @param \SimpleXMLElement $pk
     *
     * @return \Concrete\Core\Permission\Key\Key
     */
    public static function import(SimpleXMLElement $pk)
    {
        if ($pk['package']) {
            $app = Application::getFacadeApplication();
            $pkg = $app->make(PackageService::class)->getByHandle($pk['package']);
        } else {
            $pkg = null;
        }

        return self::add(
            $pk['category'],
            $pk['handle'],
            $pk['name'],
            $pk['description'],
            $pk['can-trigger-workflow'] ? 1 : 0,
            $pk['has-custom-class'] ? 1 : 0,
            $pkg
        );
    }

    /**
     * Get a permission key given its ID.
     *
     * @param int $pkID
     *
     * @return \Concrete\Core\Permission\Key\Key|null
     */
    public static function getByID($pkID)
    {
        $keys = null;
        $app = Application::getFacadeApplication();
        $cache = $app->make('cache/request');
        if ($cache->isEnabled()) {
            $item = $cache->getItem('permission_keys');
            if (!$item->isMiss()) {
                $keys = $item->get();
            }
        }
        if ($keys === null) {
            $keys = self::loadAll();
        }

        return isset($keys[$pkID]) ? $keys[$pkID] : null;
    }

    /**
     * Get a permission key given its handle.
     *
     * @param string $pkHandle
     *
     * @return \Concrete\Core\Permission\Key\Key|null
     */
    public static function getByHandle($pkHandle)
    {
        $keys = null;
        $app = Application::getFacadeApplication();
        $cache = $app->make('cache/request');
        if ($cache->isEnabled()) {
            $item = $cache->getItem('permission_keys');
            if (!$item->isMiss()) {
                $keys = $item->get();
            }
        }
        if ($keys === null) {
            $keys = self::loadAll();
        }

        return isset($keys[$pkHandle]) ? $keys[$pkHandle] : null;
    }

    /**
     * Adds an permission key.
     *
     * @param string $pkCategoryHandle
     * @param string $pkHandle
     * @param string $pkName
     * @param string $pkDescription
     * @param bool $pkCanTriggerWorkflow
     * @param bool $pkHasCustomClass
     * @param \Concrete\Core\Entity\Package|\Concrete\Core\Package\Package|null $pkg
     */
    public static function add($pkCategoryHandle, $pkHandle, $pkName, $pkDescription, $pkCanTriggerWorkflow, $pkHasCustomClass, $pkg = false)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);

        $pkCategoryID = $db->fetchColumn('select pkCategoryID from PermissionKeyCategories where pkCategoryHandle = ?', [$pkCategoryHandle]);
        $db->insert('PermissionKeys', [
            'pkHandle' => $pkHandle,
            'pkName' => $pkName,
            'pkDescription' => $pkDescription,
            'pkCategoryID' => $pkCategoryID,
            'pkCanTriggerWorkflow' => $pkCanTriggerWorkflow ? 1 : 0,
            'pkHasCustomClass' => $pkHasCustomClass ? 1 : 0,
            'pkgID' => is_object($pkg) ? $pkg->getPackageID() : 0,
        ]);
        $pkID = $db->lastInsertId();
        $keys = self::loadAll();

        return $keys[$pkID];
    }

    /**
     * Mark this permission key as having (or not) a custom class.
     *
     * @param bool $pkHasCustomClass
     */
    public function setPermissionKeyHasCustomClass($pkHasCustomClass)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->executeQuery('update PermissionKeys set pkHasCustomClass = ? where pkID = ?', [$pkHasCustomClass ? 1 : 0, $this->getPermissionKeyID()]);
        self::loadAll();
    }

    /**
     * @deprecated use the validate() method
     */
    public function can()
    {
        return $this->validate();
    }

    /**
     * Check if the current user is of for this key and its current permission object (if any).
     *
     * @return bool
     */
    public function validate()
    {
        $app = Application::getFacadeApplication();
        $u = $app->make(User::class);
        if ($u->isSuperUser()) {
            return true;
        }
        $cache = $app->make('cache/request');
        $object = $this->getPermissionObject();
        if (is_object($object)) {
            $identifier = sprintf('permission/key/%s/%s', $this->getPermissionKeyHandle(), $object->getPermissionObjectIdentifier());
        } else {
            $identifier = sprintf('permission/key/%s', $this->getPermissionKeyHandle());
        }
        $item = $cache->getItem($identifier);
        if (!$item->isMiss()) {
            return $item->get();
        }
        $pae = $this->getPermissionAccessObject();
        if (is_object($pae)) {
            $valid = $pae->validate();
        } else {
            $valid = false;
        }

        $cache->save($item->set($valid));

        return $valid;
    }

    /**
     * Delete this permission key.
     */
    public function delete()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $db->executeQuery('delete from PermissionKeys where pkID = ?', [$this->getPermissionKeyID()]);
        self::loadAll();
    }

    /**
     * A shortcut for grabbing the current assignment and passing into that object.
     *
     * @return \Concrete\Core\Permission\Access\ListItem\ListItem[]
     */
    public function getAccessListItems()
    {
        $obj = $this->getPermissionAccessObject();
        if (!$obj) {
            return [];
        }
        $args = func_get_args();
        switch (count($args)) {
            case 0:
                return $obj->getAccessListItems();
            case 1:
                return $obj->getAccessListItems($args[0]);
            case 2:
                return $obj->getAccessListItems($args[0], $args[1]);
            case 3:
                return $obj->getAccessListItems($args[0], $args[1], $args[2]);
            default:
                return call_user_func_array([$obj, 'getAccessListItems'], $args);
        }
    }

    /**
     * @return \Concrete\Core\Permission\Assignment\Assignment
     */
    public function getPermissionAssignmentObject()
    {
        $permissionObject = $this->getPermissionObject();
        if (is_object($permissionObject)) {
            $app = Application::getFacadeApplication();
            $className = $permissionObject->getPermissionAssignmentClassName();
            $targ = $app->make($className);
            $targ->setPermissionObject($permissionObject);
        } else {
            $targ = new PermissionAssignment();
        }
        $targ->setPermissionKeyObject($this);

        return $targ;
    }

    /**
     * @return \Concrete\Core\Permission\Access\Access|null
     */
    public function getPermissionAccessObject()
    {
        $targ = $this->getPermissionAssignmentObject();

        return $targ->getPermissionAccessObject();
    }

    /**
     * @return int|null
     */
    public function getPermissionAccessID()
    {
        $pa = $this->getPermissionAccessObject();

        return $pa ? $pa->getPermissionAccessID() : null;
    }

    /**
     * @param \SimpleXMLElement $pxml
     */
    public function exportAccess($pxml)
    {
        // by default we don't. but tasks do
    }

    /**
     * Export the strings that should be translated.
     *
     * @return \Gettext\Translations
     */
    public static function exportTranslations()
    {
        $translations = new Translations();
        $categories = PermissionKeyCategory::getList();
        foreach ($categories as $cat) {
            $permissions = static::getList($cat->getPermissionKeyCategoryHandle());
            foreach ($permissions as $p) {
                $translations->insert('PermissionKeyName', $p->getPermissionKeyName());
                if ($p->getPermissionKeyDescription()) {
                    $translations->insert('PermissionKeyDescription', $p->getPermissionKeyDescription());
                }
            }
        }

        return $translations;
    }

    /**
     * Load a permission key by its ID (or whatever is passed to $loadBy).
     *
     * @param int|mixed $key the ID (or the value of the $loadBy field) of the key to be loaded
     * @param string $loadBy the field to be used to locate the permission key
     *
     * @return \Concrete\Core\Permission\Key\Key|null
     */
    protected static function load($key, $loadBy = 'pkID')
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $txt = $app->make('helper/text');
        $r = $db->fetchAssoc(
            <<<EOT
select pkID, pkName, pkDescription, pkHandle, pkCategoryHandle, pkCanTriggerWorkflow, pkHasCustomClass, PermissionKeys.pkCategoryID, PermissionKeyCategories.pkgID
from PermissionKeys
inner join PermissionKeyCategories on PermissionKeyCategories.pkCategoryID = PermissionKeys.pkCategoryID where {$loadBy} = ?
EOT
            ,
            [$key]
        );
        if ($r === false) {
            return null;
        }
        if ($r['pkHasCustomClass']) {
            $class = '\\Core\\Permission\\Key\\' . $txt->camelcase($r['pkHandle'] . '_' . $r['pkCategoryHandle']) . 'Key';
        } else {
            $class = '\\Core\\Permission\\Key\\' . $txt->camelcase($r['pkCategoryHandle']) . 'Key';
        }
        $pkgHandle = $r['pkgID'] ? PackageList::getHandle($r['pkgID']) : null;
        $class = core_class($class, $pkgHandle);
        $pk = $app->make($class);
        $pk->setPropertiesFromArray($r);

        return $pk;
    }
}
