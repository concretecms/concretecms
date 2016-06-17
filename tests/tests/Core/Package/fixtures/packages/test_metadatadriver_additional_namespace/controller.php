<?php
namespace Concrete\Package\TestMetadatadriverAdditionalNamespace;

defined('C5_EXECUTE') or die(_("Access Denied."));
/**
 * Controller test addon - testing metadatadriver with legacy annotation driver
 *
 * @author Markus Liechti <markus@liechti.io>
 */
class Controller extends \Concrete\Core\Package\Package{

    protected $pkgHandle = 'test_metadatadriver_additional_namespace';
    protected $appVersionRequired = '5.8.0';
    protected $pkgVersion = '0.0.1';

    // The value we want to test
    protected $pkgAutoloaderRegistries = array(
        'src/PortlandLabs/Concrete5/MigrationTool' => '\PortlandLabs\Concrete5\MigrationTool',
        'src/Dummy' => '\Dummy'
    );

    public function getPackageDescription() {
        return t("Test addon registers entities via the default annotation driver and adds additional namespaces");
    }

    public function getPackageName(){
        return t("Test addon - uses default annotation driver and adds additional namespaces");
    }
}