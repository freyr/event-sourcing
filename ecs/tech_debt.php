<?php

declare(strict_types=1);

use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\SuperfluousWhitespaceSniff;
use PhpCsFixer\Fixer\Phpdoc\NoEmptyPhpdocFixer;
use PhpCsFixer\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitDataProviderStaticFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->rule(NoEmptyPhpdocFixer::class);
    $ecsConfig->ruleWithConfiguration(
        PhpUnitDataProviderStaticFixer::class,
        ['force' => true]
    );
    $ecsConfig->ruleWithConfiguration(
        NoSuperfluousPhpdocTagsFixer::class,
        [
            'remove_inheritdoc' => true,
            'allow_mixed' => true,
        ]
    );
    $ecsConfig->ruleWithConfiguration(SuperfluousWhitespaceSniff::class, [
        'ignoreBlankLines' => false,
    ]);
};
