<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Board\Command\CreateBoardCommand;
use Concrete\Core\Board\Command\EnableCustomSlotTemplatesCommand;
use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\DataSource\Configuration\Configuration;
use Concrete\Core\Entity\Board\DataSource\ConfiguredDataSource;
use Concrete\Core\Entity\Board\DataSource\DataSource;
use Concrete\Core\Entity\Board\Template;
use Doctrine\ORM\EntityManager;
use Concrete\Core\ENtity\Board\SlotTemplate;
class ImportBoardsRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'boards';
    }

    public function import(\SimpleXMLElement $sx)
    {
        $app = app();
        $em = $app->make(EntityManager::class);
        if (isset($sx->boards)) {
            foreach ($sx->boards->board as $b) {
                $board = $em->getRepository(Board::class)->findOneByBoardName((string) $b['name']);
                if (!$board) {
                    $template = $em->getRepository(Template::class)->findOneByHandle((string) $b['template']);
                    $command = new CreateBoardCommand();
                    $command->setName((string) $b['name']);
                    $command->setSortBy((string) $b['sort']);
                    $command->setTemplate($template);
                    $board = $app->executeCommand($command);

                    if (!empty($b->datasources)) {
                        foreach ($b->datasources->datasource as $datasource) {
                            $sourceEntity = $em->getRepository(DataSource::class)->findOneByHandle((string) $datasource['source']);
                            if ($sourceEntity) {
                                $driver = $sourceEntity->getDriver();
                                $saver = $driver->getSaver();
                                /**
                                 * @var $configuration Configuration
                                 */
                                $configuration = $saver->createConfigurationFromImport($datasource->configuration);

                                $weight = (int) $datasource['weight'];
                                if ($weight > 0) {
                                    $board->setHasCustomWeightingRules(true);
                                    $em->persist($board);
                                }

                                $configuredDataSource = new ConfiguredDataSource();
                                $configuredDataSource->setName((string) $datasource['name']);
                                $configuredDataSource->setCustomWeight($weight);
                                $configuredDataSource->setBoard($board);
                                $configuredDataSource->setDataSource($sourceEntity);
                                $configuredDataSource->setPopulationDayIntervalFuture((int) $datasource['day-interval-future']);
                                $configuredDataSource->setPopulationDayIntervalPast((int) $datasource['day-interval-past']);
                                $em->persist($configuredDataSource);
                                $em->flush();

                                $configuration->setDataSource($configuredDataSource);
                                $em->persist($configuration);
                                $em->flush();
                            }
                        }
                    }

                    if (((string) $b['uses-custom-template-subset']) == '1') {

                        $templateIds = [];
                        foreach ($b->templates->template as $templateNode) {
                            $templateNodeEntity = $em->getRepository(SlotTemplate::class)->findOneByHandle((string) $templateNode['handle']);
                            if ($templateNodeEntity) {
                                $templateIds[] = $templateNodeEntity->getID();
                            }
                        }
                        $command = new EnableCustomSlotTemplatesCommand();
                        $command->setBoard($board);
                        $command->setTemplateIDs($templateIds);
                        $app->executeCommand($command);
                    }
                }
            }
        }
    }
}
