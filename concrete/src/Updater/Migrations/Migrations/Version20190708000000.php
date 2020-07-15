<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Attribute\Category\PageCategory;
use Concrete\Core\Entity\Permission\IpAccessControlCategory;
use Concrete\Core\Entity\Permission\IpAccessControlEvent;
use Concrete\Core\Entity\Permission\IpAccessControlRange;
use Concrete\Core\Page\Page;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\ORM\EntityManagerInterface;

class Version20190708000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $this->refreshEntities([
            IpAccessControlCategory::class,
            IpAccessControlRange::class,
            IpAccessControlEvent::class,
        ]);
        $this->createFailedLoginCategory();
        $this->createSinglePage(
            '/dashboard/system/permissions/blacklist/configure',
            'Configure IP Blocking',
            ['exclude_nav' => true]
        );
        $rangePage = Page::getByPath('/dashboard/system/permissions/blacklist/range');
        if ($rangePage && !$rangePage->isError()) {
            if ($this->isAttributeHandleValid(PageCategory::class, 'exclude_nav')) {
                $rangePage->setAttribute('exclude_nav', true);
            }
        }
    }

    protected function createFailedLoginCategory()
    {
        $em = $this->app->make(EntityManagerInterface::class);
        $repo = $em->getRepository(IpAccessControlCategory::class);
        if ($repo->findOneBy(['handle' => 'failed_login']) !== null) {
            return;
        }
        $config = $this->app->make('config');
        $category = new IpAccessControlCategory();
        $category
            ->setHandle('failed_login')
            ->setName('Failed Login Attempts')
            ->setEnabled($config->get('concrete.security.ban.ip.enabled', true))
            ->setMaxEvents($config->get('concrete.security.ban.ip.attempts', 5))
            ->setTimeWindow($config->get('concrete.security.ban.ip.time', 300))
            ->setBanDuration(60 * $config->get('concrete.security.ban.ip.length', 10) ?: null)
            ->setSiteSpecific(false)
        ;
        $em->persist($category);
        $em->flush($category);
        $this->copyLoginControlIpRanges($category);
        $this->copyFailedLoginAttempts($category);
    }

    protected function copyLoginControlIpRanges(IpAccessControlCategory $category)
    {
        if (!$this->connection->tableExists('LoginControlIpRanges')) {
            return;
        }
        $site = $this->app->make('site')->getSite();
        $this->connection->executeQuery(
            '
INSERT INTO IpAccessControlRanges
    (
        iacrCategory,
        iacrSite,
        iacrIpFrom,
        iacrIpTo,
        iacrType,
        iacrExpiration
    )
    SELECT
        ?,
        ?,
        lcirIpFrom,
        lcirIpTo,
        lcirType,
        lcirExpires
    FROM
        LoginControlIpRanges
            ',
            [
                $category->getIpAccessControlCategoryID(),
                $site ? $site->getSiteID() : null,
            ]
        );
    }

    protected function copyFailedLoginAttempts(IpAccessControlCategory $category)
    {
        if (!$this->connection->tableExists('FailedLoginAttempts')) {
            return;
        }
        $site = $this->app->make('site')->getSite();
        $this->connection->executeQuery(
            '
INSERT INTO IpAccessControlEvents
    (
        iaceCategory,
        iaceSite,
        iaceIp,
        iaceDateTime
    )
    SELECT
        ?,
        ?,
        flaIp,
        flaTimestamp
    FROM
        FailedLoginAttempts
            ',
            [
                $category->getIpAccessControlCategoryID(),
                $site ? $site->getSiteID() : null,
            ]
        );
    }
}
