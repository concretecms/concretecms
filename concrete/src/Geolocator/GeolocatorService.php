<?php
namespace Concrete\Core\Geolocator;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Geolocator;
use Concrete\Core\Filesystem\FileLocator;
use Doctrine\ORM\EntityManagerInterface;
use Punic\Comparer;

class GeolocatorService
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    protected $repo;

    /**
     * Initialize the instance.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(Application $app, EntityManagerInterface $em)
    {
        $this->app = $app;
        $this->em = $em;
        $this->repo = $this->em->getRepository(Geolocator::class);
    }

    /**
     * Get a geolocator library given its ID.
     *
     * @param int $id
     *
     * @return Geolocator|null
     */
    public function getByID($id)
    {
        $id = (int) $id;

        return $id > 0 ? $this->em->find(Geolocator::class, $id) : null;
    }

    /**
     * Get a geolocator library given its handle.
     *
     * @param string $handle
     *
     * @return Geolocator|null
     */
    public function getByHandle($handle)
    {
        $handle = (string) $handle;

        return $handle === '' ? null : $this->repo->findOneBy(['glHandle' => $handle]);
    }

    /**
     * Get the currently active geolocator library.
     *
     * @return Geolocator|null
     */
    public function getCurrent()
    {
        return $this->repo->findOneBy(['glActive' => true]);
    }

    /**
     * Set the currently active geolocator library.
     *
     * @return Geolocator|null
     */
    public function setCurrent(Geolocator $geolocator = null)
    {
        $currentGeolocator = $this->getCurrent();
        if ($currentGeolocator !== $geolocator) {
            if ($currentGeolocator !== null) {
                $currentGeolocator->setIsActive(false);
                if ($currentGeolocator->getGeolocatorID() !== null) {
                    $this->em->flush($currentGeolocator);
                }
            }
            if ($geolocator !== null) {
                $geolocator->setIsActive(true);
                if ($geolocator->getGeolocatorID() !== null) {
                    $this->em->flush($geolocator);
                }
            }
        }
    }

    /**
     * Get all the installed geolocator libraries.
     *
     * @return Geolocator[]
     */
    public function getList()
    {
        $result = $this->repo->findAll();
        $comparer = new Comparer();
        usort($result, function (Geolocator $a, Geolocator $b) use ($comparer) {
            return $comparer->compare($a->getGeolocatorDisplayName('text'), $b->getGeolocatorDisplayName('text'));
        });

        return $result;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    /**
     * @param Geolocator $geolocator
     *
     * @return GeolocatorControllerInterface
     */
    public function getController(Geolocator $geolocator)
    {
        $cacheKey = 'ccm/geolocator/controller/' . $geolocator->getGeolocatorHandle() . '/' . $geolocator->getGeolocatorID();
        $cache = $this->app->make('cache/request');
        $cacheItem = $cache->getItem($cacheKey);
        if (!$cacheItem->isMiss()) {
            $result = $cacheItem->get();
        } else {
            $cacheItem->lock();
            $segment = implode('/', [DIRNAME_GEOLOCATION, $geolocator->getGeolocatorHandle(), FILENAME_CONTROLLER]);
            $fileLocator = $this->app->make(FileLocator::class);
            $package = $geolocator->getGeolocatorPackage();
            if ($package !== null) {
                $fileLocator->addPackageLocation($package->getPackageHandle());
            }
            $record = $fileLocator->getRecord($segment);
            if ($record->isOverride()) {
                $prefix = true;
            } elseif ($geolocator->getGeolocatorPackage() !== null) {
                $prefix = $geolocator->getGeolocatorPackage()->getPackageHandle();
            } else {
                $prefix = '';
            }
            $class = core_class('Geolocator\\' . camelcase($geolocator->getGeolocatorHandle()) . '\\Controller', $prefix);
            $result = $this->app->make($class, ['geolocator' => $geolocator]);
            $cacheItem->set($result)->save();
        }

        return $result;
    }
}
