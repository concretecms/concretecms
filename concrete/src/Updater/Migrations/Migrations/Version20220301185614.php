<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Permission\IpAccessControlCategory;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManagerInterface;

final class Version20220301185614 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $em = $this->app->make(EntityManagerInterface::class);
        $repo = $em->getRepository(IpAccessControlCategory::class);
        if ($repo->findOneBy(['handle' => 'forgot_password']) !== null) {
            return;
        }
        $category = new IpAccessControlCategory();
        $category
            ->setHandle('forgot_password')
            ->setName('Forgot Password Attempts')
            ->setEnabled(true)
            ->setMaxEvents(2)
            ->setTimeWindow(30)
            ->setBanDuration(600)
            ->setSiteSpecific(false)
        ;
        $em->persist($category);
        $em->flush($category);
    }
}
