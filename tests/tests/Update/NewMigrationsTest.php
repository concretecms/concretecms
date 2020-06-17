<?php

namespace Concrete\Tests\Update;

use Concrete\TestHelpers\CI\Exception;
use Concrete\TestHelpers\CI\PullRequestState;
use Concrete\TestHelpers\CI\StateFactory;
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
        $this->assertSame([], $invalidMigrationIDs, "There shouldn't be any migration with an ID lower than {$baseMigrationID}");
    }

    /**
     * @throws \PHPUnit\Framework\SkippedTestError
     */
    protected function getPullRequestState(): PullRequestState
    {
        try {
            $state = app(StateFactory::class, ['env' => getenv()])->getState();
        } catch (Exception $x) {
            $this->markTestSkipped('Failed to get the CI state: ' . $x->getMessage());
        }
        if (!($state instanceof PullRequestState)) {
            $this->markTestSkipped('Test valid only for pull request (current event: ' . $state->getEvent() . ')');
        }

        return $state;
    }

    /**
     * @throws \PHPUnit\Framework\SkippedTestError
     */
    protected function getBaseMigrationID(PullRequestState $state): string
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
    protected function listPullRequestMigrationIDs(PullRequestState $state): array
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
