<?php

namespace Concrete\Core\Entity\File\ExternalFileProvider;

use Concrete\Core\Entity\File\ExternalFileProvider\Type\Type;
use Concrete\Core\File\ExternalFileProvider\Configuration\ConfigurationInterface;
use Concrete\Core\File\ExternalFileProvider\ExternalFileProviderInterface;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="FileExternalFileProviders")
 */
class ExternalFileProvider implements ExternalFileProviderInterface
{
    /**
     * @ORM\Column(type="string")
     */
    protected $efpName;

    /**
     * @ORM\Column(type="object")
     */
    protected $efpConfiguration;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue
     */
    protected $efpID;

    /**
     * @return int
     */
    public function getID()
    {
        return $this->efpID;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->efpName;
    }

    /** Returns the display name for this external file provider (localized and escaped accordingly to $format)
     *
     * @param string $format = 'html'
     *                       Escape the result in html format (if $format is 'html').
     *                       If $format is 'text' or any other value, the display name won't be escaped
     *
     * @return string
     */
    public function getDisplayName($format = 'html')
    {
        $value = tc('ExternalFileProviderName', $this->getName());

        switch ($format) {
            case 'html':
                return h($value);
            default:
                return $value;
        }
    }

    /**
     * @param string $efpName
     */
    public function setName(string $efpName)
    {
        $this->efpName = $efpName;
    }

    /**
     * @return ConfigurationInterface
     */
    public function getConfigurationObject()
    {
        return $this->efpConfiguration;
    }

    public function setConfigurationObject($configuration)
    {
        $this->efpConfiguration = $configuration;
    }

    /**
     * @return Type
     */
    public function getTypeObject()
    {
        return $this->getConfigurationObject()->getTypeObject();
    }

    public function delete()
    {
        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        $entityManager->remove($this);
        $entityManager->flush();
    }

    public function save()
    {
        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        $entityManager->persist($this);
        $entityManager->flush();
    }
}
