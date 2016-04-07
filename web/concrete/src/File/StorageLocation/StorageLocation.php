<?php
namespace Concrete\Core\File\StorageLocation;
use Concrete\Core\File\StorageLocation\Configuration\Configuration;
use Concrete\Core\File\StorageLocation\Configuration\ConfigurationInterface;
use Database;
use Core;
/**
 * @Entity
 * @Table(name="FileStorageLocations")
 */
class StorageLocation
{

    /**
     * @Column(type="text")
     */
    protected $fslName;

    /**
     * @Column(type="object")
     */
    protected $fslConfiguration;

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected $fslID;

    /**
     * @Column(type="boolean")
     */
    protected $fslIsDefault = false;

    /**
     * @return int
     */
    public function getID()
    {
        return $this->fslID;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->fslName;
    }

    /** Returns the display name for this storage location (localized and escaped accordingly to $format)
    * @param string $format = 'html'
    *    Escape the result in html format (if $format is 'html').
    *    If $format is 'text' or any other value, the display name won't be escaped.
    * @return string
    */
    public function getDisplayName($format = 'html')
    {
        $value = tc('StorageLocationName', $this->getName());
        switch($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    /**
     * @param string $fslName
     */
    public function setName($fslName)
    {
        $this->fslName = $fslName;
    }

    /**
     * @param bool $fslIsDefault
     */
    public function setIsDefault($fslIsDefault)
    {
        $this->fslIsDefault = $fslIsDefault;
    }

    /**
     * @return \Concrete\Core\File\StorageLocation\Configuration\ConfigurationInterface
     */
    public function getConfigurationObject()
    {
        return $this->fslConfiguration;
    }

    /**
     * @return bool
     */
    public function isDefault()
    {
        return $this->fslIsDefault;
    }

    public function setConfigurationObject($configuration)
    {
        $this->fslConfiguration = $configuration;
    }

    public function getTypeObject()
    {
        $configuration = $this->getConfigurationObject();
        $type = $configuration->getTypeObject();
        return $type;
    }

    public static function add(ConfigurationInterface $configuration, $fslName, $fslIsDefault = false)
    {
        $default = self::getDefault();

        $em = \ORM::entityManager('core');
        $o = new static();
        $o->setName($fslName);
        $o->setIsDefault($fslIsDefault);
        $o->setConfigurationObject($configuration);
        $em->persist($o);

        if ($fslIsDefault && is_object($default)) {
            $default->setIsDefault(false);
            $em->persist($default);
        }

        $em->flush();

        return $o;
    }

    /**
     * @param int $id
     * @return null|StorageLocation
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public static function getByID($id)
    {
        $em = \ORM::entityManager('core');
        $r = $em->find('\Concrete\Core\File\StorageLocation\StorageLocation', intval($id));
        return $r;
    }

    /**
     * @return StorageLocation[]
     */
    public static function getList()
    {
        $em = \ORM::entityManager('core');
        return $em->getRepository('\Concrete\Core\File\StorageLocation\StorageLocation')->findBy(
            array(), array('fslID' => 'asc')
        );
    }

    /**
     * @return StorageLocation
     */
    public static function getDefault()
    {
        $em = \ORM::entityManager('core');
        $location = $em->getRepository('\Concrete\Core\File\StorageLocation\StorageLocation')->findOneBy(
            array('fslIsDefault' => true
            ));
        return $location;
    }

    /**
     * Returns the proper file system object for the current storage location, by mapping
     * it through Flysystem
     * @return \Concrete\Flysystem\Filesystem
     */
    public function getFileSystemObject()
    {
        $adapter = $this->getConfigurationObject()->getAdapter();
        $filesystem = new \Concrete\Flysystem\Filesystem($adapter);
        return $filesystem;
    }

    public function delete()
    {
        $default = self::getDefault();
        $db = Database::get();

        $fIDs = $db->GetCol('select fID from Files where fslID = ?', array($this->getID()));
        foreach($fIDs as $fID) {
            $file = \File::getByID($fID);
            if (is_object($file)) {
                $file->setFileStorageLocation($default);
            }
        }

        $em = \ORM::entityManager('core');
        $em->remove($this);
        $em->flush();
    }

    public function save()
    {
        $default = self::getDefault();

        $em = \ORM::entityManager('core');
        $em->persist($this);

        if ($this->isDefault() && is_object($default) && $default->getID() != $this->getID()) {
            $default->setIsDefault(false);
            $em->persist($default);
        }

        $em->flush();
    }

}
