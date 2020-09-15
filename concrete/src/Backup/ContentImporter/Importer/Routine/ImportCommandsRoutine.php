<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Entity\Command\Command;
use Doctrine\ORM\EntityManager;

class ImportCommandsRoutine extends AbstractRoutine
{
    public function getHandle()
    {
        return 'commands';
    }

    public function import(\SimpleXMLElement $sx)
    {
        if (isset($sx->commands)) {
            $entityManager = app(EntityManager::class);
            foreach ($sx->commands->command as $xml) {
                $pkg = static::getPackageObject($xml['package']);
                $command = $entityManager->getRepository(Command::class)
                    ->findOneByHandle((string) $xml['handle']);
                if (!$command) {
                    $command = new Command();
                    $command->setHandle((string) $xml['handle']);
                    if ($pkg) {
                        $command->setPackage($pkg);
                    }
                    $entityManager->persist($command);
                }
            }
            $entityManager->flush();
        }

    }

}
