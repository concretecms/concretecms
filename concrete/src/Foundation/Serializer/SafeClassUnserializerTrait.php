<?php

namespace Concrete\Core\Foundation\Serializer;

/**
 * Provides a way to safely unserialize a string that is expected to contain a single object,
 * without risking PHP object injection when the underlying data (e.g. a database column) may
 * have been tampered with.
 *
 * Since the set of classes that may legitimately appear nested inside the encoded object graph
 * isn't known ahead of time (and restricting `unserialize()`'s `allowed_classes` option to just
 * the outermost class would break unserialization of any object holding other objects as
 * properties), this only validates the *outermost* class name - extracted from the leading
 * `O:<len>:"<class>"` header of the serialized string via regex - against $allowedBaseClasses
 * before calling the unrestricted `unserialize()`. This still blocks the entry point that
 * PHP object injection / gadget-chain attacks rely on, since the attacker-controlled payload
 * must be an instance of one of $allowedBaseClasses to be unserialized at all.
 */
trait SafeClassUnserializerTrait
{
    /**
     * Unserializes $data, only allowing instantiation of the encoded object's class if it
     * extends/implements one of $allowedBaseClasses.
     *
     * @param string|mixed $data false is returned if it's not a string
     * @param string|string[] $allowedBaseClasses one or more base classes/interfaces that the
     *                                             encoded object's class must extend/implement
     *
     * @return mixed|false the unserialized value, or false if $data isn't a string encoding an
     *                      object whose class extends/implements one of $allowedBaseClasses
     */
    protected static function safeUnserializeObject($data, $allowedBaseClasses)
    {
        if (!is_string($data) || !preg_match('/^O:\d+:"(.+?)"/', $data, $matches)) {
            return false;
        }
        $class = $matches[1];
        foreach ((array) $allowedBaseClasses as $baseClass) {
            if (is_a($class, $baseClass, true)) {
                return unserialize($data);
            }
        }

        return false;
    }

    /**
     * Unserializes $data, which is expected to encode an array, only allowing instantiation of the
     * objects it holds if their class extends/implements one of $allowedBaseClasses.
     *
     * An array has no outermost class to check, so this goes the other way around: the classes
     * named by the serialized string are looked up, and `unserialize()` is allowed to build just
     * the ones that extend/implement one of $allowedBaseClasses. Should the string name any other
     * class, that object comes back incomplete - never instantiated, never woken up - and the whole
     * array is refused, however deep it was. That makes this stricter than safeUnserializeObject(),
     * which checks the outermost class and lets it hold whatever it holds.
     *
     * @param string|string[] $allowedBaseClasses one or more base classes/interfaces that the
     *                                             encoded objects must extend/implement
     *
     * @return array|null the array, or NULL if $data doesn't encode one or it holds an object that
     *                     isn't allowed
     */
    protected static function safeUnserializeObjectArray(?string $data, $allowedBaseClasses): ?array
    {
        if ($data === null || !preg_match('/^a:\d+:\{/', $data)) {
            return null;
        }
        $allowedBaseClasses = (array) $allowedBaseClasses;
        $allowedClasses = [];
        if (preg_match_all('/[OC]:\d+:"(.+?)"/', $data, $matches)) {
            foreach (array_unique($matches[1]) as $class) {
                foreach ($allowedBaseClasses as $baseClass) {
                    if (is_a($class, $baseClass, true)) {
                        $allowedClasses[] = $class;
                        break;
                    }
                }
            }
        }
        $unserialized = unserialize($data, ['allowed_classes' => $allowedClasses]);
        if (!is_array($unserialized)) {
            return null;
        }

        return self::holdsRefusedObject($unserialized, new \SplObjectStorage()) ? null : $unserialized;
    }

    /**
     * Does a value hold, at any depth, an object that `unserialize()` was not allowed to build?
     *
     * Only the properties of an object are walked through, which is all `unserialize()` can fill:
     * an allowed class keeping other objects in its internal storage instead (SplObjectStorage and
     * the like) would hide them from here.
     *
     * @param mixed $value
     * @param \SplObjectStorage $inspected the objects already walked through, which keeps the
     *                                      references an object graph may hold to itself from
     *                                      being walked forever
     */
    private static function holdsRefusedObject($value, \SplObjectStorage $inspected): bool
    {
        if (is_object($value)) {
            if ($value instanceof \__PHP_Incomplete_Class) {
                return true;
            }
            if ($inspected->contains($value)) {
                return false;
            }
            $inspected->attach($value);
            // the properties of an object, whatever their visibility
            $value = (array) $value;
        }
        if (is_array($value)) {
            foreach ($value as $item) {
                if (self::holdsRefusedObject($item, $inspected)) {
                    return true;
                }
            }
        }

        return false;
    }
}
