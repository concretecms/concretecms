<?php
namespace Concrete\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Represent Geolocator library.
 *
 * @ORM\Entity()
 * @ORM\Table(
 *     name="Geolocators",
 *     options={
 *         "comment": "List of all the installed Geolocator services"
 *     }
 * )
 */
class Geolocator
{
    /**
     * Create a new Geolocator instance.
     */
    public static function create($handle, $name, Package $package = null)
    {
        $result = new static();
        $result
            ->setGeolocatorHandle($handle)
            ->setGeolocatorName($name)
            ->setGeolocatorDescription('')
            ->setGeolocatorConfiguration([])
            ->setGeolocatorPackage($package)
            ->setIsActive(false)
        ;

        return $result;
    }

    /**
     * Initialize the instance.
     */
    protected function __construct()
    {
    }

    /**
     * The Geolocator ID.
     *
     * @ORM\Column(type="integer", options={"unsigned": true, "comment": "Geolocator ID"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int|null
     */
    protected $glID;

    /**
     * Get the Geolocator ID.
     *
     * @return int|null returns null if the record is not (yet) persisted
     */
    public function getGeolocatorID()
    {
        return $this->glID;
    }

    /**
     * The Geolocator handle.
     *
     * @ORM\Column(type="string", length=255, nullable=false, unique=true, options={"comment": "Geolocator handle"})
     *
     * @var string
     */
    protected $glHandle;

    /**
     * Get the Geolocator handle.
     *
     * @return string
     */
    public function getGeolocatorHandle()
    {
        return $this->glHandle;
    }

    /**
     * Set the Geolocator handle.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setGeolocatorHandle($value)
    {
        $this->glHandle = (string) $value;

        return $this;
    }

    /**
     * The Geolocator name.
     *
     * @ORM\Column(type="string", length=255, nullable=false, options={"comment": "Geolocator name"})
     *
     * @var string
     */
    protected $glName;

    /**
     * Get the Geolocator name.
     *
     * @return string
     */
    public function getGeolocatorName()
    {
        return $this->glName;
    }

    /**
     * Get the Geolocator display name.
     *
     * @param string $format = 'html' Escape the result in html format (if $format is 'html'). If $format is 'text' or any other value, the display name won't be escaped.
     *
     * @return string
     */
    public function getGeolocatorDisplayName($format = 'html')
    {
        $value = tc('GeolocatorName', $this->getGeolocatorName());
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    /**
     * Set the Geolocator name.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setGeolocatorName($value)
    {
        $this->glName = (string) $value;

        return $this;
    }

    /**
     * The Geolocator description.
     *
     * @ORM\Column(type="text", nullable=false, options={"comment": "Geolocator description"})
     *
     * @var string
     */
    protected $glDescription;

    /**
     * Get the Geolocator description.
     *
     * @return string
     */
    public function getGeolocatorDescription()
    {
        return $this->glDescription;
    }

    /**
     * Get the Geolocator display description.
     *
     * @param string $format = 'html' Escape the result in html format (if $format is 'html'). If $format is 'text' or any other value, the display description won't be escaped.
     *
     * @return string
     */
    public function getGeolocatorDisplayDescription($format = 'html')
    {
        $value = tc('GeolocatorDescription', $this->getGeolocatorDescription());
        switch ($format) {
            case 'html':
                return nl2br(h($value));
            case 'text':
            default:
                return $value;
        }
    }

    /**
     * Set the Geolocator description.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setGeolocatorDescription($value)
    {
        $this->glDescription = (string) $value;

        return $this;
    }

    /**
     * The Geolocator configuration options.
     *
     * @ORM\Column(type="json_array", nullable=false, options={"comment": "Geolocator configuration options"})
     *
     * @var array
     */
    protected $glConfiguration;

    /**
     * Get the Geolocator configuration options.
     *
     * @return array
     */
    public function getGeolocatorConfiguration()
    {
        return $this->glConfiguration;
    }

    /**
     * Set the Geolocator configuration options.
     *
     * @param array $value
     *
     * @return $this
     */
    public function setGeolocatorConfiguration(array $value)
    {
        $this->glConfiguration = $value;

        return $this;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\Package")
     * @ORM\JoinColumn(name="glPackage", referencedColumnName="pkgID", nullable=true, onDelete="CASCADE")
     *
     * @var Package|null
     */
    protected $glPackage;

    /**
     * Get the package associated to this Geolocator entity.
     *
     * @return Package|null
     */
    public function getGeolocatorPackage()
    {
        return $this->glPackage;
    }

    /**
     * Set the package associated to this Geolocator entity.
     *
     * @param Package|null $value
     *
     * @return $this
     */
    public function setGeolocatorPackage(Package $value = null)
    {
        $this->glPackage = $value;

        return $this;
    }

    /**
     * Is this Geolocator the active one?
     *
     * @ORM\Column(type="boolean", nullable=false, options={"comment": "Is this Geolocator the active one?"})
     *
     * @var bool
     */
    protected $glActive;

    /**
     * Is this Geolocator the active one?
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->glActive;
    }

    /**
     * Is this Geolocator the active one?
     *
     * @param bool $value
     *
     * @return $this
     */
    public function setIsActive($value)
    {
        $this->glActive = (bool) $value;

        return $this;
    }
}
