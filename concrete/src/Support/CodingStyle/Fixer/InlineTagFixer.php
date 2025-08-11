<?php

declare(strict_types=1);

namespace Concrete\Core\Support\CodingStyle\Fixer;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class InlineTagFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface
{
    public const OPTION_SPACEBEFORE = 'space_before';

    public const OPTION_SPACEAFTER = 'space_after';

    public const OPTION_SEMICOLON = 'semicolon';

    public const SPACE_MINIMUM = 'minimum';

    public const SPACE_ONE = 'one';

    public const SPACE_KEEP = 'keep';

    private const SUPPORTED_SPACEBEFORE_OPTIONS = [
        self::SPACE_MINIMUM,
        self::SPACE_ONE,
        self::SPACE_KEEP,
    ];

    private const SUPPORTED_SPACEAFTER_OPTIONS = [
        self::SPACE_MINIMUM,
        self::SPACE_ONE,
        self::SPACE_KEEP,
    ];

    /**
     * {@inheritdoc}
     *
     * @see \PhpCsFixer\AbstractFixer::getName()
     */
    public function getName()
    {
        return 'ConcreteCMS/' . parent::getName();
    }

    /**
     * {@inheritdoc}
     *
     * @see \PhpCsFixer\Fixer\DefinedFixerInterface::getDefinition()
     */
    public function getDefinition()
    {
        $sample = <<<'EOT'
<?=1?> <?= 1 ?> <?=  1  ?>
<?=2;?> <?= 2; ?> <?=  2;  ?>
<?=3  ;?> <?= 3  ;?> <?=3  ;?> <?=  3  ;  ?>
<?php   echo 4  ;  ?>

EOT
        ;

        return new FixerDefinition(
            'Changes spaces and semicolons in inline PHP tags.',
            [
                new CodeSample($sample),
                new CodeSample($sample, [self::OPTION_SPACEBEFORE => self::SPACE_KEEP]),
                new CodeSample($sample, [self::OPTION_SPACEBEFORE => self::SPACE_MINIMUM]),
                new CodeSample($sample, [self::OPTION_SPACEAFTER => self::SPACE_KEEP]),
                new CodeSample($sample, [self::OPTION_SPACEAFTER => self::SPACE_MINIMUM]),
                new CodeSample($sample, [self::OPTION_SEMICOLON => null]),
                new CodeSample($sample, [self::OPTION_SEMICOLON => true]),
            ],
            null
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see \PhpCsFixer\Fixer\FixerInterface::isCandidate()
     */
    public function isCandidate(Tokens $tokens)
    {
        if (
            $this->configuration[self::OPTION_SPACEBEFORE] === self::SPACE_KEEP
            && $this->configuration[self::OPTION_SPACEAFTER] === self::SPACE_KEEP
            && $this->configuration[self::OPTION_SEMICOLON] === null
        ) {
            return false;
        }

        return $this->findApplicableRange($tokens) !== null;
    }

    /**
     * {@inheritdoc}
     *
     * @see \PhpCsFixer\AbstractFixer::createConfigurationDefinition()
     */
    protected function createConfigurationDefinition()
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder(self::OPTION_SPACEBEFORE, 'The desired number of spaces at the beginning of the tag.'))
                ->setAllowedValues(self::SUPPORTED_SPACEBEFORE_OPTIONS)
                ->setDefault(self::SPACE_ONE)
                ->getOption(),
            (new FixerOptionBuilder(self::OPTION_SPACEAFTER, 'The desired number of spaces at the end of the tag.'))
                ->setAllowedValues(self::SUPPORTED_SPACEAFTER_OPTIONS)
                ->setDefault(self::SPACE_ONE)
                ->getOption(),
            (new FixerOptionBuilder(self::OPTION_SEMICOLON, 'Whether there should be a semi-colon at the end.'))
                ->setAllowedTypes(['bool', 'null'])
                ->setDefault(false)
                ->getOption(),
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @see \PhpCsFixer\AbstractFixer::applyFix()
     */
    protected function applyFix(SplFileInfo $file, Tokens $tokens)
    {
        $index = 0;
        for (;;) {
            $range = $this->findApplicableRange($tokens, $index);
            if ($range === null) {
                return;
            }
            $newRange = $this->fixRange($range);
            $tokens->overrideRange($index, $index + count($range) - 1, $newRange);
            $index += count($newRange);
        }
    }

    /**
     * Find the next range of tokens that should be fixes.
     *
     * @return \PhpCsFixer\Tokenizer\Token[]|null
     */
    private function findApplicableRange(Tokens $tokens, int &$start = 0): ?array
    {
        $maxIndex = $tokens->count() - 1;
        for (; $start < $maxIndex; $start++) {
            $token = $tokens[$start];
            /** @var \PhpCsFixer\Tokenizer\Token $token */
            if (!$token->isGivenKind([T_OPEN_TAG, T_OPEN_TAG_WITH_ECHO])) {
                continue;
            }
            if (strpos($token->getContent(), "\n") !== false) {
                continue;
            }
            $result = [$token];
            for ($nextIndex = $start + 1; $nextIndex <= $maxIndex; $nextIndex++) {
                $token = $tokens[$nextIndex];
                /** @var \PhpCsFixer\Tokenizer\Token $token */
                if ($token->isGivenKind(T_CLOSE_TAG)) {
                    $result[] = $token;

                    return $result;
                }
                if (strpos($token->getContent(), "\n") !== false) {
                    break;
                }
                $result[] = $token;
            }
            $start = $nextIndex;
        }

        return null;
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Token[] $tokens
     *
     * @return \PhpCsFixer\Tokenizer\Token[]
     */
    private function fixRange(array $tokens): array
    {
        $start = $this->fixStart($tokens);
        $end = $this->fixEnd($tokens);

        return array_merge($start, $tokens, $end);
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Token[] $tokens
     *
     * @return \PhpCsFixer\Tokenizer\Token[]
     */
    private function fixStart(array &$tokens): array
    {
        $token = array_shift($tokens);
        $spaceBefore = $this->configuration[self::OPTION_SPACEBEFORE];
        if ($spaceBefore === self::SPACE_KEEP) {
            return [$token];
        }
        // Let's remove the extra whitespaces
        while ($tokens[0]->isWhitespace()) {
            array_shift($tokens);
        }
        if ($token->getId() !== T_OPEN_TAG_WITH_ECHO) {
            return [new Token([$token->getId(), rtrim($token->getContent()) . ' '])];
        }
        $result = [new Token([T_OPEN_TAG_WITH_ECHO, '<?='])];
        if ($spaceBefore === self::SPACE_ONE) {
            $result[] = new Token([T_WHITESPACE, ' ']);
        }

        return $result;
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Token[] $tokens
     *
     * @return \PhpCsFixer\Tokenizer\Token[]
     */
    private function fixEnd(array &$tokens): array
    {
        $spaceAfter = $this->configuration[self::OPTION_SPACEAFTER];
        $semicolon = $this->configuration[self::OPTION_SEMICOLON];
        if ($spaceAfter === self::SPACE_KEEP && $semicolon === null) {
            return [];
        }
        $token = array_pop($tokens);
        if ($spaceAfter === self::SPACE_KEEP) {
            $closing = [$token];
        } else {
            $closing = [new Token([$token->getId(), ltrim($token->getContent())])];
            if ($spaceAfter === self::SPACE_ONE) {
                array_unshift($closing, new Token([T_WHITESPACE, ' ']));
            }
        }
        $spacesAfterSemicolons = $this->popWhitespacesAndSemicolons($tokens, false, true);
        $semicolons = $this->popWhitespacesAndSemicolons($tokens, true, false);
        $spacesBeforeSemicolons = $this->popWhitespacesAndSemicolons($tokens, false, true);
        if ($tokens === [] && $this->configuration[self::OPTION_SPACEBEFORE] === self::SPACE_KEEP) {
            $tokens = $spacesBeforeSemicolons;
            $spacesBeforeSemicolons = [];
        }
        if ($spaceAfter !== self::SPACE_KEEP) {
            $spacesBeforeSemicolons = [];
            $spacesAfterSemicolons = [];
        }
        if ($semicolon === false) {
            $semicolons = [];
        } elseif ($semicolon === true) {
            $semicolons = $this->needsSemicolonAfter($tokens) ? [new Token(';')] : [];
        }

        return array_merge($spacesBeforeSemicolons, $semicolons, $spacesAfterSemicolons, $closing);
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Token[] $tokens
     *
     * @return \PhpCsFixer\Tokenizer\Token[]
     */
    private function popWhitespacesAndSemicolons(array &$tokens, bool $semicolons, bool $whitespaces): array
    {
        $result = [];
        for (;;) {
            $token = array_pop($tokens);
            if ($token === null) {
                break;
            }
            if ($semicolons && $token->getContent() === ';' || $whitespaces && $token->isWhitespace()) {
                array_unshift($result, $token);

                continue;
            }
            $tokens[] = $token;

            break;
        }

        return $result;
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Token[] $tokens
     */
    private function needsSemicolonAfter(array $tokens): bool
    {
        $count = count($tokens);
        if ($count === 0) {
            return false;
        }
        $token = $tokens[$count - 1];
        if ($token->getContent() === '}') {
            return false;
        }

        return true;
    }
}
