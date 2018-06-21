<?php

namespace Concrete\Tests\User;

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\Validation\UsernameValidator;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;

class UsernameValidatorTest extends ConcreteDatabaseTestCase
{
    protected $fixtures = [
        'UsernameValidator',
    ];

    /**
     * @var \Concrete\Core\User\Validation\UsernameValidator
     */
    private static $defaultInstance;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->metadatas[] = \Concrete\Core\Entity\User\User::class;
    }

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        $app = Application::getFacadeApplication();
        self::$defaultInstance = $app->make(UsernameValidator::class);
    }

    public function usernameValidatorTestProvider()
    {
        return [
            [null, UsernameValidator::E_INVALID_STRING],
            ['', UsernameValidator::E_INVALID_STRING],
            [$this, UsernameValidator::E_INVALID_STRING],
            ['this.is.good', 0],
            ['s', UsernameValidator::E_TOO_SHORT],
            [str_repeat('x', 9999), UsernameValidator::E_TOO_LONG],
            ['invalid<chars>', UsernameValidator::E_INVALID_CHARACTERS],
            ['<', UsernameValidator::E_TOO_SHORT | UsernameValidator::E_INVALID_CHARACTERS],
            [str_repeat('>', 9999), UsernameValidator::E_TOO_LONG | UsernameValidator::E_INVALID_CHARACTERS],
            ['admin', UsernameValidator::E_IN_USE],
            ['admin', UsernameValidator::E_IN_USE, 2],
            ['admin', UsernameValidator::E_OK, 1],
        ];
    }

    /**
     * @dataProvider usernameValidatorTestProvider
     *
     * @param string $username
     * @param int $expectedFlags
     * @param null|mixed $uID
     */
    public function testUsernameValidator($username, $expectedFlags, $uID = null)
    {
        $flags = static::$defaultInstance->check($username, $uID);
        $this->assertSame($expectedFlags, $flags);
        $errorDescriptions = static::$defaultInstance->describeError($flags);
        if ($expectedFlags === UsernameValidator::E_OK) {
            $this->assertFalse($errorDescriptions->has());
        } else {
            $this->assertTrue($errorDescriptions->has());
        }
    }
}
