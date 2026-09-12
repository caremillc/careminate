<?php

declare(strict_types=1);

namespace CareminateIntegration\Tests\Integration;

use Composer\Autoload\ClassLoader;
use Composer\InstalledVersions;
use PHPUnit\Framework\TestCase;

final class FrameworkPackageTest extends TestCase
{
    public function testFrameworkIsInstalledWithItsOwnManifest(): void
    {
        self::assertTrue(
            InstalledVersions::isInstalled('caremillc/framework'),
            'Install the local framework package with Composer.',
        );

        $installPath = InstalledVersions::getInstallPath(
            'caremillc/framework',
        );

        self::assertNotNull($installPath);
        self::assertDirectoryExists($installPath);
        self::assertFileExists($installPath . '/composer.json');

        $rootManifest = realpath(
            self::projectRoot() . '/composer.json',
        );

        $frameworkManifest = realpath(
            $installPath . '/composer.json',
        );

        self::assertNotFalse($rootManifest);
        self::assertNotFalse($frameworkManifest);
        self::assertNotSame(
            $rootManifest,
            $frameworkManifest,
            'The framework must own a separate Composer manifest.',
        );
    }

    public function testFrameworkNamespaceUsesTheInstalledPackage(): void
    {
        $installPath = InstalledVersions::getInstallPath(
            'caremillc/framework',
        );

        self::assertNotNull($installPath);

        self::assertNamespaceDirectory(
            self::loader(),
            'Careminate\\',
            $installPath,
            'src',
        );
    }

    public function testApplicationNamespaceUsesTheApplicationRoot(): void
    {
        self::assertNamespaceDirectory(
            self::loader(),
            'App\\',
            self::projectRoot(),
            'app',
        );
    }

    private static function projectRoot(): string
    {
        return dirname(__DIR__, 2);
    }

    private static function loader(): ClassLoader
    {
        $loader = require self::projectRoot() . '/vendor/autoload.php';

        self::assertInstanceOf(ClassLoader::class, $loader);

        return $loader;
    }

    private static function assertNamespaceDirectory(
        ClassLoader $loader,
        string $namespace,
        string $packageDirectory,
        string $sourceDirectory,
    ): void {
        $prefixes = $loader->getPrefixesPsr4();

        self::assertArrayHasKey(
            $namespace,
            $prefixes,
            'Composer must register the expected namespace.',
        );

        $directories = $prefixes[$namespace];

        self::assertCount(
            1,
            $directories,
            'The namespace must have one owning source directory.',
        );

        $expectedParent = realpath($packageDirectory);

        self::assertNotFalse($expectedParent);

        foreach ($directories as $directory) {
            $normalizedDirectory = rtrim(
                str_replace('\\', '/', $directory),
                '/',
            );

            self::assertSame(
                $sourceDirectory,
                basename($normalizedDirectory),
            );

            $actualParent = realpath(
                dirname($normalizedDirectory),
            );

            self::assertNotFalse($actualParent);
            self::assertSame(
                $expectedParent,
                $actualParent,
                'The namespace must resolve inside its owning package.',
            );
        }
    }
}
