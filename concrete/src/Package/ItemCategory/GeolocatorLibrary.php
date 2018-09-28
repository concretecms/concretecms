<?php
namespace Concrete\Core\Package\ItemCategory;

use Concrete\Core\Entity\Geolocator;
use Concrete\Core\Entity\Package;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class GeolocatorLibrary extends AbstractCategory
{
    public function getItemCategoryDisplayName()
    {
        return t('Geolocation Libraries');
    }

    public function getItemName($geolocator)
    {
        return $geolocator->getGeolocatorDisplayName();
    }

    public function getPackageItems(Package $package)
    {
        $app = Application::getFacadeApplication();
        $em = $app->make(EntityManagerInterface::class);
        $repo = $em->getRepository(Geolocator::class);

        return $repo->findBy(['glPackage' => $package]);
    }

    public function removeItem($item)
    {
        if ($item instanceof Geolocator && $item->getGeolocatorID() !== null) {
            $app = Application::getFacadeApplication();
            $em = $app->make(EntityManagerInterface::class);
            $em->remove($item);
            $em->flush($item);
        }
    }
}
