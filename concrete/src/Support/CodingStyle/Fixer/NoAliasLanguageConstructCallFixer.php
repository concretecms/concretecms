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

require_once __DIR__ . '/Traits/DefinedOrDie.php';

final class NoAliasLanguageConstructCallFixer extends AbstractFixer
{
    use DefinedOrDie;

    /**
     * @var \PhpCsFixer\Fixer\Alias\NoAliasLanguageConstructCallFixer
     */
    private $actualFixer;

    /**
     * {@inheritdoc}
     *
     * @see \PhpCsFixer\Fixer\FixerInterface::getName()
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
        return new FixerDefinition('Use exit instead of die (except in "defined or die" ConcreteCMS lines).', []);
    }

    /**
     * {@inheritdoc}
     *
     * @see \PhpCsFixer\Fixer\FixerInterface::isCandidate()
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_EXIT);
    }

    /**
     * {@inheritdoc}
     *
     * @see \PhpCsFixer\AbstractFixer::applyFix()
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(T_EXIT)
                && strcasecmp($token->getContent(), 'exit') !== 0
                && !$this->isTokenInDefinedOrDieLine($tokens, $index)
            ) {
                $tokens[$index] = new Token([T_EXIT, 'exit']);
            }
        }
    }
}
