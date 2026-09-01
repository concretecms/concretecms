<?php

declare(strict_types=1);

namespace Concrete\Core\Api\Block;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Turn the value of a block received via the API into what the block types are given by their own form.
 *
 * JSON has booleans, the block types don't: they store their flags as 0 and 1, and they read them back as
 * the strings that their record holds. Whoever writes a value can therefore use true and false, and this is
 * where they become the 1 and the 0 that every block type understands.
 */
class ApiValueNormalizer
{
    /**
     * @param array<string,mixed> $value
     *
     * @return array<string,mixed>
     */
    public function normalize(array $value): array
    {
        foreach ($value as $key => $item) {
            if (is_bool($item)) {
                $value[$key] = $item ? '1' : '0';
            } elseif (is_array($item)) {
                $value[$key] = $this->normalize($item);
            }
        }

        return $value;
    }
}
