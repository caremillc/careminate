# Development quality checks

## Scope

The root project owns development tooling.

Checks cover package integration, framework unit tests, static analysis,
and PHP formatting.

The framework package does not acquire runtime dependencies on PHPUnit,
PHPStan, or PHP-CS-Fixer.

## Prerequisites

Use PHP 8.4 or PHP 8.5 and Composer 2.

Confirm the CLI executable and configuration:

```powershell
Get-Command php
php --version
php --ini
composer --version
```

The CLI runtime may differ from Apache's XAMPP runtime.

Composer reports missing extensions during dependency resolution.
Do not bypass platform requirements.

## Installation

For initial adoption of the Phase 1B development dependencies:

```powershell
Set-Location C:\xampp\htdocs\caremi

composer update
composer check-platform-reqs
composer check
```

Review and commit the generated composer.lock.

For subsequent checkouts with a committed lock file:

```powershell
composer install
composer check-platform-reqs
composer check
```

Run each command only after the preceding command succeeds.

## Applying Phase 1C

Phase 1C changes no dependency constraints.

After copying its files, refresh the installed local framework package
and regenerate Composer's autoloader:

```powershell
composer reinstall careminate/framework
composer dump-autoload
composer check
```

This also refreshes environments where Composer mirrors the path package.

Install dependencies first if the framework is not yet installed.

## Commands

| Command | Behavior |
| --- | --- |
| composer validate:packages | Strictly validates both manifests |
| composer test | Runs integration and framework unit tests |
| composer analyse | Runs PHPStan at max |
| composer cs:check | Reports formatting differences without editing |
| composer cs:fix | Applies the configured formatting rules |
| composer check | Runs validation, tests, analysis, and formatting checks |

composer validate:packages 
composer test 
composer analyse
composer cs:check
composer cs:fix 
composer check

The aggregate check stops on failure.

Composer's @php script prefix uses the PHP executable running Composer.

## Test policy

PHPUnit fails on empty discovery, risky tests, warnings, notices,
deprecations, and unexpected test output.

Tests run in random order to help expose order dependencies.

Replay a reported seed with:

```powershell
composer test -- --random-order-seed=12345
```

Replace the example seed with the reported value.

Package integration tests verify installed package ownership and
Composer's generated namespace mappings.

Framework unit tests load production exceptions through Composer and
verify the public exception contracts.

Framework tests are discovered directly by PHPUnit. Shared test helpers,
if introduced later, require an explicit development autoload decision.

Tests require no database or network service after installation.

## Static analysis

PHPStan runs at max without a baseline or ignored errors.

The PHP language target is 8.4.

The PHPUnit extension is loaded explicitly from phpstan.neon.
Composer plugins remain disabled.

Analysis covers:

- framework/src/
- framework/tests/
- tests/
- .php-cs-fixer.dist.php

Application source and any other new PHP directories must be included
when introduced.

Static analysis does not replace runtime tests on PHP 8.4 and PHP 8.5.

## Formatting

PHP-CS-Fixer applies PSR-12 to framework source, framework tests,
integration tests, and its own configuration file.

Risky fixers are disabled.

The check command uses dry-run mode with its cache disabled.

The fix command edits files. Review its diff and rerun all checks.

New source directories must be added to the finder when introduced.

## Troubleshooting

### Dependency installation fails

Resolve Composer's reported version or extension conflict.

Do not bypass platform checks or lower the supported PHP baseline.

### Installed framework source is stale

Refresh the path package and autoload metadata:

```powershell
composer reinstall careminate/framework
composer dump-autoload
composer test
```

### A namespace check or class load fails

Inspect the owning Composer manifest and installed package location.

Correct source manifests and regenerate autoloading.

Do not edit generated files under vendor/composer.

### A quality check fails

Read the diagnostic, fix its cause, and rerun the affected check.

Do not weaken analysis, test policies, or formatting to claim completion.

## Verification

Copying files is not evidence that checks pass.

Record actual command results.

Coverage collection requires an appropriate coverage driver and an
explicit coverage run. The source configuration alone does not establish
coverage evidence.

Phase 1 remains incomplete until all planned units and mandatory gates
have passing evidence.