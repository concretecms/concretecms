<?php

namespace Concrete\Core\Entity\File\ExternalFileProvider\Type;

use Concrete\Core\File\ExternalFileProvider\Configuration\ConfigurationInterface;
use Concrete\Core\Entity\File\ExternalFileProvider\ExternalFileProvider as ExternalFileProviderEntity;
use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Package\PackageList;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\View\View;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * @ORM\Entity
 * @ORM\Table(name="FileExternalFileProviderTypes")
 */
class Type
{
    /**
     * @ORM\Column(type="text")
     */
    public $efpTypeHandle;

    /**
     * @ORM\Column(type="text")
     */
    public $efpTypeName;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    public $efpTypeID;

    /**
     * @ORM\Column(type="integer")
     */
    public $pkgID = 0;

    /**
     * @return string
     */
    public function getHandle()
    {
        return $this->efpTypeHandle;
    }

    /**
     * @return int
     */
    public function getID()
    {
        return $this->efpTypeID;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->efpTypeName;
    }

    /**
     * @return int
     */
    public function getPackageID()
    {
        return $this->pkgID;
    }

    /**
     * @return null|string
     */
    public function getPackageHandle()
    {
        return PackageList::getHandle($this->pkgID);
    }

    /**
     * @return ConfigurationInterface
     */
    public function getConfigurationObject()
    {
        $class = core_class('\\Core\\File\\ExternalFileProvider\\Configuration\\' . camelcase($this->getHandle()) . 'Configuration', $this->getPackageHandle());
        $app = Application::getFacadeApplication();
        return $app->make($class);
    }

    /**
     * @return bool
     */
    public function hasOptionsForm()
    {
        $app = Application::getFacadeApplication();
        /** @var FileLocator $fileLocator */
        $fileLocator = $app->make(FileLocator::class);

        if ($this->getPackageHandle()) {
            $fileLocator->addLocation(new FileLocator\PackageLocation($this->getPackageHandle()));
        }

        $rec = $fileLocator->getRecord(DIRNAME_ELEMENTS . '/' . DIRNAME_EXTERNAL_FILE_PROVIDER_TYPES . '/' . $this->getHandle() . '.php');

        return $rec->exists();
    }

    /**
     * @param bool|ExternalFileProviderEntity $fileProvider
     * @throws Exception
     */
    public function includeOptionsForm($fileProvider = false)
    {
        $configuration = $this->getConfigurationObject();

        if ($fileProvider instanceof ExternalFileProviderEntity) {
            $configuration = $fileProvider->getConfigurationObject();
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        View::element(
            DIRNAME_EXTERNAL_FILE_PROVIDER_TYPES . '/' . $this->getHandle(),
            [
                'type' => $this,
                'fileProvider' => $fileProvider,
                'configuration' => $configuration,
            ],
            $this->getPackageHandle()
        );
    }

    /**
     * Removes the external file provider type if no configurations exist.
     *
     * @return bool
     * @throws Exception
     *
     */
    public function delete()
    {
        $app = Application::getFacadeApplication();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $app->make(EntityManagerInterface::class);
        $entityManager->remove($this);
        $entityManager->flush();

        return true;
    }
}
