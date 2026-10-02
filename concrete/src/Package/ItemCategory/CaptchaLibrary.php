<?php

namespace Concrete\Core\Package\ItemCategory;

use Concrete\Core\Captcha\Library;
use Concrete\Core\Entity\Package;

defined('C5_EXECUTE') or die('Access Denied.');

class CaptchaLibrary extends AbstractCategory
{
    public function getItemCategoryDisplayName()
    {
        return t('Captcha Libraries');
    }

    public function getItemName($library)
    {
        return $library->getSystemCaptchaLibraryName();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\ItemCategory\AbstractCategory::getPackageItems()
     *
     * @return \Concrete\Core\Captcha\Library[]
     */
    public function getPackageItems(Package $package)
    {
        return Library::getListByPackage($package);
    }
}
