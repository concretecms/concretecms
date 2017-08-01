<?php
namespace Concrete\Core\Database\EntityManager\Provider;

use Concrete\Core\Database\EntityManager\Provider\ProviderInterface;
use Concrete\Core\Database\EntityManager\Driver\Driver;
use Doctrine\ORM\Mapping\Driver\XmlDriver;

/**
 * Use this provider if you wish to store your entity metadata in XML files.
 * 
 * The Provider adds automatically the default mapping information for the package
 * Expected location for the YAML mapping files: packageDir/config/xml
 * Expected namespace for the entites: Concrete\Packages\PackageXYZ\Entity
 * 
 * The default mapping information can be omitted by setting $useDefaultSettings to false.
 * In this case, the mapping information (Namespace, DoctrineMappingDriver) has to be added
 * manually with the method addDriver().
 *   
 */
class XmlProvider implements ProviderInterface
{

    protected $locations = [];

       /**
     * @var array
     */
    protected $drivers = [];
    
    /**
     * Constructor
     * 
     * @param ProviderInterface $pkg
     * @param boolean $useDefaultSettings  if it's set to false, no default 
     *                                      mapping information will be added to 
     *                                      the drivers array
     */
    public function __construct(ProviderInterface $pkg, $useDefaultSettings = true)
    {
        if($useDefaultSettings){
            $defaultNamespace = $pkg->getNamespace() . '\Entity';
            $mappingDataPath = $pkg->getPackagePath() . '/' 
                    . DIRNAME_CONFIG . '/' . DIRNAME_METADATA_XML;
            
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
        $this->drivers[] = new Driver($namespace, new XmlDriver($locations));
    }

}