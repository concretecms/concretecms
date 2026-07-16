<?php

namespace Concrete\Tests\Foundation\Serializer;

use Concrete\Core\Foundation\Serializer\SafeClassUnserializerTrait;
use Concrete\Tests\TestCase;

class SafeClassUnserializerTraitTest extends TestCase
{
    public function testValidObjectMatchingAllowedClassIsUnserialized()
    {
        $data = serialize(new SafeUnserializerFixtureAllowedThing('hello'));

        $result = SafeUnserializerFixtureConsumer::unserialize($data, SafeUnserializerFixtureAllowedBase::class);

        $this->assertInstanceOf(SafeUnserializerFixtureAllowedThing::class, $result);
        $this->assertSame('hello', $result->value);
    }

    public function testAllowedBaseClassCanBePassedAsAnArray()
    {
        $data = serialize(new SafeUnserializerFixtureAllowedThing('hello'));

        $result = SafeUnserializerFixtureConsumer::unserialize($data, [
            SafeUnserializerFixtureUnrelatedBase::class,
            SafeUnserializerFixtureAllowedBase::class,
        ]);

        $this->assertInstanceOf(SafeUnserializerFixtureAllowedThing::class, $result);
    }

    public function testSubclassOfAllowedBaseIsAccepted()
    {
        $data = serialize(new SafeUnserializerFixtureAllowedSubThing('nested'));

        $result = SafeUnserializerFixtureConsumer::unserialize($data, SafeUnserializerFixtureAllowedBase::class);

        $this->assertInstanceOf(SafeUnserializerFixtureAllowedSubThing::class, $result);
    }

    public function testObjectNotExtendingAllowedBaseIsRejectedWithoutUnserializing()
    {
        SafeUnserializerFixtureGadget::$triggered = false;
        $data = serialize(new SafeUnserializerFixtureGadget());

        $result = SafeUnserializerFixtureConsumer::unserialize($data, SafeUnserializerFixtureAllowedBase::class);

        $this->assertFalse($result);
        $this->assertFalse(SafeUnserializerFixtureGadget::$triggered, 'The disallowed class should never be instantiated/unserialized');
    }

    public function testNestedPropertiesOfUnrelatedClassesAreStillFullyHydrated()
    {
        $thing = new SafeUnserializerFixtureAllowedThing('outer');
        $thing->nested = new SafeUnserializerFixtureNestedHelper('inner');
        $data = serialize($thing);

        $result = SafeUnserializerFixtureConsumer::unserialize($data, SafeUnserializerFixtureAllowedBase::class);

        $this->assertInstanceOf(SafeUnserializerFixtureAllowedThing::class, $result);
        $this->assertInstanceOf(SafeUnserializerFixtureNestedHelper::class, $result->nested);
        $this->assertSame('inner', $result->nested->value);
    }

    /**
     * @dataProvider provideNonMatchingInput
     */
    public function testNonMatchingOrMalformedInputReturnsFalse($data)
    {
        $result = SafeUnserializerFixtureConsumer::unserialize($data, SafeUnserializerFixtureAllowedBase::class);

        $this->assertFalse($result);
    }

    public function provideNonMatchingInput()
    {
        return [
            'null' => [null],
            'integer' => [123],
            'array' => [['not', 'a', 'string']],
            'empty string' => [''],
            'garbage string' => ['not a serialized value'],
            'serialized scalar' => [serialize('just a string')],
            'serialized array' => [serialize(['a', 'b'])],
            'truncated object header' => ['O:5:"Foo'],
        ];
    }
}

trait SafeUnserializerFixtureTestingTrait
{
    use SafeClassUnserializerTrait;

    public static function unserialize($data, $allowedBaseClasses)
    {
        return static::safeUnserializeObject($data, $allowedBaseClasses);
    }
}

class SafeUnserializerFixtureConsumer
{
    use SafeUnserializerFixtureTestingTrait;
}

abstract class SafeUnserializerFixtureAllowedBase
{
}

abstract class SafeUnserializerFixtureUnrelatedBase
{
}

class SafeUnserializerFixtureAllowedThing extends SafeUnserializerFixtureAllowedBase
{
    public $value;
    public $nested;

    public function __construct($value = null)
    {
        $this->value = $value;
    }
}

class SafeUnserializerFixtureAllowedSubThing extends SafeUnserializerFixtureAllowedThing
{
}

class SafeUnserializerFixtureNestedHelper
{
    public $value;

    public function __construct($value = null)
    {
        $this->value = $value;
    }
}

/**
 * Simulates a gadget-chain class that does NOT extend the allowed base class.
 * If safeUnserializeObject() ever called unserialize() on this, $triggered would flip to true.
 */
class SafeUnserializerFixtureGadget
{
    public static $triggered = false;

    public function __wakeup()
    {
        self::$triggered = true;
    }
}
