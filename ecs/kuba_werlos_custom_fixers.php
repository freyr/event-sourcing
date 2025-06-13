<?php

declare(strict_types=1);

use PhpCsFixerCustomFixers\Fixer\MultilineCommentOpeningClosingAloneFixer;
use PhpCsFixerCustomFixers\Fixer\MultilinePromotedPropertiesFixer;
use PhpCsFixerCustomFixers\Fixer\NoCommentedOutCodeFixer;
use PhpCsFixerCustomFixers\Fixer\NoDuplicatedArrayKeyFixer;
use PhpCsFixerCustomFixers\Fixer\NoDuplicatedImportsFixer;
use PhpCsFixerCustomFixers\Fixer\NoPhpStormGeneratedCommentFixer;
use PhpCsFixerCustomFixers\Fixer\NoTrailingCommaInSinglelineFixer;
use PhpCsFixerCustomFixers\Fixer\NoUselessDirnameCallFixer;
use PhpCsFixerCustomFixers\Fixer\NoUselessParenthesisFixer;
use PhpCsFixerCustomFixers\Fixer\PhpdocNoSuperfluousParamFixer;
use PhpCsFixerCustomFixers\Fixer\PhpdocParamOrderFixer;
use PhpCsFixerCustomFixers\Fixer\PhpdocSingleLineVarFixer;
use PhpCsFixerCustomFixers\Fixer\PhpdocTypesTrimFixer;
use PhpCsFixerCustomFixers\Fixer\PhpUnitAssertArgumentsOrderFixer;
use PhpCsFixerCustomFixers\Fixer\PhpUnitDedicatedAssertFixer;
use PhpCsFixerCustomFixers\Fixer\SingleSpaceAfterStatementFixer;
use PhpCsFixerCustomFixers\Fixer\SingleSpaceBeforeStatementFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    // Original fixers from the package
    $ecsConfig->rule(MultilineCommentOpeningClosingAloneFixer::class);
    $ecsConfig->rule(MultilinePromotedPropertiesFixer::class);
    $ecsConfig->rule(NoCommentedOutCodeFixer::class);
    $ecsConfig->rule(NoDuplicatedArrayKeyFixer::class);
    $ecsConfig->rule(NoDuplicatedImportsFixer::class);
    $ecsConfig->rule(NoPhpStormGeneratedCommentFixer::class);
    $ecsConfig->rule(NoTrailingCommaInSinglelineFixer::class);
    $ecsConfig->rule(NoUselessDirnameCallFixer::class);
    $ecsConfig->rule(NoUselessParenthesisFixer::class);
    $ecsConfig->rule(PhpdocParamOrderFixer::class);
    $ecsConfig->rule(PhpdocSingleLineVarFixer::class);
    $ecsConfig->rule(PhpdocTypesTrimFixer::class);
    $ecsConfig->rule(PhpUnitAssertArgumentsOrderFixer::class);
    $ecsConfig->rule(PhpUnitDedicatedAssertFixer::class);
};
