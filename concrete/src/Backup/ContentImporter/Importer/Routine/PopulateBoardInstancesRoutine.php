<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Board\Command\CreateBoardInstanceCommand;
use Concrete\Core\Board\Command\PopulateBoardInstanceDataPoolCommand;
use Concrete\Core\Board\Command\RegenerateBoardInstanceCommand;
use Concrete\Core\Entity\Board\Board;
use Doctrine\ORM\EntityManager;

class PopulateBoardInstancesRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'populate_board_instances';
    }

    public function import(\SimpleXMLElement $sx)
    {
        $app = app();
        $em = $app->make(EntityManager::class);
        if (isset($sx->boards)) {
            foreach ($sx->boards->board as $b) {
                $board = $em->getRepository(Board::class)->findOneByBoardName((string) $b['name']);
                $em->refresh($board);
                if ($board) {

                    foreach ($board->getDataSources() as $configuredDataSource) {
                        $em->refresh($configuredDataSource);
                    }

                    $instances = $board->getInstances();
                    foreach ($instances as $instance) {
                        $command = new RegenerateBoardInstanceCommand();
                        $command->setInstance($instance);
                        $app->executeCommand($command);
                    }
                }
            }
        }
    }
}
