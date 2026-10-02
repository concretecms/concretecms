<?php

namespace Concrete\Core\Package\ItemCategory;

use Concrete\Core\Entity\Package;

defined('C5_EXECUTE') or die('Access Denied.');

class MailImporter extends AbstractCategory
{
    public function getItemCategoryDisplayName()
    {
        return t('Mail Importers');
    }

    public function getItemName($importer)
    {
        return $importer->getMailImporterName();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\ItemCategory\AbstractCategory::getPackageItems()
     *
     * @return \Concrete\Core\Mail\Importer\MailImporter[]
     */
    public function getPackageItems(Package $package)
    {
        return \Concrete\Core\Mail\Importer\MailImporter::getListByPackage($package);
    }
}
