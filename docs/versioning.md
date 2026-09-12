# Framework versioning policy

## Current status

Careminate is under development.

No stable release is declared by the Phase 1 implementation.

The Composer package is caremillc/framework.

The local development path repository uses development version metadata.
That metadata must not be presented as a stable release.

## Version source of truth

Published package versions come from repository release tags.

The framework composer.json omits the version field.

No independent framework version constant is introduced in Phase 1D.1.

Duplicating the same version in source constants, Composer metadata, and
release tags would require synchronization without a current runtime
consumer that justifies it.

Composer's installed package metadata can be inspected from the project:

    composer show caremillc/framework

The displayed version describes the installed package. It does not by
itself prove that tests, security review, or release gates passed.

## Release numbering

Released versions use MAJOR.MINOR.PATCH.

After the first stable release:

- PATCH: backward-compatible fixes.
- MINOR: backward-compatible additions and documented deprecations.
- MAJOR: incompatible public API changes.

Pre-release identifiers distinguish development milestones from stable
releases.

A pre-release or development branch must not be relabeled as stable
merely to satisfy a dependency constraint.

Published release tags must not be moved to different source content.
Corrections require another release.

## Public API

Types explicitly marked @api and documented public contracts form the
supported API.

Current public exception types are:

- Careminate\Exception\ExceptionInterface
- Careminate\Exception\FrameworkException

Compatibility review includes documented behavior, inheritance
relationships, constructor parameters, and parameter names used by
supported named-argument calls.

Changing an implementation detail must not silently change the public
contract.

## Development compatibility

Before 1.0, the project may evolve, but changes must still be deliberate.

Existing working behavior is preserved unless a breaking change is
explicitly authorized.

Record incompatible changes and migration instructions in the unit that
introduces them.

Pre-1.0 status is not permission to silently rewrite previously delivered
contracts.

## Deprecation

A deprecation must identify:

- The affected public contract.
- The supported replacement.
- Migration instructions.
- The intended removal release.

Do not introduce a deprecation without a usable migration path.

Automated backward-compatibility tooling and the complete release process
remain later work. This document establishes policy, not evidence that
those tools exist.

## Dependency reproducibility

Commit the Composer-generated application lock file.

Use composer install for ordinary checkouts and CI.

Dependency updates must be deliberate, reviewed changes.

For a local path package, retain the matching repository commit as well:
the Composer lock file alone does not freeze the framework source tree.

## Release prerequisites

A release requires evidence for the applicable gates, including:

- Supported PHP and operating-system verification.
- Tests, analysis, formatting, and manifest validation.
- Public API compatibility review.
- Matching documentation and migration guidance.
- Dependency and security review.
- A deliberate distribution license decision.

This list does not mark any gate complete.

## Executable enforcement in Phase 1D.1

PackageBoundaryTest verifies that framework/composer.json omits version.

It also verifies the approved PHP compatibility declaration.

Tag validation, release automation, and installed-version reporting APIs
are not implemented by this unit.