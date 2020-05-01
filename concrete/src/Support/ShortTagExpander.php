<?php

namespace Concrete\Core\Support;

use InvalidArgumentException;

class ShortTagExpander
{
    /**
     * Replace the short PHP open tags to long tags (`<?` to `<?php`) and optionally the short echo tags (`<?=` to `<?php echo `).
     *
     * @param string $code the code to be expanded
     * @param bool $expandShortEcho expand
     *
     * @throws \InvalidArgumentException if $code is not a string
     *
     * @return string|null return NULL if code didn't changed, the expanded code otherwise
     */
    public function expandCode($code, $expandShortEcho)
    {
        if (!is_string($code)) {
            throw new InvalidArgumentException();
        }
        $result = '';
        $tokens = token_get_all($code);
        $numTokens = count($tokens);
        $translationMap = [
            T_OPEN_TAG => '<?php',
        ];
        if ($expandShortEcho) {
            $translationMap = [
                T_OPEN_TAG_WITH_ECHO => '<?php echo',
            ];
        }
        $changed = false;
        $matches = null;
        foreach ($tokens as $tokenIndex => $token) {
            if (!is_array($token)) {
                $result .= $token;
                continue;
            }
            if (!isset($translationMap[$token[0]])) {
                $result .= $token[1];
                continue;
            }
            $changed = true;
            $expanded = $translationMap[$token[0]];
            $result .= $expanded;
            // Let's see if we have to add some white space after the expanded token
            if (preg_match('/(\s+)$/', $token[1], $matches)) {
                $result .= $matches[1];
            } elseif ($tokenIndex < $numTokens - 1) {
                $nextToken = $tokens[$tokenIndex + 1];
                if (!is_array($nextToken) || $nextToken[0] !== T_WHITESPACE) {
                    $result .= ' ';
                }
            }
        }

        return $changed ? $result : null;
    }
}
