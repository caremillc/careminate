# ADR 0002: Package metadata boundaries

Status: Accepted
Scope: Phase 1D.1
Related decision: ADR 0001

## Context

The verified local project uses:

- Framework Composer package: caremillc/framework
- Framework namespace: Careminate\
- Application namespace: App\
- Local directory: C:\xampp\htdocs\careminate

These observed names supersede the initial package-name and local-path
examples in ADR 0001. Its dependency-direction decision remains in force.

The application's package name is read from its manifest rather than
duplicated in the boundary tests.

## Decision

The application is a Composer project.

The framework is a separate Composer library.

The application requires caremillc/framework. The framework must not
require the application or itself.

Framework production autoloading consists of:

    Careminate\ => src/

Application production autoloading consists of:

    App\ => app/

The application must consume the framework through its installed package.
It must not duplicate the framework's production namespace mapping.

Additional production autoload mechanisms or namespaces require a
documented architecture change.

## Development dependencies

The following tools belong in the application's require-dev section:

- friendsofphp/php-cs-fixer
- phpstan/phpstan
- phpstan/phpstan-phpunit
- phpunit/phpunit

They must not appear in either package's runtime require section.

This rule concerns these direct tool dependencies. It does not prohibit a
runtime library merely because a development tool also uses that library.

## PHP compatibility

Both manifests must declare:

    ~8.4.0 || ~8.5.0

The boundary test intentionally checks this exact policy expression.

Supporting another PHP branch requires an explicit policy change,
manifest updates, and successful compatibility verification.

## Version ownership

The framework manifest must omit version.

Repository metadata supplies package version identity. Development path
repository metadata is not evidence that a stable release exists.

The detailed release policy is recorded in docs/versioning.md.

## Enforcement

PackageBoundaryTest reads the repository's source manifests.

It checks:

1. Package roles and dependency direction.
2. Framework production namespace ownership.
3. Application production namespace ownership.
4. Direct quality-tool dependency isolation.
5. The approved PHP compatibility expression.
6. Absence of a duplicated framework version field.

Missing files, unreadable files, invalid JSON, and missing required
sections fail rather than being replaced with fallback data.

Existing FrameworkPackageTest separately checks Composer's installed
package and generated namespace mappings.

## Limitations

These tests do not inspect the transitive dependency graph.

They do not detect PHP source references to application classes,
dynamic class names, or runtime filesystem access.

Source dependency checks belong to Phase 1D.2.

These rules are architecture checks, not a sandbox or a security boundary.

## Compatibility

No production API or Composer manifest changes in this unit.

The tests enforce the currently approved package architecture.

An intentional architecture change must update the relevant decision
and tests together. An accidental failure must be fixed at its source.