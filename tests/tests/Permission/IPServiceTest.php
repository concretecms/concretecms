<?php

namespace Concrete\Tests\Permission;

use Concrete\Core\Entity\Permission\IpAccessControlCategory;
use Concrete\Core\Entity\Permission\IpAccessControlEvent;
use Concrete\Core\Entity\Permission\IpAccessControlRange;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Permission\IPService;
use Concrete\Core\Site\Factory as SiteFactory;
use Concrete\Core\Support\Facade\Application;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use IPLib\Factory as IPFactory;

class IPServiceTest extends ConcreteDatabaseTestCase
{
    protected $tables = [
        'Logs',
    ];

    protected $metadatas = [
        Site::class,
        IpAccessControlCategory::class,
        IpAccessControlEvent::class,
        IpAccessControlRange::class,
    ];

    /**
     * @var \Concrete\Core\Entity\Permission\IpAccessControlCategory
     */
    private $category;

    /**
     * @var \Concrete\Core\Permission\IPService
     */
    private $ipService;

    protected function setUp()
    {
        parent::setUp();
        $app = Application::getFacadeApplication();
        $app->make('cache/request')->flush();
        $em = $app->make(EntityManagerInterface::class);
        $em->createQueryBuilder()->delete(Site::class)->getQuery()->execute();
        $em->createQueryBuilder()->delete(IpAccessControlEvent::class)->getQuery()->execute();
        $em->createQueryBuilder()->delete(IpAccessControlRange::class)->getQuery()->execute();
        $em->createQueryBuilder()->delete(IpAccessControlCategory::class)->getQuery()->execute();
        $site = $app->make(SiteFactory::class)->createDefaultEntity();
        $em->persist($site);
        $em->flush($site);
        $this->category = new IpAccessControlCategory();
        $this->category
            ->setHandle('failed_login')
            ->setName('Failed Login Attempts')
            ->setEnabled(true)
            ->setMaxEvents(3)
            ->setTimeWindow(300)
            ->setBanDuration(600)
            ->setSiteSpecific(false)
        ;
        $em->persist($this->category);
        $em->flush($this->category);
        $this->ipService = $app->make(IPService::class);
    }

    public function automaticBanEnabledProvider()
    {
        return [
            [1],
            [2],
            [5],
        ];
    }

    /**
     * @dataProvider automaticBanEnabledProvider
     *
     * @param mixed $allowedAttempts
     */
    public function testAutomaticBanEnabled($allowedAttempts)
    {
        $this->category
            ->setEnabled(true)
            ->setMaxEvents($allowedAttempts)
            ->setTimeWindow(300)
            ->setBanDuration(600)
        ;
        $ip = IPFactory::addressFromString('1.2.3.4');
        for ($attempt = 1; $attempt <= $allowedAttempts; ++$attempt) {
            $this->assertFalse($this->ipService->isWhitelisted($ip));
            $this->assertFalse($this->ipService->isBlacklisted($ip));
            $this->assertFalse($this->ipService->failedLoginsThresholdReached($ip));
            $this->ipService->logFailedLogin($ip);
            if ($attempt < $allowedAttempts) {
                $this->assertFalse($this->ipService->failedLoginsThresholdReached($ip));
            } else {
                $this->assertTrue($this->ipService->failedLoginsThresholdReached($ip));
            }
        }
        $this->assertFalse($this->ipService->isWhitelisted($ip));
        $this->assertFalse($this->ipService->isBlacklisted($ip));
        $this->ipService->addToBlacklistForThresholdReached($ip);
        $this->assertFalse($this->ipService->isWhitelisted($ip));
        $this->assertTrue($this->ipService->isBlacklisted($ip));
        $ip2 = IPFactory::addressFromString('::');
        $this->assertFalse($this->ipService->isWhitelisted($ip2));
        $this->assertFalse($this->ipService->isBlacklisted($ip2));
    }

    public function testAutomaticBanDisabled()
    {
        $this->category
            ->setEnabled(false)
            ->setMaxEvents(3)
            ->setTimeWindow(300)
            ->setBanDuration(600)
        ;
        $ip = IPFactory::addressFromString('1.2.3.4');
        for ($attempt = 1; $attempt <= 10; ++$attempt) {
            $this->ipService->logFailedLogin($ip);
        }
        $this->assertFalse($this->ipService->failedLoginsThresholdReached($ip));
        $this->assertFalse($this->ipService->isBlacklisted($ip));
    }

