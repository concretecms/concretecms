<?php
namespace Concrete\Core\Database\EntityManager\Provider;

use Concrete\Core\Database\EntityManager\Provider\ProviderInterface;
use Concrete\Core\Database\EntityManager\Driver\Driver;
use Doctrine\ORM\Mapping\Driver\YamlDriver;

/**
 * Use this provider if you wish to store your entity metadata in YAML files.
 * 
 * The Provider adds automatically the default mapping information for the package
 * Expected location for the YAML mapping files: packageDir/config/yaml
 * Expected namespace for the entites: Concrete\Packages\PackageXYZ\Entity
 * 
 * The default mapping information can be omitted by setting $useDefaultSettings to false.
 * In this case, the mapping information (Namespace, DoctrineMappingDriver) has to be added
 * manually with the method $this->addDriver()
 *   
 */
class YamlProvider implements ProviderInterface
{
    
    /**
     * @var array
     */
    protected $drivers = array();
    
    /**
     * Constructor
     * 
     * @param Package $pkg
     * @param boolean $useDefaultSettings  if it's set to false, no default 
     *                                      mapping information will be added to 
     *                                      the drivers array
     */
    public function __construct(ProviderInterface $pkg, $useDefaultSettings = true)
    {
        if($useDefaultSettings){
            $defaultNamespace = $pkg->getNamespace() . '\Entity';
            $mappingDataPath = $pkg->getPackagePath() . '/' 
                    . DIRNAME_CONFIG . '/' . DIRNAME_METADATA_YAML;
            
            $this->addDriver($defaultNamespace, $mappingDataPath);
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function getDrivers()
    {
        return $this->drivers;
    }
    
    /**
     * Add additional driver
     * 
     * @param string $namespace
     * @param string|array $locations
     */
    public function addDriver($namespace, $locations)
    {
        $this->drivers[] = new Driver($namespace, new YamlDriver($locations));
    }
}
