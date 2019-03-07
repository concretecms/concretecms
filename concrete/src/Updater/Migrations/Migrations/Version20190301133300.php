<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use PDO;

class Version20190301133300 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $this->fixDuplicatedStacks();
    }

    protected function fixDuplicatedStacks()
    {
        foreach ($this->getDuplicatedStacks() as $duplicatedStack) {
            $this->fixDuplicatedStack($duplicatedStack);
        }
    }

    /**
     * @return array[]
     */
    protected function getDuplicatedStacks()
    {
        $rs = $this->connection->executeQuery(<<<'EOT'
SELECT
    Stacks.stID, Stacks.cID, SiteTrees.siteTreeID
FROM
    Stacks
    INNER JOIN (
        SELECT cID
        FROM Stacks
        GROUP BY cID
        HAVING COUNT(stID) > 1
    ) AS DuplicatedStacks
    ON Stacks.cID = DuplicatedStacks.CID
    LEFT JOIN SiteTrees
    ON Stacks.stMultilingualSection = SiteTrees.siteHomePageID
EOT
        );
        $dataPerCID = [];
        while (($row = $rs->fetch(PDO::FETCH_ASSOC)) !== false) {
            $cID = $row['cID'];
            if (!isset($dataPerCID[$cID])) {
                $dataPerCID[$cID] = [];
            }
            $dataPerCID[$cID][] = $row;
        }

        return array_values($dataPerCID);
    }

    /**
     * @param array[] $stackList
     */
    protected function fixDuplicatedStack(array $stackList)
    {
        $stacksWithSection = array_filter($stackList, function (array $stack) {
            return !empty($stack['siteTreeID']);
        });
        if (!empty($stacksWithSection)) {
            foreach ($stackList as $stack) {
                if (!in_array($stack, $stacksWithSection, true)) {
                    $this->connection->delete('Stacks', ['stID' => $stack['stID']]);
                }
            }
        }
    }
}
