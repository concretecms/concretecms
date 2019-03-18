<?php

namespace Concrete\Core\Package\ItemCategory;

use Concrete\Core\Entity\Geolocator;
use Concrete\Core\Entity\Package;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class IpAccessControlCategory extends AbstractCategory
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\ItemCategory\ItemInterface::getItemCategoryDisplayName()
     */
    public function getItemCategoryDisplayName()
    {
        return t('IP Access Control Categories');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\ItemCategory\ItemInterface::getItemName()
     *
     * @param \Concrete\Core\Entity\Permission\IpAccessControlCategory $ipAccessControlCategory
     */
    public function getItemName($ipAccessControlCategory)
    {
        return $ipAccessControlCategory->getDisplayName();
    }

    /**
     * @param \Concrete\Core\Entity\Package $package
     *
     * @return \Concrete\Core\Entity\Permission\IpAccessControlCategory[]
     */
    public function getPackageItems(Package $package)
    {
        $app = Application::getFacadeApplication();
        $em = $app->make(EntityManagerInterface::class);
        $repo = $em->getRepository(self::class);

        return $repo->findBy(['package' => $package]);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\ItemCategory\AbstractCategory::removeItem()
     *
     * @param \Concrete\Core\Entity\Permission\IpAccessControlCategory|mixed $ipAccessControlCategory
     */
    public function removeItem($ipAccessControlCategory)
    {
        if ($ipAccessControlCategory instanceof Geolocator && $ipAccessControlCategory->getIpAccessControlCategoryID() !== null) {
            $app = Application::getFacadeApplication();
            $em = $app->make(EntityManagerInterface::class);
            $em->remove($ipAccessControlCategory);
            $em->flush($ipAccessControlCategory);
        }
    }
}
