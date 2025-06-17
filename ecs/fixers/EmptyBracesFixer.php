<?php

declare(strict_types=1);

namespace Freyr\EventSourcing\Fixers;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

/**
 * Fixer that ensures empty method and class bodies have opening and closing braces on the same line as class/function name.
 */
final class EmptyBracesFixer extends AbstractFixer
{
    public function getName(): string
    {
        return 'Freyr/empty_braces';
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Ensure empty classes and methods use `{}` on the same line as class/function name with no line breaks, one space before `{`, and no space between braces.',
            [],
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_CLASS) || $tokens->isTokenKindFound(T_FUNCTION);
    }


    /**
     * Must run before BracesFixer.
     */
    public function getPriority(): int
    {
        return 40;
    }

    protected function applyFix(SplFileInfo $file, Tokens $tokens): void
    {
        for ($index = 0; $index < $tokens->count(); $index++) {
            if (!$tokens[$index]->isGivenKind([T_FUNCTION, T_CLASS])) {
                continue;
            }

            $braceStart = (int) $tokens->getNextTokenOfKind($index, ['{']);
            $braceEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $braceStart);

            // Check for empty body
            if ($this->isEmptyBody($tokens, $braceStart, $braceEnd)) {
                $this->formatInline($tokens, $braceStart, $braceEnd);
            }
        }
    }



    private function isEmptyBody(Tokens $tokens, int $braceStart, int $braceEnd): bool
    {
        for ($i = $braceStart + 1; $i < $braceEnd; $i++) {
            if (!$tokens[$i]->isWhitespace() && !$tokens[$i]->isComment()) {
                return false;
            }
        }
        return true;
    }

    private function formatInline(Tokens $tokens, int $braceStart, int $braceEnd): void
    {
        // Remove whitespace between declaration and opening brace
        $prevIndex = $braceStart - 1;
        if ($tokens[$prevIndex]->isWhitespace()) {
            $tokens[$prevIndex] = new Token([T_WHITESPACE, ' ']); // Ensure one space
        } else {
            $tokens->insertAt($braceStart, new Token([T_WHITESPACE, ' ']));
        }

        $tokens[$braceStart] = new Token('{');
        $tokens[$braceEnd] = new Token('}');

        // Remove all tokens between { and } (keep empty)
        for ($i = $braceStart + 1; $i < $braceEnd; $i++) {
            $tokens->clearAt($i);
        }
    }
}
