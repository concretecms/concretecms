<?php
namespace Concrete\Core\Database\EntityManager\Provider;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\EntityManager\Driver\Driver;
use Concrete\Core\Database\EntityManager\Provider\ProviderInterface;
use Concrete\Core\Package\Package;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Doctrine\ORM\Mapping\Driver\YamlDriver;

/**
 * Use this provider if you wish to store your entity metadata in YAML files.
 */
class YamlProvider implements ProviderInterface
{

    protected $locations = [];

    /**
     * YamlProvider constructor. $location may be a single path or an array of paths to YAML metadata.
     * @param $location
     */
    public function __construct($location)
    {
        $this->locations = $location;
    }

    public function getDrivers()
    {
        $drivers = array(
            new YamlDriver($this->locations)
        );
        return $drivers;
    }

}