<?php

declare(strict_types=1);

namespace Freyr\EventSourcing\Fixers;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Converts empty function/class bodies to have both curly braces on the same line.
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
            'Empty function bodies and classes should have opening and closing curly braces on the same line.',
            [
                new CodeSample(
                    '<?php
class EmptyClass
{
}

function emptyFunction()
{
}
'
                ),
                new CodeSample(
                    '<?php
class EmptyClass
{
}

function emptyFunction() {
}
'
                ),
                new CodeSample(
                    '<?php
class EmptyClass {}

function emptyFunction() {}
'
                ),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_FUNCTION) || $tokens->isTokenKindFound(T_CLASS);
    }

    /**
     * Must run before BracesFixer and after ClassAttributesSeparationFixer.
     */
    public function getPriority(): int
    {
        return 40;
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        // Fix empty functions
        for ($index = $tokens->count() - 1; $index > 0; --$index) {
            if (!$tokens[$index]->isGivenKind(T_FUNCTION)) {
                continue;
            }

            $openBraceIndex = $tokens->getNextTokenOfKind($index, ['{']);
            if (null === $openBraceIndex) {
                continue;
            }

            $closeBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $openBraceIndex);
            if (null === $closeBraceIndex) {
                continue;
            }

            // Check if this is an empty function body (only whitespace between braces)
            $isEmpty = true;
            for ($i = $openBraceIndex + 1; $i < $closeBraceIndex; ++$i) {
                if (!$tokens[$i]->isWhitespace() && !$tokens[$i]->isComment()) {
                    $isEmpty = false;
                    break;
                }
            }

            if (!$isEmpty) {
                continue;
            }

            // Fix empty function formatting
            $this->fixEmptyBody($tokens, $openBraceIndex, $closeBraceIndex);
        }

        // Fix empty classes
        for ($index = $tokens->count() - 1; $index > 0; --$index) {
            if (!$tokens[$index]->isGivenKind(T_CLASS)) {
                continue;
            }

            $openBraceIndex = $tokens->getNextTokenOfKind($index, ['{']);
            if (null === $openBraceIndex) {
                continue;
            }

            $closeBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $openBraceIndex);
            if (null === $closeBraceIndex) {
                continue;
            }

            // Check if this is an empty class (only whitespace between braces)
            $isEmpty = true;
            for ($i = $openBraceIndex + 1; $i < $closeBraceIndex; ++$i) {
                if (!$tokens[$i]->isWhitespace() && !$tokens[$i]->isComment()) {
                    $isEmpty = false;
                    break;
                }
            }

            if (!$isEmpty) {
                continue;
            }

            // Fix empty class formatting
            $this->fixEmptyBody($tokens, $openBraceIndex, $closeBraceIndex);
        }
    }

    /**
     * Fixes the formatting of empty function/class bodies.
     */
    private function fixEmptyBody(Tokens $tokens, int $openBraceIndex, int $closeBraceIndex): void
    {
        // Get the token before the opening brace
        $prevMeaningfulIndex = $tokens->getPrevMeaningfulToken($openBraceIndex);

        // Find the token before the open brace
        $beforeOpenBraceIndex = $openBraceIndex - 1;
        
        // Ensure there's a single space before the opening brace if needed
        if ($tokens[$prevMeaningfulIndex]->getContent() !== ')' &&
            $tokens[$beforeOpenBraceIndex]->isWhitespace()) {
            $tokens[$beforeOpenBraceIndex] = new Token([T_WHITESPACE, ' ']);
        }
        
        // Detect if we have a specific pattern where { is at the end of a line and } is on a new line
        $hasNewlineBetweenBraces = false;
        $betweenBraces = '';
        
        // Collect all content between braces
        for ($i = $openBraceIndex + 1; $i < $closeBraceIndex; ++$i) {
            if ($tokens[$i]->isWhitespace()) {
                $betweenBraces .= $tokens[$i]->getContent();
            }
        }
        
        // Check if there's a newline between braces
        if (strpos($betweenBraces, "\n") !== false) {
            $hasNewlineBetweenBraces = true;
        }
        
        // Remove tokens between braces
        for ($i = $openBraceIndex + 1; $i < $closeBraceIndex; ++$i) {
            $tokens->clearAt($i);
        }
        
        // Special handling for case where function name and opening brace are on the same line,
        // but closing brace is on a new line
        if ($hasNewlineBetweenBraces) {
            // Place a token with empty content between braces to ensure they appear together {}
            $tokens[$openBraceIndex + 1] = new Token([T_WHITESPACE, '']);
        }
    }
}
