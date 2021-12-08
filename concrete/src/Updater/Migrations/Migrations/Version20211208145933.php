<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Page\Container;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211208145933 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $em = $this->connection->getEntityManager();
        $names = [
            'two_column_light' => 'Two Column Highlight',
            'light_stripe' => 'Highlight Stripe',
        ];
        $containersFound = false;
        foreach ($names as $handle => $name) {
            $container = $em->getRepository(Container::class)
                ->findOneByContainerHandle($handle);
            if ($container) {
                $containersFound = true;
                $container->setContainerName($name);
                $em->persist($container);
            }
        }
        if ($containersFound) {
            $em->flush();
        }
    }
}
