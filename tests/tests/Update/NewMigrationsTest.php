<?php

namespace Concrete\Tests\Update;

use CIInfo\Exception;
use CIInfo\State\PullRequest;
use CIInfo\StateFactory;
use PHPUnit\Framework\TestCase;

class NewMigrationsTest extends TestCase
{
    public function testNewMigrations(): void
    {
        $state = $this->getPullRequestState();
        $pullRequestMigrationIDs = $this->listPullRequestMigrationIDs($state);
        if ($pullRequestMigrationIDs === []) {
            $invalidMigrationIDs = [];
            $baseMigrationID = '';
        } else {
            $baseMigrationID = $this->getBaseMigrationID($state);
            $invalidMigrationIDs = array_filter($pullRequestMigrationIDs, function (string $pullRequestMigrationID) use ($baseMigrationID): bool {
                return $pullRequestMigrationID < $baseMigrationID;
            });
        }
        $this->assertSame(
            [],
            $invalidMigrationIDs,
            <<<EOT
There shouldn't be any migration with an ID lower than {$baseMigrationID}
for commits between
{$state->getBaseSha1()}
and
{$state->getMergeSha1()} 
EOT
        );
    }

    /**
     * @throws \PHPUnit\Framework\SkippedTestError
     */
    protected function getPullRequestState(): PullRequest
    {
        try {
            $state = (new StateFactory())->getCurrentState();
        } catch (Exception $x) {
            $this->markTestSkipped('Failed to get the CI state: ' . $x->getMessage());
        }
        if (!($state instanceof PullRequest)) {
            $this->markTestSkipped('Test valid only for pull requests (current job type: ' . $state->getEvent() . ')');
        }

        return $state;
    }

    /**
     * @throws \PHPUnit\Framework\SkippedTestError
     */
    protected function getBaseMigrationID(PullRequest $state): string
    {
        $rc = -1;
        $output = [];
        exec('git -C ' . escapeshellarg(str_replace('/', DIRECTORY_SEPARATOR, DIR_BASE)) . " show {$state->getBaseSha1()}:concrete/config/concrete.php 2>&1", $output, $rc);
        if ($rc !== 0) {
            $this->markTestSkipped("Failed to retrieve the base migration ID:\n" . trim(implode("\n", $output)));
        }
        $matches = null;
        foreach ($output as $line) {
            if (preg_match('/^\s*(["\'])version_db\1\s*=>\s*(["\'])(\d+)\2/', $line, $matches)) {
                return $matches[3];
            }
        }
        $this->markTestSkipped('Failed to extract the base migration ID');
    }

    /**
     * @throws \PHPUnit\Framework\SkippedTestError
     *
     * @return string[]
     */
    protected function listPullRequestMigrationIDs(PullRequest $state): array
    {
        $rc = -1;
        $output = [];
        exec('git -C ' . escapeshellarg(str_replace('/', DIRECTORY_SEPARATOR, DIR_BASE)) . " diff --name-status {$state->getBaseSha1()}..{$state->getMergeSha1()} 2>&1", $output, $rc);
        if ($rc !== 0) {
            $this->markTestSkipped("Failed to list the git changes (maybe the fetched git history is too short?):\n" . trim(implode("\n", $output)));
        }
        $result = [];
        $matches = null;
        foreach ($output as $line) {
            if (preg_match('%^[AR].*\sconcrete/src/Updater/Migrations/Migrations/Version(\d+)\.php\s*$%', $line, $matches)) {
                $result[] = $matches[1];
            }
        }

        return $result;
    }
}
