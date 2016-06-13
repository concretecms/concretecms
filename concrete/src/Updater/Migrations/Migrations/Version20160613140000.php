<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

/**
 * Version20160613140000
 */
class Version20160613140000 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        $this->createMetaDataConfigurationForPackages();
    }

    /**
     * Loop through all installed packages and write the metadata setting for packages
     * tho the database.php config in genereated_overrides
     */
    protected function createMetaDataConfigurationForPackages(){

        $r = $this->connection->executeQuery('SELECT * FROM packages WHERE pkgIsInstalled = 1;');
        $packageService = new \Concrete\Core\Package\PackageService();

        while ($row = $r->fetch()) {
            $pkgClass = \Concrete\Core\Package\PackageService::getClass($row['pkgHandle']);
            $packageService->savePackageMetadataDriverToConfig($pkgClass);
        }
    }


    public function down(Schema $schema)
    {

    }
}