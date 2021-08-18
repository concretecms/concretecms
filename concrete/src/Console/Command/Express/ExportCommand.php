<?php

namespace Concrete\Core\Console\Command\Express;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Console\Command;
use Concrete\Core\Csv\WriterFactory;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Express\EntryList;
use Concrete\Core\Express\Export\EntryList\CsvWriter;
use Doctrine\ORM\EntityManagerInterface;
use SplFileObject;

class ExportCommand extends Command
{

    /**
     * Our command description
     *
     * @var string
     */
    protected $description = 'Export express entries';

    /**
     * The signature for this command
     *
     * @var string
     */
    protected $signature = 'c5:express:export {entity : Which entity to export entries from}';

    /**
     * Handle processing calls to this command
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \Concrete\Core\Application\Application $app
     * @param \Concrete\Core\Config\Repository\Repository $config
     * @param \Concrete\Core\Csv\WriterFactory $factory
     *
     * @return int
     */
    public function handle(
        EntityManagerInterface $entityManager,
        Application $app,
        Repository $config,
        WriterFactory $factory
    ) {
        $entityHandle = $this->input->getArgument('entity');

        // Locate the entity
        $repository = $entityManager->getRepository(Entity::class);
        /** @var Entity $entity */
        $entity = $repository->findOneBy([
            'handle' => $entityHandle
        ]);

        // Make sure we found a proper entity
        if (!$entity) {
            $this->output->error('Invalid entity handle.');
            return static::INVALID;
        }

        return $this->outputFormatCsv($entity, $app, $config, $factory);
    }

    /**
     * Output the entries as CSV
     *
     * @param \Concrete\Core\Entity\Express\Entity $entity
     * @param \Concrete\Core\Application\Application $app
     * @param \Concrete\Core\Config\Repository\Repository $config
     * @param \Concrete\Core\Csv\WriterFactory $factory
     *
     * @return int
     */
    private function outputFormatCsv(Entity $entity, Application $app, Repository $config, WriterFactory $factory)
    {
        $bom = $config->get('concrete.export.csv.include_bom') ? $config->get('concrete.charset_bom') : '';

        // Build writer
        $file = new SplFileObject('php://output', 'w');
        $csv = $factory->createFromFileObject($file);
        $writer = $app->make(CsvWriter::class, [
            'writer' => $csv
        ]);

        // Insert BOM if needed
        if ($bom) {
            $csv->setOutputBOM($bom);
        }

        // Write out data
        $entryList = $app->make(EntryList::class, [
            'entity' => $entity
        ]);
        $entryList->ignorePermissions();
        $writer->insertHeaders($entity);
        $writer->insertEntryList($entryList);

        // Success
        return static::SUCCESS;
    }

}
