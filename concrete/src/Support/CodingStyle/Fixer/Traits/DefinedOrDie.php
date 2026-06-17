<?php

declare(strict_types=1);

namespace Concrete\Core\Support\CodingStyle\Fixer\Traits;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

defined('C5_EXECUTE') or die('Access Denied.');

trait DefinedOrDie
{
    /**
     * @return \PhpCsFixer\Tokenizer\Token[]
     */
    private function getDefinedOrDieTokens(bool $includeWhiteSpaces): array
    {
        $whiteSpaceToken = $includeWhiteSpaces ? new Token([T_WHITESPACE, ' ']) : null;

        $lineTokens = [
            new Token([T_STRING, 'defined']),
            new Token('('),
            new Token([T_CONSTANT_ENCAPSED_STRING, "'C5_EXECUTE'"]),
            new Token(')'),
            $whiteSpaceToken,
            new Token([T_LOGICAL_OR, 'or']),
            $whiteSpaceToken,
            new Token([T_EXIT, 'die']),
            new Token('('),
            new Token([T_CONSTANT_ENCAPSED_STRING, "'Access Denied.'"]),
            new Token(')'),
            new Token(';'),
        ];

        return array_values(array_filter(
            $lineTokens,
            static function (?Token $token): bool {
                return $token !== null;
            },
        ));
    }

    /**
     * @param int $index The index of the token that may be inside a `defined('C5_EXECUTE') or die('Access Denied.');` line.
     *
     * @return ?array{int, int}
     */
    private function getDefinedOrDieLineTokensAt(Tokens $tokens, int $index): ?array
    {
        $lineTokens = $this->getDefinedOrDieTokens(false);

        $normalizedTokenGetter = static function (int $index) use ($tokens): Token {
            $token = $tokens[$index];
            if ($token->getId() === T_CONSTANT_ENCAPSED_STRING) {
                $content = $token->getContent();
                if ($content !== "'Access Denied.'" && preg_match('/^["\']\s*access\s+denied\s*\.?["\']$/i', $content)) {
                    $token = new Token([T_CONSTANT_ENCAPSED_STRING, "'Access Denied.'"]);
                } elseif (str_starts_with($content, '"')) {
                    $token = new Token([T_CONSTANT_ENCAPSED_STRING, str_replace('"', "'", $content)]);
                }
            }

            return $token;
        };
        $lineTokenSearcher = static function (Token $token) use ($lineTokens): ?int {
            foreach ($lineTokens as $index => $lineToken) {
                if ($lineToken->equals($token, true)) {
                    return $index;
                }
            }

            return null;
        };

        $startIndex = null;
        while ($index >= 0) {
            $token = $normalizedTokenGetter($index);
            if (!$token->isWhitespace()) {
                $lineTokenIndex = $lineTokenSearcher($token);
                if ($lineTokenIndex === null) {
                    return null;
                }
                if ($lineTokenIndex === 0) {
                    $startIndex = $index;
                    break;
                }
                $index = min($index, $index - $lineTokenIndex + 1);
            }
            $index = $tokens->getPrevNonWhitespace($index) ?? -1;
        }
        if ($startIndex === null) {
            return null;
        }
        $index = $startIndex;
        $expectedLineTokensIndex = 1;
        while (true) {
            if (!isset($lineTokens[$expectedLineTokensIndex])) {
                return [$startIndex, $index];
            }
            $index = $tokens->getNextNonWhitespace($index);
            if ($index === null) {
                return null;
            }
            $token = $normalizedTokenGetter($index);
            if (!$lineTokens[$expectedLineTokensIndex]->equals($token, true)) {
                return null;
            }
            $expectedLineTokensIndex++;
        }
    }

    private function isTokenInDefinedOrDieLine(Tokens $tokens, int $index): bool
    {
        return $this->getDefinedOrDieLineTokensAt($tokens, $index) !== null;
    }
}
