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

    public function getPackageItems(Package $package)
    {
        return Type::getListByPackage($package);
    }
}
