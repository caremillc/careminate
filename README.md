# Careminate

Careminate is a modular PHP framework under incremental development.

The current implementation provides engineering foundations. It is not
yet a runnable web framework or a stable production release.

## Project identity

| Item | Value |
| --- | --- |
| Framework package | caremillc/framework |
| Framework namespace | Careminate\ |
| Application namespace | App\ |
| Supported PHP branches | 8.4 and 8.5 |

The development application depends on the framework through a Composer
path repository at framework/.

## Implemented foundations

- Separate application and framework Composer manifests.
- Composer-managed production and development autoloading.
- Public exception marker and abstract runtime-exception base.
- Package installation and namespace-mapping tests.
- Composer metadata boundary tests.
- A lexical application-namespace boundary check for framework source.
- PHPUnit, PHPStan max, and PSR-12 formatting configuration.
- Architecture and versioning policies.
- A Windows/Linux CI workflow for PHP 8.4 and PHP 8.5.

Implementation availability does not imply that every verification gate
has passed. Consult docs/progress.md for the recorded evidence.

## Requirements

Use Composer 2 and PHP 8.4 or PHP 8.5.

Composer checks required PHP extensions during installation.

Use a CLI runtime that meets the declared requirements. XAMPP's Apache
runtime and the PHP executable on the shell path may differ.

## Installation

From a checkout containing the committed composer.lock:

```powershell
composer install
composer check-platform-reqs
composer check
```

Run each command only after the preceding command succeeds.

If this is the initial repository setup and no lock file exists, resolve
dependencies deliberately using the minimum supported PHP branch:

```powershell
composer update
```

Review and commit the generated lock file. Do not fabricate or manually
edit dependency lock entries.

## Local Windows development

The current development directory is:

```text
C:\xampp\htdocs\careminate
```

From PowerShell:

```powershell
Set-Location C:\xampp\htdocs\careminate
composer check
```

Git Bash users can run composer check directly from the project directory.

No HTTP entry point or application kernel is delivered in Phase 1.

## Quality commands

| Command | Purpose |
| --- | --- |
| composer validate:packages | Validate application and framework manifests |
| composer test | Run configured tests |
| composer analyse | Run PHPStan at max |
| composer cs:check | Check PSR-12 formatting without editing |
| composer cs:fix | Apply formatting changes |
| composer check | Run the complete configured quality sequence |

## Local framework installation

Composer may link or mirror the framework path package.

After framework changes, a mirrored installation can be refreshed with:

```powershell
composer reinstall caremillc/framework
composer dump-autoload
```

Do not edit generated Composer files inside vendor/.

## Continuous integration

.github/workflows/ci.yml runs on pushes to main, pull requests, and manual
dispatch.

The matrix covers Ubuntu 24.04 and Windows Server 2022 with PHP 8.4 and
PHP 8.5.

Every job requires a committed lock file, installs its dependencies, checks
the actual runtime requirements, and runs composer check.

This workflow requires a GitHub repository with Actions enabled.

## Documentation

- [Contribution workflow](CONTRIBUTING.md)
- [Quality checks](docs/development/quality-checks.md)
- [Exception contracts](docs/exceptions.md)
- [Versioning policy](docs/versioning.md)
- [Package boundaries](docs/architecture/0001-package-boundaries.md)
- [Metadata boundaries](docs/architecture/0002-package-metadata-boundaries.md)
- [Source boundaries](docs/architecture/0003-framework-source-boundaries.md)
- [Phase 1 verification](docs/development/phase-1-verification.md)
- [Progress](docs/progress.md)

## Licensing and release status

The initial manifests declare proprietary pending a distribution-license
decision.

No open-source license grant or stable release is implied by this
development repository.