<?php

namespace Concrete\Core\Console\Command;

use Concrete\Core\Console\Command;
use Concrete\Core\Database\CharacterSetCollation\Exception;
use Concrete\Core\Database\CharacterSetCollation\Exception\UnsupportedCharacterSetException;
use Concrete\Core\Database\CharacterSetCollation\Exception\UnsupportedCollationException;
use Concrete\Core\Database\CharacterSetCollation\Manager;
use Concrete\Core\Database\CharacterSetCollation\Resolver;
use Concrete\Core\Database\DatabaseManager;

class SetDatabaseCharacterSetCommand extends Command
{
    protected $canRunAsRoot = false;

    protected $description = 'Set the character set and collation of a database connection.';

    protected $signature = <<<'EOT'
c5:database:charset:set
    {charset : the character set or the collation to be used for the connection}
    {connection? : the name of the connection - if not specified we'll use the default one}
    {--f|force : re-apply the character set/collation even if they should already be in use}
    {--e|environment : The environment, if none specified the global configuration will be used}
EOT
    ;

    public function handle(DatabaseManager $databaseManager, Resolver $resolver, Manager $manager)
    {
        $connectionName = (string) $this->argument('connection');
        if ($connectionName === '') {
            $connectionName = $databaseManager->getDefaultConnection();
        }
        $connection = $databaseManager->connection($connectionName);
        try {
            list($characterSet, $collation) = $resolver->setCharacterSet($this->argument('charset'))->setCollation('')->resolveCharacterSetAndCollation($connection);
        } catch (UnsupportedCharacterSetException $x) {
            try {
                list($characterSet, $collation) = $resolver->setCharacterSet('')->setCollation($this->argument('charset'))->resolveCharacterSetAndCollation($connection);
            } catch (UnsupportedCollationException $x) {
                $this->output->error(sprintf('"%s" is neither a valid character set nor a valid collation.', $this->argument('charset')));

                return static::INVALID;
            } catch (Exception $x) {
                $this->output->error($x->getMessage());

                return static::FAILURE;
            }
        } catch (Exception $x) {
            $this->output->error($x->getMessage());

            return static::FAILURE;
        }
        $params = $connection->getParams();
        if (isset($params['character_set']) && $params['character_set'] === $characterSet && isset($params['collation']) && $params['collation'] === $collation) {
            if (!$this->option('force')) {
                $this->output->writeln(sprintf('Skipping since the connection "%s" is already using character set "%s" and collation "%s"', $connectionName, $characterSet, $collation));

                return static::SUCCESS;
            }
        }
        $manager->apply(
            $characterSet,
            $collation,
            $connectionName,
            $this->option('environment'),
            function ($message) {
                $this->output->writeln($message);
            }
        );

        return static::SUCCESS;
    }
}
