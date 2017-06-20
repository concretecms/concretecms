<?php
namespace Concrete\Tests\Core\Permission;

use Concrete\Core\Permission\IPService;
use Concrete\Core\Support\Facade\Application;
use ConcreteDatabaseTestCase;
use DateTime;
use IPLib\Factory as IPFactory;

class IPServiceTest extends ConcreteDatabaseTestCase
{
    protected $tables = [
        'FailedLoginAttempts',
        'LoginControlIpRanges',
    ];

    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    private $config;

    /**
     * @var array
     */
    private $originalConfig;

    /**
     * @var IPService
     */
    private $ipService;

    protected function setUp()
    {
        parent::setUp();
        $this->connection()->executeQuery('DELETE FROM FailedLoginAttempts');
        $this->connection()->executeQuery('DELETE FROM LoginControlIpRanges');
        $app = Application::getFacadeApplication();
        $this->config = $app->make('config');
        $this->originalConfig = $this->config->get('concrete.security.ban.ip');
        $this->ipService = $app->build(
            IPService::class,
            [
                'config' => $this->config,
                'connection' => $this->connection(),
            ]
        );
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->config->set('concrete.security.ban.ip', $this->originalConfig);
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
     */
    public function testAutomaticBanEnabled($allowedAttempts)
    {
        $this->config->set('concrete.security.ban.ip.enabled', true);
        $this->config->set('concrete.security.ban.ip.attempts', $allowedAttempts);
        $this->config->set('concrete.security.ban.ip.time', 300);
        $this->config->set('concrete.security.ban.ip.length', 10);
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
        $this->config->set('concrete.security.ban.ip.enabled', false);
        $this->config->set('concrete.security.ban.ip.attempts', 3);
        $this->config->set('concrete.security.ban.ip.time', 300);
        $this->config->set('concrete.security.ban.ip.length', 10);
        $ip = IPFactory::addressFromString('1.2.3.4');
        for ($attempt = 1; $attempt <= 10; ++$attempt) {
            $this->ipService->logFailedLogin($ip);
        }
        $this->assertFalse($this->ipService->failedLoginsThresholdReached($ip));
        $this->assertFalse($this->ipService->isBlacklisted($ip));
    }

    public function testWhitelisted()
    {
        $this->config->set('concrete.security.ban.ip.enabled', true);
        $this->config->set('concrete.security.ban.ip.attempts', 3);
        $this->config->set('concrete.security.ban.ip.time', 300);
        $this->config->set('concrete.security.ban.ip.length', 10);
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
        $this->config->set('concrete.security.ban.ip.enabled', true);
        $this->config->set('concrete.security.ban.ip.attempts', 3);
        $this->config->set('concrete.security.ban.ip.time', 300);
        $this->config->set('concrete.security.ban.ip.length', 10);
        $ip = IPFactory::addressFromString('1.2.3.4');
        $this->assertFalse($this->ipService->isWhitelisted($ip));
        $this->assertFalse($this->ipService->isBlacklisted($ip));
        $this->ipService->createRange(IPFactory::rangeFromString('1.*.*.*'), IPService::IPRANGETYPE_BLACKLIST_MANUAL);
        $this->assertFalse($this->ipService->isWhitelisted($ip));
        $this->assertTrue($this->ipService->isBlacklisted($ip));
    }

    public function testBlacklistedExpiration()
    {
        $this->config->set('concrete.security.ban.ip.enabled', true);
        $this->config->set('concrete.security.ban.ip.attempts', 3);
        $this->config->set('concrete.security.ban.ip.time', 300);
        $this->config->set('concrete.security.ban.ip.length', 10);
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
        $this->config->set('concrete.security.ban.ip.enabled', true);
        $this->config->set('concrete.security.ban.ip.attempts', 3);
        $this->config->set('concrete.security.ban.ip.time', 300);
        $this->config->set('concrete.security.ban.ip.length', 10);
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
