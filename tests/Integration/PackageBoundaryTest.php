<?php

declare(strict_types=1);

namespace CareminateIntegration\Tests\Integration;

use PHPUnit\Framework\TestCase;

final class PackageBoundaryTest extends TestCase
{
    public function testApplicationDependsOnFrameworkWithoutReverseDependency(): void
    {
        $application = self::manifest('composer.json');
        $framework = self::manifest('framework/composer.json');

        self::assertSame('project', $application['type'] ?? null);
        self::assertSame('library', $framework['type'] ?? null);
        self::assertSame(
            'caremillc/framework',
            $framework['name'] ?? null,
        );

        $applicationName = $application['name'] ?? null;

        self::assertIsString($applicationName);
        self::assertNotSame('caremillc/framework', $applicationName);

        $applicationRequirements = self::section(
            $application,
            'require',
        );

        $frameworkRequirements = self::section(
            $framework,
            'require',
        );

        self::assertArrayHasKey(
            'caremillc/framework',
            $applicationRequirements,
        );

        self::assertArrayNotHasKey(
            $applicationName,
            $frameworkRequirements,
            'The framework must not depend on its consuming application.',
        );

        self::assertArrayNotHasKey(
            'caremillc/framework',
            $frameworkRequirements,
            'The framework must not depend on itself.',
        );
    }

    public function testFrameworkOwnsOnlyItsProductionNamespace(): void
    {
        $framework = self::manifest('framework/composer.json');

        self::assertSame(
            [
                'psr-4' => [
                    'Careminate\\' => 'src/',
                ],
            ],
            self::section($framework, 'autoload'),
        );
    }

    public function testApplicationDoesNotDuplicateFrameworkAutoloading(): void
    {
        $application = self::manifest('composer.json');

        self::assertSame(
            [
                'psr-4' => [
                    'App\\' => 'app/',
                ],
            ],
            self::section($application, 'autoload'),
        );
    }

    public function testQualityToolsRemainDevelopmentDependencies(): void
    {
        $application = self::manifest('composer.json');
        $framework = self::manifest('framework/composer.json');

        $developmentRequirements = self::section(
            $application,
            'require-dev',
        );

        $applicationRequirements = self::section(
            $application,
            'require',
        );

        $frameworkRequirements = self::section(
            $framework,
            'require',
        );

        $tools = [
            'friendsofphp/php-cs-fixer',
            'phpstan/phpstan',
            'phpstan/phpstan-phpunit',
            'phpunit/phpunit',
        ];

        foreach ($tools as $tool) {
            self::assertArrayHasKey(
                $tool,
                $developmentRequirements,
            );

            self::assertArrayNotHasKey(
                $tool,
                $applicationRequirements,
                $tool . ' must not be an application runtime dependency.',
            );

            self::assertArrayNotHasKey(
                $tool,
                $frameworkRequirements,
                $tool . ' must not be a framework runtime dependency.',
            );
        }
    }

    public function testBothPackagesDeclareTheApprovedPhpBranches(): void
    {
        foreach (['composer.json', 'framework/composer.json'] as $path) {
            $requirements = self::section(
                self::manifest($path),
                'require',
            );

            self::assertSame(
                '~8.4.0 || ~8.5.0',
                $requirements['php'] ?? null,
                $path . ' must preserve the approved PHP compatibility policy.',
            );
        }
    }

    public function testFrameworkDoesNotDuplicateRepositoryVersionMetadata(): void
    {
        $framework = self::manifest('framework/composer.json');

        self::assertArrayNotHasKey(
            'version',
            $framework,
            'Framework versions must come from repository metadata.',
        );
    }

    /**
     * @return array<array-key, mixed>
     */
    private static function manifest(string $relativePath): array
    {
        $path = dirname(__DIR__, 2) . '/' . $relativePath;

        self::assertFileExists($path);

        $contents = file_get_contents($path);

        self::assertIsString($contents);

        $manifest = json_decode(
            $contents,
            associative: true,
            flags: JSON_THROW_ON_ERROR,
        );

        self::assertIsArray($manifest);

        return $manifest;
    }

    /**
     * @param array<array-key, mixed> $manifest
     *
     * @return array<array-key, mixed>
     */
    private static function section(array $manifest, string $key): array
    {
        self::assertArrayHasKey($key, $manifest);

        $section = $manifest[$key];

        self::assertIsArray($section);

        return $section;
    }
}
