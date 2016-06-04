<?php
namespace Concrete\Package\TestMetadatadriverXml;

defined('C5_EXECUTE') or die(_("Access Denied."));
/**
 * Controller test addon - testing metadatadriver with legacy annotation driver
 *
 * @author markus.liechti
 */
class Controller extends \Concrete\Core\Package\Package{

    protected $pkgHandle = 'test_metadatadriver_xml';
    protected $appVersionRequired = '5.8.0';
    protected $pkgVersion = '0.0.1';
    protected $metadataDriver = self::PACKAGE_METADATADRIVER_XML;

    public function getPackageDescription() {
        return t("Test addon registers entities via the xml driver");
    }

    public function getPackageName(){
        return t("Test addon - uses xml driver");
    }
}