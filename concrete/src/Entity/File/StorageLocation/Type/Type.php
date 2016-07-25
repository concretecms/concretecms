<?php
namespace Concrete\Core\Entity\File\StorageLocation\Type;

use Concrete\Core\File\StorageLocation\StorageLocation;
use Concrete\Core\Package\PackageList;
use Database;
use Core;
use Environment;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="FileStorageLocationTypes")
 */
class Type
{
    /**
     * @ORM\Column(type="text")
     */
    public $fslTypeHandle;

    /**
     * @ORM\Column(type="text")
     */
    public $fslTypeName;

    /**
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    public $fslTypeID;

    /**
     * @ORM\Column(type="integer")
     */
    public $pkgID = 0;

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
        $class = core_class('\\Core\\File\\StorageLocation\\Configuration\\' . camelcase($this->getHandle()) . 'Configuration', $this->getPackageHandle());

        return Core::make($class);
    }

    /**
     * @return bool
     */
    public function hasOptionsForm()
    {
        $env = Environment::get();
        $rec = $env->getRecord(DIRNAME_ELEMENTS .
            '/' . DIRNAME_FILE_STORAGE_LOCATION_TYPES .
            '/' . $this->getHandle() . '.php',
        $this->getPackageHandle());

        return $rec->exists();
    }

    /**
     * @param bool|\Concrete\Core\Entity\File\StorageLocation\StorageLocation $location
     */
    public function includeOptionsForm($location = false)
    {
        $configuration = $this->getConfigurationObject();
        if ($location instanceof \Concrete\Core\Entity\File\StorageLocation\StorageLocation) {
            $configuration = $location->getConfigurationObject();
        }
        \View::element(DIRNAME_FILE_STORAGE_LOCATION_TYPES . '/' . $this->getHandle(),
        array(
            'type' => $this,
            'location' => $location,
            'configuration' => $configuration,
        ), $this->getPackageHandle());
    }

    /**
     * Removes the storage type if no configurations exist.
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function delete()
    {
        $list = StorageLocation::getList();
        foreach ($list as $item) {
            if ($item->getTypeObject()->getHandle() == $this->getHandle()) {
                throw new \Exception(t('Please remove all storage locations using this storage type.'));
            }
        }

        $em = \ORM::entityManager();
        $em->remove($this);
        $em->flush();

        return true;
    }
}
