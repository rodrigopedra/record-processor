<?php

use Rector\CodeQuality\Rector\Foreach_\UnusedForeachValueToArrayKeysRector;
use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector;
use Rector\CodeQuality\Rector\If_\SimplifyIfElseToTernaryRector;
use Rector\CodingStyle\Rector\ArrowFunction\StaticArrowFunctionRector;
use Rector\CodingStyle\Rector\Catch_\CatchExceptionNameMatchingTypeRector;
use Rector\CodingStyle\Rector\Closure\StaticClosureRector;
use Rector\CodingStyle\Rector\FuncCall\CountArrayToEmptyArrayComparisonRector;
use Rector\CodingStyle\Rector\If_\NullableCompareToNullRector;
use Rector\Config\RectorConfig;
use Rector\EarlyReturn\Rector\If_\ChangeOrIfContinueToMultiContinueRector;
use Rector\EarlyReturn\Rector\Return_\ReturnBinaryOrToEarlyReturnRector;
use Rector\Naming\Rector\Assign\RenameVariableToMatchMethodCallReturnTypeRector;
use Rector\Naming\Rector\Class_\RenamePropertyToMatchTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameParamToMatchTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameVariableToMatchNewTypeRector;
use Rector\Php73\Rector\FuncCall\JsonThrowOnErrorRector;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Php80\Rector\NotIdentical\MbStrContainsRector;
use Rector\Php81\Rector\Array_\ArrayToFirstClassCallableRector;
use Rector\Php82\Rector\Class_\ReadOnlyClassRector;
use Rector\Php82\Rector\Param\AddSensitiveParameterAttributeRector;
use Rector\Php83\Rector\Class_\ReadOnlyAnonymousClassRector;
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;
use Rector\TypeDeclaration\Rector\ArrowFunction\AddArrowFunctionReturnTypeRector;
use Rector\TypeDeclaration\Rector\BooleanAnd\BinaryOpNullableToInstanceofRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddParamTypeFromPropertyTypeRector;
use Rector\TypeDeclaration\Rector\Closure\AddClosureNeverReturnTypeRector;
use Rector\TypeDeclaration\Rector\Closure\AddClosureVoidReturnTypeWhereNoReturnRector;
use Rector\TypeDeclaration\Rector\Closure\ClosureReturnTypeRector;
use Rector\TypeDeclaration\Rector\Empty_\EmptyOnNullableObjectToInstanceOfRector;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\SafeDeclareStrictTypesRector;
use Rector\TypeDeclaration\Rector\While_\WhileNullableToInstanceofRector;

/** @noinspection PhpUnhandledExceptionInspection */
return RectorConfig::configure()
    ->withComposerBased(laravel: true)
    ->withImportNames(
        importDocBlockNames: false,
        importShortClasses: false,
        removeUnusedImports: true,
    )
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withPhpSets(php83: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        // typeDeclarationDocblocks: true,
        privatization: true,
        naming: true,
        instanceOf: true,
        earlyReturn: true,
    )
    ->withRules([
        AddSensitiveParameterAttributeRector::class,
        JsonThrowOnErrorRector::class,
        MbStrContainsRector::class,
        StaticClosureRector::class,
        StaticArrowFunctionRector::class,
    ])
    ->withSkip([
        // PHP
        AddOverrideAttributeToOverriddenMethodsRector::class,
        ReadOnlyClassRector::class,
        ReadOnlyAnonymousClassRector::class,

        // codeQuality
        ExplicitBoolCompareRector::class,
        SafeDeclareStrictTypesRector::class,
        SimplifyIfElseToTernaryRector::class,
        UnusedForeachValueToArrayKeysRector::class,

        // codingStyle
        CatchExceptionNameMatchingTypeRector::class,
        CountArrayToEmptyArrayComparisonRector::class,
        NullableCompareToNullRector::class,

        // typeDeclarations
        AddClosureNeverReturnTypeRector::class,
        AddClosureVoidReturnTypeWhereNoReturnRector::class,
        AddArrowFunctionReturnTypeRector::class,
        ClosureReturnTypeRector::class,

        // naming
        RenameParamToMatchTypeRector::class,
        RenamePropertyToMatchTypeRector::class,
        RenameVariableToMatchMethodCallReturnTypeRector::class,
        RenameVariableToMatchNewTypeRector::class,

        // instanceOf
        BinaryOpNullableToInstanceofRector::class,
        EmptyOnNullableObjectToInstanceOfRector::class,
        FlipTypeControlToUseExclusiveTypeRector::class,
        WhileNullableToInstanceofRector::class,

        // earlyReturn
        ChangeOrIfContinueToMultiContinueRector::class,
        ReturnBinaryOrToEarlyReturnRector::class,

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

        StaticClosureRector::class => [
            __DIR__ . '/app/Providers/AppServiceProvider.php',
            __DIR__ . '/routes/console.php',
        ],
    ]);
