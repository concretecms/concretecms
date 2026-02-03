<?php

declare(strict_types=1);

namespace Concrete\Core\File\Tracker;

class RichTextExtractor
{
    /**
     * The regular expression that identifies a valid UUID.
     * 
     * @var string
     */
    protected const RX_UUID = '(?<uuid>[0-9a-fA-F]{8}(?:-[0-9a-fA-F]{4}){3}-[0-9a-fA-F]{12})';
    
    /**
     * The regular expression that identifies a valid ID (integer).
     * We assume 19 digits at max (the greatest signed 64-bit integer is 2^64 - 1 = 9,223,372,036,854,775,807)
     * 
     * @var string
     */
    protected const RX_ID = '(?<id>[1-9][0-9]{0,18})';

    protected const RX_UUID_OR_ID = '(?:' . self::RX_UUID . '|' . self::RX_ID . ')';

    /**
     * Extract the file UUIDs and the files IDs from a rich text.
     *
     * @param string|null $richText
     *
     * @return int[]|string[]
     */
    public function extractFiles($richText): array
    {
        $richText = (string) $richText;
        if ($richText === '') {
            return [];
        }

        return array_merge(
            $this->extractImages($richText),
            $this->extractDownloads($richText)
        );
    }

    /**
     * @return int[]|string[]
     */
    protected function extractImages(string $richText): array
    {
        return $this->extractWithRegex(
            $richText,
            '/\<concrete-picture[^>]*?\bfID\s*=\s*[\'"]' . static::RX_UUID_OR_ID . '[\'"]/i'
        );
    }
    
    protected function extractDownloads(string $richText): array
    {
        return $this->extractWithRegex(
            $richText,
            '/\bFID_DL_' . static::RX_UUID_OR_ID . '\b/'
        );
    }

    private function extractWithRegex(string $richText, string $regex): array
    {
        $matches = null;
        if (!preg_match_all($regex, $richText, $matches)) {
            return [];
        }

        return array_merge(
            array_map('strtolower', array_filter($matches['uuid'])),
            array_map('intval', array_filter($matches['id']))
        );
    }
}
