<?php

namespace Concrete\Core\Package\ItemCategory;

use Concrete\Core\Entity\Package;
use Concrete\Core\File\ExternalFileProvider\Type\Type;

class ExternalFileProviderType extends AbstractCategory
{
    public function getItemCategoryDisplayName()
    {
        return t('External File Providers');
    }

    public function getItemName($location)
    {
        return $location->getName();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\ItemCategory\AbstractCategory::getPackageItems()
     *
     * @return \Concrete\Core\Entity\File\ExternalFileProvider\Type\Type[]
     */
    public function getPackageItems(Package $package)
    {
        return Type::getListByPackage($package);
    }
}
