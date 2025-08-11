<?php

namespace Concrete\Core\Package;

use Concrete\Core\Cache\CacheLocal;
use Concrete\Core\Entity\Package as PackageEntity;
use Concrete\Core\Foundation\ConcreteObject;
use Doctrine\ORM\EntityManagerInterface;

class PackageList extends ConcreteObject
{
    /**
     * @var \Concrete\Core\Entity\Package[]
     */
    protected $packages = [];

    /**
     * @param \Concrete\Core\Entity\Package $pkg
     */
    public function add($pkg)
    {
        $this->packages[] = $pkg;
    }

    /**
     * @return \Concrete\Core\Entity\Package[]
     */
    public function getPackages()
    {
        return $this->packages;
    }

    /**
     * @param \SimpleXMLElement $xml
     */
    public static function export($xml)
    {
        $packages = static::get()->getPackages();
        $pkgs = $xml->addChild("packages");
        foreach ($packages as $pkg) {
            $node = $pkgs->addChild('package');
            $node->addAttribute('handle', $pkg->getPackageHandle());
        }
    }

    /**
     * @param int|null $pkgID
     *
     * @return string|false
     */
    public static function getHandle($pkgID)
    {
        if ($pkgID < 1) {
            return false;
        }
        $packageList = CacheLocal::getEntry('packageHandleList', false);
        if (!$packageList) {
            $packageList = [];
            foreach (static::get(true)->getPackages() as $packageEntity) {
                $packageList[$packageEntity->getPackageID()] = $packageEntity->getPackageHandle();
            }
            CacheLocal::set('packageHandleList', false, $packageList);

        }
        return $packageList[$pkgID] ?? false;
    }

    public static function refreshCache()
    {
        CacheLocal::delete('packageHandleList', false);
        CacheLocal::delete('pkgList', 1);
        CacheLocal::delete('pkgList', 0);
    }

    /**
     * @deprecated
     * @param bool|int $pkgIsInstalled
     * @return static
     */
    public static function get($pkgIsInstalled = 1)
    {
        $pkgIsInstalled = $pkgIsInstalled ? 1 : 0;
        $pkgList = CacheLocal::getEntry('pkgList', $pkgIsInstalled);
        if ($pkgList) {
            return $pkgList;
        }

        $em = app(EntityManagerInterface::class);
        $r = $em->getRepository(PackageEntity::class);
        $packages = $r->findBy(['pkgIsInstalled' => $pkgIsInstalled], ['pkgID' => 'asc']);
        $list = new static();
        foreach($packages as $pkg) {
            $list->add($pkg);
        }

        CacheLocal::set('pkgList', $pkgIsInstalled, $list);

        return $list;
    }
}
