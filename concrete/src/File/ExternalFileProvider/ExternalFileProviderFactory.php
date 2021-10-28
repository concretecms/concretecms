<?php

namespace Concrete\Core\File\ExternalFileProvider;

use Concrete\Core\Entity\File\ExternalFileProvider\ExternalFileProvider as ExternalFileProviderEntity;
use Concrete\Core\File\ExternalFileProvider\Configuration\ConfigurationInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class ExternalFileProviderFactory
 * Get ahold of existing external file providers and create new ones.
 */
class ExternalFileProviderFactory
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Create a new ExternalFileProvider, pass this to ExternalFileProviderFactory->persist() to store this external file provider.
     *
     * @param ConfigurationInterface $configuration
     * @param $name
     *
     * @return ExternalFileProviderEntity
     */
    public function create(ConfigurationInterface $configuration, $name)
    {
        $externalFileProvider = new ExternalFileProviderEntity();
        $externalFileProvider->setConfigurationObject($configuration);
        $externalFileProvider->setName($name);
        return $externalFileProvider;
    }

    /**
     * Fetch a external file provider by its ID.
     *
     * @param int $id
     *
     * @return ExternalFileProviderEntity|object|null
     *
     */
    public function fetchByID(int $id)
    {
        return $this->entityManager->find(ExternalFileProviderEntity::class, (int)$id);
    }

    /**
     * Fetch a external file provider by its name.
     *
     * @param string $name
     *
     * @return ExternalFileProviderEntity|null|object
     */
    public function fetchByName(string $name)
    {
        return $this->entityManager->getRepository(ExternalFileProviderEntity::class)->findOneBy(['efpName' => $name]);
    }

    /**
     * Fetch a list of external file providers.
     *
     * @return ExternalFileProviderEntity[]|object[]
     */
    public function fetchList()
    {
        return $this->entityManager->getRepository(ExternalFileProviderEntity::class)->findBy([], ['efpID' => 'asc']);
    }

    /**
     * Store a created external file provider to the database.
     *
     * @param ExternalFileProviderEntity $externalFileProvider
     * @return ExternalFileProviderEntity
     */
    public function persist(ExternalFileProviderEntity $externalFileProvider)
    {
        $this->entityManager->persist($externalFileProvider);
        $this->entityManager->flush();
        return $externalFileProvider;
    }
}
