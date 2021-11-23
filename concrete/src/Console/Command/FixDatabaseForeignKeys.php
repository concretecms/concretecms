<?php

namespace Concrete\Core\Console\Command;

use ArrayObject;
use Concrete\Core\Console\Command;
use Concrete\Core\Database\ForeignKeyFixer;
use Concrete\Core\Error\UserMessageException;
use Exception;
use Throwable;

class FixDatabaseForeignKeys extends Command
{
    protected $canRunAsRoot = false;

    protected $description = 'Fix the foreign keys.';

    protected $signature = <<<'EOT'
c5:database:foreignkey:fix
    {table? : the name of the database table to be fixed - if not specified we'll fix all the tables in the database}
EOT
    ;

    public function handle(ForeignKeyFixer $foreignKeyFixer)
    {
        $tableName = $this->argument('table');
        $tableNames = $tableName === null ? null : [$tableName];

        $errors = new ArrayObject();
        $foreignKeyFixer->setTick(function ($what) {
            if ($what instanceof UserMessageException) {
                $this->output->error($what->getMessage());
            } elseif ($what instanceof Exception || $what instanceof Throwable) {
                $this->output->error($what->getMessage() . "\n" . $what->getTraceAsString());
            } else {
                $this->output->writeln((string) $what);
            }
        });
        $foreignKeyFixer->fixForeignKeys($tableNames, $errors);

        return $errors->count() === 0 ? static::SUCCESS : static::FAILURE;
    }
}
