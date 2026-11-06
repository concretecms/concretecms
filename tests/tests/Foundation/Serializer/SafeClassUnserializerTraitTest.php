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

    public function testListOfAllowedObjectsIsUnserialized()
    {
        $data = serialize([
            'first' => new SafeUnserializerFixtureAllowedThing('one'),
            'second' => new SafeUnserializerFixtureAllowedSubThing('two'),
        ]);

        $result = SafeUnserializerFixtureConsumer::unserializeList($data, SafeUnserializerFixtureAllowedBase::class);

        $this->assertSame(['first', 'second'], array_keys($result));
        $this->assertSame('one', $result['first']->value);
        $this->assertInstanceOf(SafeUnserializerFixtureAllowedSubThing::class, $result['second']);
    }

    public function testObjectsOfTheListAreFoundHoweverDeepTheyAre()
    {
        $data = serialize(['test' => [new SafeUnserializerFixtureAllowedThing('deep')]]);

        $result = SafeUnserializerFixtureConsumer::unserializeList($data, SafeUnserializerFixtureAllowedBase::class);

        $this->assertInstanceOf(SafeUnserializerFixtureAllowedThing::class, $result['test'][0]);
        $this->assertSame('deep', $result['test'][0]->value);
    }

    public function testWhatTheListHoldsBesidesObjectsIsKept()
    {
        $data = serialize(['just a string', 42, null, new SafeUnserializerFixtureAllowedThing('kept')]);

        $result = SafeUnserializerFixtureConsumer::unserializeList($data, SafeUnserializerFixtureAllowedBase::class);

        $this->assertSame([0, 1, 2, 3], array_keys($result));
        $this->assertSame(42, $result[1]);
    }

    public function testObjectNotExtendingAllowedBaseRefusesTheWholeListWithoutUnserializing()
    {
        SafeUnserializerFixtureGadget::$triggered = false;
        $data = serialize([new SafeUnserializerFixtureAllowedThing('kept'), new SafeUnserializerFixtureGadget()]);

        $result = SafeUnserializerFixtureConsumer::unserializeList($data, SafeUnserializerFixtureAllowedBase::class);

        $this->assertNull($result);
        $this->assertFalse(SafeUnserializerFixtureGadget::$triggered, 'The disallowed class should never be instantiated/unserialized');
    }

    public function testObjectNotExtendingAllowedBaseIsFoundHoweverDeepItIs()
    {
        SafeUnserializerFixtureGadget::$triggered = false;
        $thing = new SafeUnserializerFixtureAllowedThing('outer');
        $thing->nested = ['deeper' => new SafeUnserializerFixtureGadget()];
        $data = serialize(['test' => [$thing]]);

        $result = SafeUnserializerFixtureConsumer::unserializeList($data, SafeUnserializerFixtureAllowedBase::class);

        $this->assertNull($result);
        $this->assertFalse(SafeUnserializerFixtureGadget::$triggered, 'The disallowed class should never be instantiated/unserialized');
    }

    public function testObjectNotExtendingAllowedBaseIsFoundInsideAPrivateProperty()
    {
        SafeUnserializerFixtureGadget::$triggered = false;
        $data = serialize([new SafeUnserializerFixtureSecretiveThing(new SafeUnserializerFixtureGadget())]);

        $result = SafeUnserializerFixtureConsumer::unserializeList($data, SafeUnserializerFixtureAllowedBase::class);

        $this->assertNull($result);
        $this->assertFalse(SafeUnserializerFixtureGadget::$triggered, 'The disallowed class should never be instantiated/unserialized');
    }

    public function testAnObjectHoldingItselfIsWalkedThroughJustOnce()
    {
        $thing = new SafeUnserializerFixtureAllowedThing('itself');
        $thing->nested = $thing;
        $data = serialize([$thing]);

        $result = SafeUnserializerFixtureConsumer::unserializeList($data, SafeUnserializerFixtureAllowedBase::class);

        $this->assertInstanceOf(SafeUnserializerFixtureAllowedThing::class, $result[0]);
        $this->assertSame($result[0], $result[0]->nested);
    }

    /**
     * @dataProvider provideNonListInput
     */
    public function testInputThatIsNoListReturnsNull($data)
    {
        $result = SafeUnserializerFixtureConsumer::unserializeList($data, SafeUnserializerFixtureAllowedBase::class);

        $this->assertNull($result);
    }

    public static function provideNonListInput()
    {
        return [
            'null' => [null],
            'empty string' => [''],
            'garbage string' => ['not a serialized value'],
            'serialized scalar' => [serialize('just a string')],
            'serialized object' => [serialize(new SafeUnserializerFixtureAllowedThing('alone'))],
            'truncated array header' => ['a:1:'],
        ];
    }

    /**
     * @dataProvider provideNonMatchingInput
     */
    public function testNonMatchingOrMalformedInputReturnsFalse($data)
    {
        $result = SafeUnserializerFixtureConsumer::unserialize($data, SafeUnserializerFixtureAllowedBase::class);

        $this->assertFalse($result);
    }

    public static function provideNonMatchingInput()
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

    public static function unserializeList($data, $allowedBaseClasses)
    {
        return static::safeUnserializeObjectArray($data, $allowedBaseClasses);
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

class SafeUnserializerFixtureSecretiveThing extends SafeUnserializerFixtureAllowedBase
{
    private $secret;

    public function __construct($secret = null)
    {
        $this->secret = $secret;
    }
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
