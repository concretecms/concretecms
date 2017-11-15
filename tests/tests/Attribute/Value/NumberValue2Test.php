<?php

namespace Concrete\Tests\Attribute\Value;

use Concrete\Core\Entity\Attribute\Value\Value\NumberValue;
use Concrete\Core\Entity\Attribute\Value\Value\Value;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;
use Database;
use ORM;

class NumberValue2Test extends ConcreteDatabaseTestCase
{
    protected static $lastID = 1;

    protected $metadatas = [
        NumberValue::class,
        Value::class,
    ];

    public function renderProvider()
    {
        return [
            [null, null, ''],
            [1.23, '1.23', '1.23'],
            [200, '200', '200'],
            [199.9999, '199.9999', '199.9999'],
            [199.99999, '200', '200'],
            [1.2349, '1.2349', '1.2349'],
            [1000000000.2349, '1000000000.2349', '1000000000.2349'],
            [1000000000.234900000001, '1000000000.2349', '1000000000.2349'],
            [1000000000.234099999999, '1000000000.2341', '1000000000.2341'],
            [-1.23, '-1.23', '-1.23'],
            [-200, '-200', '-200'],
            [-199.9999, '-199.9999', '-199.9999'],
            [-199.99999, '-200', '-200'],
            [-1.2349, '-1.2349', '-1.2349'],
            [-1000000000.2349, '-1000000000.2349', '-1000000000.2349'],
            [-1000000000.234900000001, '-1000000000.2349', '-1000000000.2349'],
            [-1000000000.234099999999, '-1000000000.2341', '-1000000000.2341'],
        ];
    }

    /**
     * @dataProvider renderProvider
     *
     * @param mixed $value
     * @param mixed $expectedGetValue
     * @param mixed $expectedToString
     */
    public function testRender($value, $expectedGetValue, $expectedToString)
    {
        $avID = static::$lastID + 1;
        static::$lastID = $avID;

        $db = Database::connection();
        $db->executeQuery('insert into AttributeValues (avID) values (?)', [$avID]);
        $avID = $db->lastInsertId();
        $db->executeQuery('insert into atNumber (avID, value) values (?, ?)', [$avID, $value]);
        $em = ORM::entityManager();
        $repo = $em->getRepository(NumberValue::class);
        $entity = $repo->find($avID);
        $this->assertInstanceOf(NumberValue::class, $entity);
        $this->assertSame($expectedGetValue, $entity->getValue());
        $this->assertSame($expectedToString, (string) $entity);
    }
}
