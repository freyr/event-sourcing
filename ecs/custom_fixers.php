<?php

declare(strict_types=1);

use Freyr\EventSourcing\Fixers\EmptyBracesFixer;
use PhpCsFixer\Fixer\Basic\BracesPositionFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

// Include our custom fixer directly
require_once __DIR__ . '/fixers/EmptyBracesFixer.php';

/**
 * Register and enable custom fixers
 */
return static function (ECSConfig $ecsConfig): void {
    // Register our custom fixer
    $ecsConfig->rule(EmptyBracesFixer::class);
    
    // Skip the default BracesPositionFixer to avoid conflicts
    $ecsConfig->skip([
        BracesPositionFixer::class => null,
    ]);
};
