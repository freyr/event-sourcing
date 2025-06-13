<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\ReturnToYieldFromFixer;
use PhpCsFixer\Fixer\ArrayNotation\YieldFromArrayToYieldsFixer;
use PhpCsFixer\Fixer\Basic\SingleLineEmptyBodyFixer;
use PhpCsFixer\Fixer\Casing\NativeTypeDeclarationCasingFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer;
use PhpCsFixer\Fixer\ClassNotation\OrderedTypesFixer;
use PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer;
use PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationArrayAssignmentFixer;
use PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationSpacesFixer;
use PhpCsFixer\Fixer\LanguageConstruct\NullableTypeDeclarationFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitDataProviderReturnTypeFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer;
use PhpCsFixer\Fixer\Whitespace\TypeDeclarationSpacesFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

/**
 * Override default config for rules or include rules that are not part of the official rule sets.
 * See: https://mlocati.github.io/php-cs-fixer-configurator/
 */
return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->rules([
        ReturnToYieldFromFixer::class,
        SingleLineEmptyBodyFixer::class,
        TypeDeclarationSpacesFixer::class,
        YieldFromArrayToYieldsFixer::class,
        NativeTypeDeclarationCasingFixer::class,
        DeclareStrictTypesFixer::class,
        PhpUnitDataProviderReturnTypeFixer::class,
    ]);
    $ecsConfig->ruleWithConfiguration(ClassDefinitionFixer::class, [
        'multi_line_extends_each_single_line' => true,
        'space_before_parenthesis' => true,
    ]);
    $ecsConfig->ruleWithConfiguration(DoctrineAnnotationSpacesFixer::class, [
        'before_array_assignments_colon' => false,
    ]);
    $ecsConfig->ruleWithConfiguration(DoctrineAnnotationArrayAssignmentFixer::class, [
        'operator' => ':',
    ]);
    $ecsConfig->ruleWithConfiguration(
        NoExtraBlankLinesFixer::class,
        [
            'tokens' => [
                'attribute',
                'case',
                'continue',
                'curly_brace_block',
                'default',
                'extra',
                'parenthesis_brace_block',
                'return',
                'square_brace_block',
                'throw',
                'use',
            ],
        ],
    );
    $ecsConfig->ruleWithConfiguration(NullableTypeDeclarationFixer::class, [
        'syntax' => 'question_mark',
    ]);
    $ecsConfig->ruleWithConfiguration(OrderedTypesFixer::class, [
        'sort_algorithm' => 'none',
        'null_adjustment' => 'always_last',
    ]);
};
