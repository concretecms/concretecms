<?php

namespace Concrete\Core\Entity\Board\DataSource\Configuration;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="BoardConfiguredDataSourceConfiguration")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 */
abstract class Configuration
{

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $configurationID;

    /**
     * @ORM\OneToOne(targetEntity="Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource",
     *     inversedBy="configuration", cascade={"persist"})
     * @ORM\JoinColumn(name="configuredDataSourceID", referencedColumnName="configuredDataSourceID")
     **/
    protected $data_source;

    /**
     * @return mixed
     */
    public function getConfigurationID()
    {
        return $this->configurationID;
    }

    /**
     * @return mixed
     */
    public function getDataSource()
    {
        return $this->data_source;
    }

    /**
     * @param mixed $data_source
     */
    public function setDataSource($data_source): void
    {
        $this->data_source = $data_source;
    }

    abstract public function export(\SimpleXMLElement $element);
    
    



}
