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
     * @param string $data
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
}
