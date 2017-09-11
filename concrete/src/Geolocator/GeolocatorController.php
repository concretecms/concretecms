<?php
namespace Concrete\Core\Geolocator;

use Concrete\Core\Entity\Geolocator;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Filesystem\FileLocator;
use Controller;
use Exception;
use IPLib\Address\AddressInterface;
use IPLib\Range\Type as IPRangeType;
use Symfony\Component\HttpFoundation\ParameterBag;
use Throwable;

abstract class GeolocatorController extends Controller implements GeolocatorControllerInterface
{
    /**
     * @var Geolocator
     */
    protected $geolocator;

    /**
     * @param Geolocator $geolocator
     */
    public function __construct(Geolocator $geolocator)
    {
        parent::__construct();
        $this->geolocator = $geolocator;
    }

    /**
     * Get the path to a geolocator file.
     *
     * @param Geolocator $geolocator
     * @param string $file
     *
     * @return \Concrete\Core\Filesystem\FileLocator\Record
     */
    protected function getFileRecord($file)
    {
        $result = null;
        $segment = implode('/', [DIRNAME_GEOLOCATION, $this->geolocator->getGeolocatorHandle(), $file]);
        $fileLocator = $this->app->make(FileLocator::class);
        /* @var FileLocator $fileLocator */
        $package = $this->geolocator->getGeolocatorPackage();
        if ($package !== null) {
            $fileLocator->addPackageLocation($package->getPackageHandle());
        }

        return $fileLocator->getRecord($segment);
    }

    /**
     * @var \Concrete\Core\Filesystem\FileLocator\Record|null
     */
    private $configurationRecord = null;

    /**
     * @return \Concrete\Core\Filesystem\FileLocator\Record
     */
    protected function getConfigurationRecord()
    {
        if ($this->configurationRecord === null) {
            $this->configurationRecord = $this->getFileRecord('configure.php');
        }

        return $this->configurationRecord;
    }

    /**
     * {@inheritdoc}
     *
     * @see GeolocatorControllerInterface::hasConfigurationForm()
     */
    public function hasConfigurationForm()
    {
        return $this->getConfigurationRecord()->exists();
    }

    /**
     * {@inheritdoc}
     *
     * @see GeolocatorControllerInterface::renderConfigurationForm()
     */
    public function renderConfigurationForm()
    {
        $record = $this->getConfigurationRecord();
        if ($record->exists()) {
            call_user_func(
                function ($_vars) {
                    extract($_vars);
                    unset($_vars);
                    require_once $_file;
                },
                [
                    '_file' => $record->getFile(),
                    'app' => $this->app,
                    'geolocator' => $this->geolocator,
                    'controller' => $this,
                    'form' => $this->app->make('helper/form'),
                ]
            );
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Geolocator\GeolocatorControllerInterface::saveConfigurationForm()
     */
    public function saveConfigurationForm(array $configuration, ParameterBag $data, ErrorList $error)
    {
        if ($this->hasConfigurationForm()) {
            throw new UserMessageException(t('The geolocator controller must implement the %s method', __FUNCTION__));
        }

        return $configuration;
    }

    /**
     * {@inheritdoc}
     *
     * @see GeolocatorControllerInterface::geolocateIPAddress()
     */
    public function geolocateIPAddress(AddressInterface $address)
    {
        $result = new GeolocationResult();
        try {
            switch ($address->getRangeType()) {
                case IPRangeType::T_PUBLIC:
                    $addressString = (string) $address;
                    $session = $this->app->make('session');
                    $sessionKey = 'ccm/geolocation/' . $this->geolocator->getGeolocatorHandle() . '/' . $this->geolocator->getGeolocatorID() . '/' . md5(serialize($this->geolocator->getGeolocatorConfiguration())) . '/' . $addressString;
                    if ($session->has($sessionKey)) {
                        $result = $session->get($sessionKey);
                    } else {
                        $result = $this->performGeolocation($address);
                        $session->set($sessionKey, $result);
                    }
                    break;
                default:
                    $result->setError(GeolocationResult::ERR_IPNOTPUBLIC);
                    break;
            }
        } catch (Exception $x) {
            $result->setError(GeolocationResult::ERR_OTHER, '', $x);
        } catch (Throwable $x) {
            $result->setError(GeolocationResult::ERR_OTHER, '', new Exception($x->getMessage(), $x->getCode()));
        }

        return $result;
    }

    /**
     * Geolocate an IP address.
     *
     * @param AddressInterface $address
     *
     * @return GeolocationResult
     */
    abstract protected function performGeolocation(AddressInterface $address);
}
