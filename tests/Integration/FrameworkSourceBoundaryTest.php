<?php

declare(strict_types=1);

namespace CareminateIntegration\Tests\Integration;

use CareminateIntegration\Tests\Architecture\ApplicationNamespaceScanner;
use FilesystemIterator;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

final class FrameworkSourceBoundaryTest extends TestCase
{
    public function testFrameworkSourceContainsNoExplicitApplicationReferences(): void
    {
        $sourceDirectory = dirname(__DIR__, 2) . '/framework/src';

        self::assertDirectoryExists($sourceDirectory);
        self::assertFalse(
            is_link($sourceDirectory),
            'The framework source root must not be a symbolic link.',
        );

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $sourceDirectory,
                FilesystemIterator::SKIP_DOTS,
            ),
            RecursiveIteratorIterator::SELF_FIRST,
        );

        $paths = [];

        foreach ($iterator as $entry) {
            self::assertInstanceOf(SplFileInfo::class, $entry);

            self::assertFalse(
                $entry->isLink(),
                'Source traversal must not silently skip symbolic links: '
                    . $entry->getPathname(),
            );

            if (!$entry->isFile()) {
                continue;
            }

            if (strtolower($entry->getExtension()) !== 'php') {
                continue;
            }

            $paths[] = $entry->getPathname();
        }

        sort($paths, SORT_STRING);

        self::assertNotEmpty(
            $paths,
            'The source boundary check must inspect at least one PHP file.',
        );

        $scanner = new ApplicationNamespaceScanner();
        $violations = [];

        foreach ($paths as $path) {
            $source = file_get_contents($path);

            self::assertIsString(
                $source,
                'Framework source must be readable: ' . $path,
            );

            $relativePath = str_replace(
                '\\',
                '/',
                substr($path, strlen($sourceDirectory) + 1),
            );

            foreach ($scanner->violationLines($source) as $line) {
                $violations[] = 'framework/src/' . $relativePath . ':' . $line;
            }
        }

        self::assertSame(
            [],
            $violations,
            "Framework source contains reserved App\\ references:\n"
                . implode("\n", $violations),
        );
    }
}
