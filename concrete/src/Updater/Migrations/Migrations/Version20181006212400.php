<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Database\CharacterSetCollation\Exception as CharacterSetCollationException;
use Concrete\Core\Database\CharacterSetCollation\Manager;
use Concrete\Core\Database\CharacterSetCollation\Resolver;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\LongRunningMigrationInterface;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Exception;
use Throwable;

class Version20181006212400 extends AbstractMigration implements RepeatableMigrationInterface, LongRunningMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $config = $this->app->make('config');
        $resolver = $this->app->make(Resolver::class);
        $error = null;
        try {
            list($characterSet, $collation) = $resolver
                ->setCharacterSet((string) $config->get('database.preferred_character_set'))
                ->setCollation((string) $config->get('database.preferred_collation'))
                ->resolveCharacterSetAndCollation($this->connection)
            ;
        } catch (CharacterSetCollationException $x) {
            try {
                list($characterSet, $collation) = $resolver
                    ->setCharacterSet((string) $config->get('database.fallback_character_set'))
                    ->setCollation((string) $config->get('database.fallback_collation'))
                    ->resolveCharacterSetAndCollation($this->connection)
                ;
            } catch (Exception $x) {
                $error = $x;
            }
        } catch (Throwable $x) {
            $error = $x;
        }
        if ($error !== null) {
            $this->output(t('Failed to resolve the database character set and collation: %s', $error->getMessage()));

            return;
        }
        $params = $this->connection->getParams();
        if (isset($params['character_set']) && $params['character_set'] === $characterSet && isset($params['collation']) && $params['collation'] === $collation) {
            return;
        }
        $manager = $this->app->make(Manager::class);
        try {
            $manager->apply(
                $characterSet,
                $collation,
                '',
                '',
                function ($message) {
                    $this->output($message);
                }
            );
        } catch (Exception $x) {
            $this->output(t('Failed to set character sets: %s', $x->getMessage()));
        } catch (Throwable $x) {
            $this->output(t('Failed to set character sets: %s', $x->getMessage()));
        }
    }
}
