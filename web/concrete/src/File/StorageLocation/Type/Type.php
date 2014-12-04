<?php
namespace Concrete\Core\File\StorageLocation\Type;
use Concrete\Core\File\StorageLocation\StorageLocation;
use Concrete\Core\Package\PackageList;
use Database;
use Core;
use Environment;

/**
 * @Entity
 * @Table(name="FileStorageLocationTypes")
 */
class Type
{

    /**
     * @Column(type="text")
     */
    protected $fslTypeHandle;

    /**
     * @Column(type="text")
     */
    protected $fslTypeName;

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected $fslTypeID;

    /**
     * @Column(type="integer")
     */
    protected $pkgID = 0;

    /**
     * @return string
     */
    public function getHandle()
    {
        return $this->fslTypeHandle;
    }

    /**
     * @return int
     */
    public function getID()
    {
        return $this->fslTypeID;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->fslTypeName;
    }

    /**
     * @return int
     */
    public function getPackageID()
    {
        return $this->pkgID;
    }

    /**
     * @return null|string
     */
    public function getPackageHandle()
    {
        return PackageList::getHandle($this->pkgID);
    }

    /**
     * @return \Concrete\Core\File\StorageLocation\Configuration\ConfigurationInterface
     */
    public function getConfigurationObject()
    {
        if ($this->getPackageID()) {
            return Core::make('\\Concrete\\Package\\' . camelcase($this->getPackageHandle()) . '\\Core\\File\\StorageLocation\\Configuration\\'
                . camelcase($this->getHandle()) . 'Configuration');
        } else {
            return Core::make('\\Concrete\\Core\\File\\StorageLocation\\Configuration\\'
                . camelcase($this->getHandle()) . 'Configuration');
        }
    }

    /**
     * @param string $fslTypeHandle
     * @param string $fslTypeName
     * @param int|\Package $pkg
     * @return \Concrete\Core\File\StorageLocation\Type\Type
     */
    public static function add($fslTypeHandle, $fslTypeName, $pkg = false)
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        $o = new static();
        $o->fslTypeHandle = $fslTypeHandle;
        $o->fslTypeName = $fslTypeName;
        if ($pkg instanceof \Concrete\Core\Package\Package) {
            $o->pkgID = $pkg->getPackageID();
        }
        $em->persist($o);
        $em->flush();
        return $o;
    }

    /**
     * @param int $id
     * @return null|\Concrete\Core\File\StorageLocation\Type\Type
     */
    public static function getByID($id)
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        $r = $em->find('\Concrete\Core\File\StorageLocation\Type\Type', $id);
        return $r;
    }


    /**
     * @param $fslTypeHandle
     * @return \Concrete\Core\File\StorageLocation\Type\Type
     */
    public static function getByHandle($fslTypeHandle)
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        $type = $em->getRepository('\Concrete\Core\File\StorageLocation\Type\Type')->findOneBy(
            array('fslTypeHandle' => $fslTypeHandle
         ));
        return $type;
    }


    /**
     * Returns an array of \Concrete\Core\File\StorageLocation\Type\Type objects.
     * @return \Concrete\Core\File\StorageLocation\Type\Type[]
     */
    public static function getList()
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        return $em->getRepository('\Concrete\Core\File\StorageLocation\Type\Type')->findBy(
            array(), array('fslTypeID' => 'asc')
        );
    }

    /**
     * @return bool
     */
    public function hasOptionsForm() {
        $env = Environment::get();
        $rec = $env->getRecord(DIRNAME_ELEMENTS .
            '/' . DIRNAME_FILE_STORAGE_LOCATION_TYPES .
            '/' . $this->getHandle() . '.php',
        $this->getPackageHandle());
        return $rec->exists();
    }

    /**
     * @param bool|StorageLocation $location
     */
    public function includeOptionsForm($location = false) {
        $configuration = $this->getConfigurationObject();
        if ($location instanceof StorageLocation) {
            $configuration = $location->getConfigurationObject();
        }
        \View::element(DIRNAME_FILE_STORAGE_LOCATION_TYPES . '/' . $this->getHandle(),
        array(
            'type' => $this,
            'location' => $location,
            'configuration' => $configuration
        ), $this->getPackageHandle());
    }

    /**
     * Return an array of AuthenticationTypes that are associated with a specific package.
     * @param \Package $pkg
     * @return \Concrete\Core\File\StorageLocation\Type\Type[]
     */
    public static function getListByPackage(\Package $pkg)
    {
        $db = Database::get();
        $em = $db->getEntityManager();
        return $em->getRepository('\Concrete\Core\File\StorageLocation\Type\Type')->findBy(
            array('pkgID' => $pkg->getPackageID()), array('fslTypeID' => 'asc')
        );
    }

    /**
     * Removes the storage type if no configurations exist.
     * @throws \Exception
     * @return bool
     */
    public function delete()
    {
        $list = StorageLocation::getList();
        foreach($list as $item) {
            if($item->getTypeObject()->getHandle() == $this->getHandle()) {
                throw new \Exception(t('Please remove all storage locations using this storage type.'));
            }
        }

        $db = \Database::get();
        $em = $db->getEntityManager();
        $em->remove($this);
        $em->flush();
        return true;
    }


}