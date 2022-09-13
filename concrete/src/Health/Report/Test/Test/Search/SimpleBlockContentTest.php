<?php
namespace Concrete\Core\Health\Report\Test\Test\Search;

use Concrete\Core\Block\Block;
use Concrete\Core\Database\Schema\Parser\Axmls;
use Concrete\Core\Entity\Block\BlockType\BlockType;
use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Filesystem\FileLocator\PackageLocation;
use Concrete\Core\Health\Report\Runner;
use Concrete\Core\Health\Report\Test\TestInterface;
use Concrete\Core\Health\Search\Traits\SearchContentTrait;
use Doctrine\ORM\EntityManagerInterface;

class SimpleBlockContentTest extends Axmls implements TestInterface
{
    use SearchContentTrait;

    private $em;
    private $locator;
    private $scanTables = [];

    public function __construct(EntityManagerInterface $em, FileLocator $locator)
    {
        $this->em = $em;
        $this->locator = $locator;
        $this->scanTables['btContentLocal'] = ['bID', 'content'];
    }

    public function run(Runner $report): void
    {
        $typeRepository = $this->em->getRepository(BlockType::class);
        /** @var BlockType[] $types */
        $types = $typeRepository->findAll();

        foreach ($types as $type) {
            $typeHandle = $type->getBlockTypeHandle();
            if ($typeHandle !== 'content' && $typeHandle !== 'html') {
                $this->addTable($type);
            }
        }

        // Alright we're ready to scan.
        foreach ($this->scanTables as $table => $columns) {
            $this->auditTable($report, $table, $columns);
        }
    }

    private function auditTable(Runner $report, string $table, array $columns): void
    {
        $qb = $this->em->getConnection()->createQueryBuilder();
        $hasID = in_array('bID', $columns);

        // If we don't have an ID we can't really figure out what this content is related to reliably.
        // In the future it's possible that we can notify on findings in orphaned data.
        if (!$hasID) {
            return;
        }

        $columns = collect($columns)
            ->filter(function($column) { return $column !== 'bID'; });

        if (!$columns->count()) {
            return;
        }

        $columns = $columns->map(function($column) use ($table) { return "`{$table}`.`{$column}`"; });

        // Add column select, if there are more than one columns just concat them together.
        if ($columns->count() > 1) {
            $columns = $columns->implode(',');
            $qb->select("concat({$columns}) as content");
        } else {
            $columns = $columns->first();
            $qb->select("{$columns} as content");
        }

        $qb->addSelect('bID');
        $qb->from($table);

        if (!$this->applyQueryFilters($report, $qb)) {
            return;
        }

        foreach ($this->iterateQuery($qb, 1000) as $item) {
            $id = $item['bID'] ?? 0;

            if (!$id) {
                continue;
            }

            $block = Block::getByID($id);

            if ($block instanceof Block) {
                $content = $item['content'] ?? '';
                $this->addBlockWarning($report, $block, $content);
            }
        }
    }

    /**
     * Locate a block's db.xml file and track its table to be scanned
     *
     * @param BlockType $type
     *
     * @return bool
     */
    private function addTable(BlockType $type): bool
    {
        $pkgHandle = $type->getPackageHandle();
        $locator = clone $this->locator;

        if ($pkgHandle) {
            $locator->addLocation(new PackageLocation($pkgHandle));
        }

        $path = $locator->getRecord(DIRNAME_BLOCKS . '/' . $type->getBlockTypeHandle() . '/' . FILENAME_BLOCK_DB)->getFile();
        if (!file_exists($path)) {
            return false;
        }

        $xml = simplexml_load_file($path);
        $allowedTypes = ['text', 'string', 'json', 'json_array'];
        $tableCount = 0;

        $scanAdditions = [];
        foreach ($xml->table as $tableNode) {
            $table = (string) $tableNode['name'];
            $tableCount++;

            foreach ($tableNode->field as $fieldNode) {
                $column = (string) $fieldNode['name'];

                $columnType = strtolower($this->_getColumnType($fieldNode) ?: $fieldNode['type']);
                if (in_array($columnType, $allowedTypes) || $column === 'bID') {
                    $scanAdditions[$table] = $scanAdditions[$table] ?? [];
                    $scanAdditions[$table][] = $column;
                }
            }
        }

        if ($tableCount === 1) {
            foreach ($scanAdditions as $table => $columns) {
                $this->scanTables[$table] = $columns;
            }
        }

        return true;
    }

}
