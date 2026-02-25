<?php

declare(strict_types=1);

namespace Concrete\Tests\Encryption;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Encryption\PasswordHasher;
use Concrete\Core\Legacy\PasswordHash;
use Concrete\Tests\TestCase;

class PasswordHasherTest extends TestCase
{

    private function hasher(
        $algorithm = PASSWORD_BCRYPT,
        ?int $cost = null,
        array $hashOptions = [],
        bool $portable = false
    ) {
        $config = \Mockery::mock(Repository::class);

        $config->shouldReceive('get')->with('concrete.user.password.hash_algorithm')->andReturn($algorithm);
        $config->shouldReceive('get')->with('concrete.user.password.hash_options', [])->andReturn($hashOptions);
        $config->shouldReceive('get')->with(
            'concrete.user.password.hash_cost_log2',
            PASSWORD_BCRYPT_DEFAULT_COST
        )->andReturn($cost);
        $config->shouldReceive('get')->with('concrete.user.password.hash_portable')->andReturn($portable);

        return new PasswordHasher($config);
    }

    /**
     * @dataProvider portableHashes
     */
    public function testPortableHashesSucceed(string $password, string $hash)
    {
        $this->assertTrue($this->hasher()->checkPassword($password, $hash));
    }

    /**
     * @dataProvider bcryptHashes
     */
    public function testBcryptHashesSucceed(string $password, string $hash)
    {
        $this->assertTrue($this->hasher()->checkPassword($password, $hash));
    }

    /**
     * @dataProvider argon2IHashes
     */
    public function testArgon2IHashesSucceed(string $password, string $hash)
    {
        if (!defined('PASSWORD_ARGON2I')) {
            $this->markTestSkipped('Argon2i is not available.');
        }

        $this->assertTrue($this->hasher()->checkPassword($password, $hash));
    }

    /**
     * @dataProvider argon2IDHashes
     */
    public function testArgon2IDHashesSucceed(string $password, string $hash)
    {
        if (!defined('PASSWORD_ARGON2ID')) {
            $this->markTestSkipped('Argon2id is not available.');
        }

        $this->assertTrue($this->hasher()->checkPassword($password, $hash));
    }

    /**
     * @dataProvider portableHashes
     */
    public function testPortableRehash(string $password, string $hash)
    {
        $this->assertTrue($this->hasher()->needsRehash($hash));
    }

    /**
     * @dataProvider bcryptHashes
     */
    public function testBcryptRehash(string $password, string $hash)
    {
        [, $cost,] = explode('$', $hash);
        if (strpos('$2y', $hash) === 0) {
            // Normal PHP Bcrypt passwords
            $this->assertFalse($this->hasher(PASSWORD_BCRYPT, 0, ['cost' => (int) $cost])->needsRehash($hash));
        } else {
            // phpass Bcrypt passwords
            $this->assertTrue($this->hasher(PASSWORD_BCRYPT, 0, ['cost' => (int) $cost])->needsRehash($hash));
        }

        // Make sure we _do_ rehash when a different algorithm is selected
        $this->assertTrue($this->hasher(PASSWORD_ARGON2I)->needsRehash($hash));
    }

    /**
     * @dataProvider argon2IHashes
     */
    public function testArgonPasswordRehash(string $password, string $hash)
    {
        $this->assertFalse($this->hasher(PASSWORD_ARGON2I)->needsRehash($hash));
        $this->assertTrue($this->hasher(PASSWORD_BCRYPT)->needsRehash($hash));
    }

    private function testPasswords(): array
    {
        return [
            'test' => [
                '$P$8g.Za0pIMib3iN5n67qPPdcsO4SOEP.',
                '$2y$10$pWumkN0.3qMv2tKrUI.T6uJZTM3yh74pF1dT9bnXBBEco4tXMjGrS',
                '$argon2i$v=19$m=65536,t=4,p=1$cXl0SWpIQ1FrekdkclJjNg$Co3gyfUDrORD2dHDcmgLtc06HSwEQVXpCDPsbtwCLrA',
                '$argon2id$v=19$m=65536,t=4,p=1$UDlRQUhlZUFUTzdScWh3aQ$JgLJV6aSjDCZcteay2yIbA8WwoVOHr7lxcnbSl1H+sY',
            ],
            'foobar' => [
                '$P$88oBskqXEWX7rOxrCyNaDok5B/oN3C1',
                '$2y$10$10q/v3k7mo0Z8h7JIPpEvOlsgs8i7AxGVwWbEHBfc3.Qv/YYJ7jCe',
                '$argon2i$v=19$m=65536,t=4,p=1$QmphTXpjdzF5a3hvYXdHcA$FlmL/MDZhgH7j36AlSEDaGcEEebU5GzJtoQ5zvFCUVE',
                '$argon2id$v=19$m=65536,t=4,p=1$aE9iTk1mNFR4NVFzclQ5Rw$re30N5yfOepebPBGo/K7ZyBt+8B232Wblkqg7fmHNR0',
            ],
            '12342534634556394802458201394r578013947562358630-4586-2085-2385034786908345679034578602834512' => [
                '$P$BuHyIFZwn/f/JEOqo3nh2kQKCRqGBg.',
                '$2y$10$IC.ZoKeDI1zcctL1pSaLYOPH.QV6ohWAuRR5iXE.EYkqTkBldGQmW',
                '$argon2i$v=19$m=65536,t=4,p=1$Qy4yQTRrRXFCWS9DeTdWcg$Uqp17XIJTdEI155pLeVcre6szpRtNBh2WZVianbfTyc',
                '$argon2id$v=19$m=65536,t=4,p=1$Q292Q0R0MGMuZTRMSFZNUQ$wD8764iNx8RqIK5ca7WfmUOrAtm7CJU4e5b+wkVpXUI',
            ],
            '' => [
                '$P$8r7Ap2g6hrAkULLh.YcklS9BSqHjVa/',
                '$2y$10$/qciYxbCfqkhUvlEsViY7ec/ejn7N0lCqJ19z1hht2a.tXGYUU8uy',
                '$argon2i$v=19$m=65536,t=4,p=1$L0FkNEEzSHFrZGwwQ3ZhRg$pKH2B/D/I+geZz8cuLzUsfPikdjaxRcA8iIKC7RyTC4',
                '$argon2id$v=19$m=65536,t=4,p=1$U3VkcTgwamQxMzhtUGlaNA$QlqcO+MXAigKBMLbUHCzb9wJ/Vp5JzukRah9veONCnI',
            ],
        ];
    }


    public function portableHashes(): iterable
    {
        foreach ($this->testPasswords() as $password => $hashes) {
            yield [$password, $hashes[0]];
        }

        $hasher = new PasswordHash(5, true);
        foreach (array_keys($this->testPasswords()) as $password) {
            yield [$password, $hasher->HashPassword($password)];
        }
    }

    public function bcryptHashes(): iterable
    {
        foreach ($this->testPasswords() as $password => $hashes) {
            yield [$password, $hashes[1]];
        }

        $hasher = new PasswordHash(5, false);
        foreach (array_keys($this->testPasswords()) as $password) {
            yield [$password, $hasher->HashPassword($password)];
        }
    }

    public function argon2IHashes(): iterable
    {
        foreach ($this->testPasswords() as $password => $hashes) {
            yield [$password, $hashes[2]];
        }
    }

    public function argon2IDHashes(): iterable
    {
        foreach ($this->testPasswords() as $password => $hashes) {
            yield [$password, $hashes[3]];
        }
    }

}