    public function testWhitelisted()
    {
        $this->category
            ->setEnabled(true)
            ->setMaxEvents(3)
            ->setTimeWindow(300)
            ->setBanDuration(600)
        ;
        $ip = IPFactory::addressFromString('1.2.3.4');
        $this->assertFalse($this->ipService->isWhitelisted($ip));
        $this->assertFalse($this->ipService->isBlacklisted($ip));
        $this->ipService->createRange(IPFactory::rangeFromString('1.2.3.*'), IPService::IPRANGETYPE_WHITELIST_MANUAL);
        $this->assertTrue($this->ipService->isWhitelisted($ip));
        $this->assertFalse($this->ipService->isBlacklisted($ip));
        for ($attempt = 1; $attempt <= 10; ++$attempt) {
            $this->ipService->logFailedLogin($ip);
        }
        $this->assertFalse($this->ipService->failedLoginsThresholdReached($ip));
        $this->assertTrue($this->ipService->isWhitelisted($ip));
        $this->assertFalse($this->ipService->isBlacklisted($ip));
    }

    public function testBlacklistedPermament()
    {
        $this->category
            ->setEnabled(true)
            ->setMaxEvents(3)
            ->setTimeWindow(300)
            ->setBanDuration(600)
        ;
        $ip = IPFactory::addressFromString('1.2.3.4');
        $this->assertFalse($this->ipService->isWhitelisted($ip));
        $this->assertFalse($this->ipService->isBlacklisted($ip));
        $this->ipService->createRange(IPFactory::rangeFromString('1.*.*.*'), IPService::IPRANGETYPE_BLACKLIST_MANUAL);
        $this->assertFalse($this->ipService->isWhitelisted($ip));
        $this->assertTrue($this->ipService->isBlacklisted($ip));
    }

    public function testBlacklistedExpiration()
    {
        $this->category
            ->setEnabled(true)
            ->setMaxEvents(3)
            ->setTimeWindow(300)
            ->setBanDuration(600)
        ;
        $ip = IPFactory::addressFromString('1.2.3.4');
        $this->assertFalse($this->ipService->isWhitelisted($ip));
        $this->assertFalse($this->ipService->isBlacklisted($ip));
        $this->ipService->createRange(IPFactory::rangeFromString('1.*.*.*'), IPService::IPRANGETYPE_BLACKLIST_AUTOMATIC, new DateTime('-1 seconds'));
        $this->assertFalse($this->ipService->isWhitelisted($ip));
        $this->assertFalse($this->ipService->isBlacklisted($ip));
        $this->ipService->createRange(IPFactory::rangeFromString('1.*.*.*'), IPService::IPRANGETYPE_BLACKLIST_AUTOMATIC, new DateTime('+10 seconds'));
        $this->assertFalse($this->ipService->isWhitelisted($ip));
        $this->assertTrue($this->ipService->isBlacklisted($ip));
    }

    public function testBlacklistedVsWhitelisted()
    {
        $this->category
            ->setEnabled(true)
            ->setMaxEvents(3)
            ->setTimeWindow(300)
            ->setBanDuration(600)
        ;
        $ip = IPFactory::addressFromString('1.2.3.4');
        $this->assertFalse($this->ipService->isWhitelisted($ip));
        $this->assertFalse($this->ipService->isBlacklisted($ip));
        $this->ipService->createRange(IPFactory::rangeFromString('1.*.*.*'), IPService::IPRANGETYPE_BLACKLIST_AUTOMATIC, new DateTime('+10 hours'));
        $this->assertFalse($this->ipService->isWhitelisted($ip));
        $this->assertTrue($this->ipService->isBlacklisted($ip));
        $this->ipService->createRange(IPFactory::rangeFromString('1.2.3.*'), IPService::IPRANGETYPE_WHITELIST_MANUAL, new DateTime('+10 hours'));
        $this->assertTrue($this->ipService->isWhitelisted($ip));
        $this->assertFalse($this->ipService->isBlacklisted($ip));
    }
}
