<?php

declare(strict_types=1);

namespace Concrete\Core\Support\CodingStyle\Fixer;

use Concrete\Core\Support\CodingStyle\Fixer\Traits\DefinedOrDie;
use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

defined('C5_EXECUTE') or die('Access Denied.');

final class LogicalOperatorsFixer extends AbstractFixer
{
    use DefinedOrDie;

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
        return new FixerDefinition('Replace `and`/`or` with `&&`/`||` (except in "defined or die" ConcreteCMS lines).', []);
    }

    /**
     * {@inheritdoc}
     *
     * @see \PhpCsFixer\Fixer\FixerInterface::isCandidate()
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([\T_LOGICAL_AND, \T_LOGICAL_OR]);
    }

    /**
     * {@inheritdoc}
     *
     * @see \PhpCsFixer\Fixer\FixerInterface::isRisky()
     * @see \PhpCsFixer\AbstractFixer::isRisky()
     */
    public function isRisky(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @see \PhpCsFixer\AbstractFixer::applyFix()
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(T_LOGICAL_AND)) {
                $tokens[$index] = new Token([T_BOOLEAN_AND, '&&']);
            } elseif ($token->isGivenKind(\T_LOGICAL_OR) && !$this->isTokenInDefinedOrDieLine($tokens, $index)) {
                $tokens[$index] = new Token([\T_BOOLEAN_OR, '||']);
            }
        }
    }
}
