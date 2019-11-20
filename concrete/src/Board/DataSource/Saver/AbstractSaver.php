<?php
namespace Concrete\Core\Board\DataSource\Saver;

use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\DataSource\Configuration\Configuration;
use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Concrete\Core\Entity\Board\DataSource\DataSource;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

defined('C5_EXECUTE') or die("Access Denied.");

abstract class AbstractSaver implements SaverInterface
{
    /**
     * @var EntityManager 
     */
    protected $entityManager;
    
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Request $request
     * @return Configuration
     */
    abstract public function createConfiguration(Request $request);

    public function addConfiguredDataSourceFromRequest(
        Board $board,
        DataSource $dataSource,
        Request $request) : ConfiguredDataSource
    {
        $configuredDataSource = new ConfiguredDataSource();

        $configuration = $this->createConfiguration($request);
        $configuration->setDataSource($configuredDataSource);

        $configuredDataSource->setBoard($board);
        $configuredDataSource->setDataSource($dataSource);
        $this->entityManager->persist($configuredDataSource);
        $this->entityManager->persist($configuration);
        $this->entityManager->flush();
        
        return $configuredDataSource;
    }

    public function updateConfiguredDataSourceFromRequest(
        ConfiguredDataSource $configuredDataSource,
        Request $request) : ConfiguredDataSource
    {
        $board = $configuredDataSource->getBoard();
        $dataSource = $configuredDataSource->getDataSource();
        $this->entityManager->remove($configuredDataSource);
        $this->entityManager->flush();
        
        return $this->addConfiguredDataSourceFromRequest($board, $dataSource, $request);
        
    }


}
