<?php

declare(strict_types=1);

namespace Concrete\Core\Support\CodingStyle\Fixer;

use Concrete\Core\Support\CodingStyle\Fixer\Traits\DefinedOrDie;
use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\ConfigurableFixerTrait;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

defined('C5_EXECUTE') or die('Access Denied.');

final class EnsureDefinedOrDieFixer extends AbstractFixer implements ConfigurableFixerInterface, WhitespacesAwareFixerInterface
{
    use DefinedOrDie;
    use ConfigurableFixerTrait;

    /**
     * {@inheritdoc}
     *
     * @see \PhpCsFixer\AbstractFixer::getName()
     */
    public function getName(): string
    {
        return 'ConcreteCMS/' . parent::getName();
    }

    /**
     * {@inheritdoc}
     *
     * @see \PhpCsFixer\Fixer\FixerInterface::getDefinition()
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('Ensure that the files have the `defined(\'C5_EXECUTE\') or die("\'Access Denied.\'")` line.', []);
    }

    /**
     * {@inheritdoc}
     *
     * @see \PhpCsFixer\Fixer\FixerInterface::isCandidate()
     */
    public function isCandidate(Tokens $tokens): bool
    {
        $firstToken = $tokens[0] ?? null;

        return $firstToken !== null && $firstToken->isGivenKind(T_OPEN_TAG);
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('normalize', 'Whether to normalize existing `defined(\'C5_EXECUTE\') or die("\'Access Denied.\'")` lines.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(true)
                ->getOption(),
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @see \PhpCsFixer\AbstractFixer::applyFix()
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $existingRanges = $this->getExistingRanges($tokens);
        if ($existingRanges === []) {
            $this->insertNewRange($tokens);
        } elseif ($this->configuration['normalize']) {
            while (($range = array_pop($existingRanges)) !== null) {
                $this->normalizeRange($tokens, $range[0], $range[1]);
            }
        }
    }

    private function getExistingRanges(Tokens $tokens): array
    {
        $ranges = [];

        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(T_EXIT)) {
                $range = $this->getDefinedOrDieLineTokensAt($tokens, $index);
                if ($range !== null) {
                    $ranges[] = $range;
                }
            }
        }

        return $ranges;
    }

    private function normalizeRange(Tokens $tokens, int $start, int $end): void
    {
        $normalizedTokens = $this->getDefinedOrDieTokens(true);
        if (($end - $start + 1) === count($normalizedTokens)) {
            for ($index = $start; $index <= $end; $index++) {
                if (!$tokens[$index]->equals($normalizedTokens[$index - $start], true)) {
                    $tokens[$index] = $normalizedTokens[$index - $start];
                }
            }
        } else {
            $tokens->overrideRange($start, $end, $normalizedTokens);
        }
    }

    private function insertNewRange(Tokens $tokens): void
    {
        $after = 0;
        foreach ([T_DECLARE, T_NAMESPACE, T_USE] as $tokenKind) {
            $after = $this->skipAfters($tokens, $after, $tokenKind);
            if ($after === null) {
                return;
            }
        }
        $newTokens = $this->getDefinedOrDieTokens(true);
        $beforeNewLines = $this->countBorderNewLinesIn($tokens[$after], true);
        $lineEnding = $this->whitespacesConfig->getLineEnding();
        if ($beforeNewLines < 2) {
            array_unshift($newTokens, new Token([T_WHITESPACE, str_repeat($lineEnding, 2 - $beforeNewLines)]));
        }
        $nextToken = $tokens[$after + 1] ?? null;
        if ($nextToken === null) {
            $newTokens[] = new Token([T_WHITESPACE, $lineEnding]);
        } else {
            $afterNewLines = $this->countBorderNewLinesIn($nextToken, false);
            if ($afterNewLines === 0) {
                $newTokens[] = new Token([T_WHITESPACE, $lineEnding . $lineEnding]);
            } elseif ($afterNewLines === 1) {
                $newTokens[] = new Token([T_WHITESPACE, $lineEnding]);
            }
        }
        $tokens->insertAt($after + 1, $newTokens);
    }

    private function countBorderNewLinesIn(Token $token, bool $trailing): int
    {
        $content = str_replace("\r", "\n", str_replace("\r\n", "\n", $token->getContent()));

        return strlen($content) - strlen(rtrim($content, "\n"));
    }

    private function skipAfters(Tokens $tokens, int $after, int $tokenKind): ?int
    {
        while (true) {
            $newAfter = $this->skipAfter($tokens, $after, $tokenKind);
            if ($newAfter === null || $newAfter === $after) {
                return $newAfter;
            }
            $after = $newAfter;
        }
    }

    private function skipAfter(Tokens $tokens, int $after, int $tokenKind): ?int
    {
        while (true) {
            $index = $tokens->getNextMeaningfulToken($after);
            if ($index === null) {
                return $after;
            }
            $token = $tokens[$index];
            if (!$token->isGivenKind($tokenKind)) {
                return $after;
            }
            while (true) {
                $index = $tokens->getNextMeaningfulToken($index);
                if ($index === null) {
                    return null;
                }
                $token = $tokens->offsetGet($index);
                if ($token->getId() === null) {
                    if ($token->getContent() === ';') {
                        return $index;
                    }
                } elseif (in_array($token->getId(), [T_CLOSE_TAG], true)) {
                    return null;
                }
            }
        }
    }
}
