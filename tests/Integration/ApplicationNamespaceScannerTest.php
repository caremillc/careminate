<?php

declare(strict_types=1);

namespace CareminateIntegration\Tests\Integration;

use CareminateIntegration\Tests\Architecture\ApplicationNamespaceScanner;
use ParseError;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ApplicationNamespaceScannerTest extends TestCase
{
    #[DataProvider('forbiddenSources')]
    public function testDetectsReservedApplicationReferences(string $source): void
    {
        $scanner = new ApplicationNamespaceScanner();

        self::assertSame([1], $scanner->violationLines($source));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function forbiddenSources(): iterable
    {
        yield 'ordinary import' => [
            '<?php use App\Service;',
        ];

        yield 'aliased import' => [
            '<?php use App\Service as ServiceAlias;',
        ];

        yield 'grouped import' => [
            '<?php use App\{Service, Handler};',
        ];

        yield 'fully qualified grouped import' => [
            '<?php use \App\{Service, Handler};',
        ];

        yield 'function import' => [
            '<?php use function App\run;',
        ];

        yield 'constant import' => [
            '<?php use const App\MODE;',
        ];

        yield 'fully qualified construction' => [
            '<?php $service = new \App\Service();',
        ];

        yield 'case variant' => [
            '<?php $service = new \aPp\Service();',
        ];

        yield 'parameter type' => [
            '<?php function consume(\App\Service $service): void {}',
        ];

        yield 'attribute' => [
            '<?php #[\App\Marker] final class Example {}',
        ];
    }

    #[DataProvider('allowedSources')]
    public function testIgnoresUnrelatedCodeAndText(string $source): void
    {
        $scanner = new ApplicationNamespaceScanner();

        self::assertSame([], $scanner->violationLines($source));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function allowedSources(): iterable
    {
        yield 'comment' => [
            '<?php // App\Service is mentioned in a comment.',
        ];

        yield 'documentation comment' => [
            '<?php /** @see App\Service */ final class Example {}',
        ];

        yield 'string literal' => [
            '<?php $name = \'App\\Service\';',
        ];

        yield 'different namespace prefix' => [
            '<?php use Application\Service;',
        ];

        yield 'nested application-like segment' => [
            '<?php use Vendor\App\Service;',
        ];

        yield 'framework namespace' => [
            '<?php use Careminate\Exception\FrameworkException;',
        ];

        yield 'ordinary function name' => [
            '<?php function app(): void {}',
        ];

        yield 'explicit namespace-relative reference' => [
            '<?php namespace Careminate; $service = new namespace\App\Service();',
        ];
    }

    public function testReportsUniqueSourceLinesInOrder(): void
    {
        $source = <<<'PHP'
<?php
use App\Service;
use App\Handler;
$result = [\App\Service::class, \App\Handler::class];
PHP;

        $scanner = new ApplicationNamespaceScanner();

        self::assertSame([2, 3, 4], $scanner->violationLines($source));
    }

    public function testMalformedPhpIsNotTreatedAsCleanSource(): void
    {
        $scanner = new ApplicationNamespaceScanner();

        $this->expectException(ParseError::class);

        $scanner->violationLines('<?php function broken(');
    }
}
