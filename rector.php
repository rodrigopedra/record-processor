<?php

use Rector\CodingStyle\Rector\Catch_\CatchExceptionNameMatchingTypeRector;
use Rector\Config\RectorConfig;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Php81\Rector\Array_\ArrayToFirstClassCallableRector;
use Rector\Php82\Rector\Class_\ReadOnlyClassRector;
use Rector\TypeDeclaration\Rector\ArrowFunction\AddArrowFunctionReturnTypeRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddParamTypeFromPropertyTypeRector;
use Rector\TypeDeclaration\Rector\Closure\AddClosureNeverReturnTypeRector;
use Rector\TypeDeclaration\Rector\Closure\AddClosureVoidReturnTypeWhereNoReturnRector;
use Rector\TypeDeclaration\Rector\Closure\ClosureReturnTypeRector;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\SafeDeclareStrictTypesRector;

/** @noinspection PhpUnhandledExceptionInspection */
return RectorConfig::configure()
    ->withImportNames(
        importDocBlockNames: false,
        importShortClasses: false,
        removeUnusedImports: true,
    )
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withPhpSets(php82: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        // typeDeclarationDocblocks: true,
        privatization: true,
        instanceOf: true,
        earlyReturn: true,
    )
    ->withSkip([
        // PHP
        ReadOnlyClassRector::class,

        // codeQuality
        SafeDeclareStrictTypesRector::class,

        // codingStyle
        CatchExceptionNameMatchingTypeRector::class,

        // typeDeclarations
        AddClosureNeverReturnTypeRector::class,
        AddClosureVoidReturnTypeWhereNoReturnRector::class,
        AddArrowFunctionReturnTypeRector::class,
        ClosureReturnTypeRector::class,

        // skip these rules on the specified files
        AddParamTypeFromPropertyTypeRector::class => [
            __DIR__ . '/app/Enrichment/EnrichmentJob.php',
        ],

        ArrayToFirstClassCallableRector::class => [
            __DIR__ . '/routes/*',
        ],

        ClosureToArrowFunctionRector::class => [
            __DIR__ . '/app/Exceptions/ExceptionHandler.php',
        ],
    ]);
