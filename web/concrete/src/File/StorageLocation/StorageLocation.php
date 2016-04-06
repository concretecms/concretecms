<?php
namespace Concrete\Core\File\StorageLocation;

use Concrete\Core\File\StorageLocation\Configuration\ConfigurationInterface;
use Database;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="FileStorageLocations")
 */
class StorageLocation
{
    /**
     * @ORM\Column(type="string")
     */
    protected $fslName;

    /**
     * @ORM\Column(type="object")
     */
    protected $fslConfiguration;

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue
     */
    protected $fslID;

    /**
     * @ORM\OneToMany(targetEntity="\Concrete\Core\File\File", mappedBy="storageLocation")
     **/
    protected $files;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $fslIsDefault = false;

    public function getID()
    {
        return $this->fslID;
    }

    public function getName()
    {
        return $this->fslName;
    }

    /** Returns the display name for this storage location (localized and escaped accordingly to $format)
     * @param string $format = 'html'
     *    Escape the result in html format (if $format is 'html').
     *    If $format is 'text' or any other value, the display name won't be escaped.
     *
     * @return string
     */
    public function getDisplayName($format = 'html')
    {
        $value = tc('StorageLocationName', $this->getName());
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    public function setName($fslName)
    {
        $this->fslName = $fslName;
    }

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

        $em = \ORM::entityManager();
        $o = new static();
        $o->fslName = $fslName;
        $o->fslIsDefault = $fslIsDefault;
        $o->fslConfiguration = $configuration;
        $em->persist($o);

        if ($fslIsDefault && is_object($default)) {
            $default->fslIsDefault = false;
            $em->persist($default);
        }

        $em->flush();

        return $o;
    }

    public static function getByID($id)
    {
        $em = \ORM::entityManager();
        $r = $em->find('\Concrete\Core\File\StorageLocation\StorageLocation', intval($id));

        return $r;
    }
    /**
     * @return StorageLocation[]
     */
    public static function getList()
    {
        $em = \ORM::entityManager();
        return $em->getRepository('\Concrete\Core\File\StorageLocation\StorageLocation')->findBy(
            array(), array('fslID' => 'asc')
        );
    }

    /**
     * @return StorageLocation
     */
    public static function getDefault()
    {
        $em = \ORM::entityManager();
        $location = $em->getRepository('\Concrete\Core\File\StorageLocation\StorageLocation')->findOneBy(
            array('fslIsDefault' => true,
            ));

        return $location;
    }

    /**
     * Returns the proper file system object for the current storage location, by mapping
     * it through Flysystem.
     *
     * @return \League\Flysystem\Filesystem
     */
    public function getFileSystemObject()
    {
        $adapter = $this->fslConfiguration->getAdapter();
        $filesystem = new \League\Flysystem\Filesystem($adapter);

        return $filesystem;
    }

    public function delete()
    {
        $default = self::getDefault();
        $db = Database::get();

        $fIDs = $db->GetCol('select fID from Files where fslID = ?', array($this->getID()));
        foreach ($fIDs as $fID) {
            $file = \File::getByID($fID);
            if (is_object($file)) {
                $file->setFileStorageLocation($default);
            }
        }

        $em = \ORM::entityManager();
        $em->remove($this);
        $em->flush();
    }

    public function save()
    {
        $default = self::getDefault();

        $em = \ORM::entityManager();
        $em->persist($this);

        if ($this->isDefault() && is_object($default) && $default->getID() != $this->getID()) {
            $default->setIsDefault(false);
            $em->persist($default);
        }

        $em->flush();
    }
}
