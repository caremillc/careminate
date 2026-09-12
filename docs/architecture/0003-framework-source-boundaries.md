# ADR 0003: Framework source namespace boundaries

Status: Accepted
Scope: Phase 1D.2
Related decisions: ADR 0001 and ADR 0002

## Context

Composer metadata checks enforce package ownership but cannot detect
application references written inside framework PHP files.

The framework must remain independent of its consuming application.

The application namespace is App.

## Decision

Framework source must not contain explicit qualified code names starting
with App\, compared without regard to case.

The rule reserves that spelling in framework source, including
unqualified-leading references such as App\Service.

This is intentionally a lexical policy. Such a relative spelling could
resolve beneath the current namespace in PHP, but it is still prohibited
to avoid ambiguity with the application's reserved namespace.

An explicitly namespace-relative spelling such as
namespace\App\Service refers beneath the current namespace and is not
classified as an application reference by this rule.

## Implementation

ApplicationNamespaceScanner uses PhpToken with TOKEN_PARSE.

It examines qualified-name tokens and the separated namespace prefix
used by grouped imports.

Comments and whitespace do not interfere with grouped-import detection.

The scanner returns unique, one-based violation line numbers in source
order.

Malformed PHP raises a ParseError. It is not reported as clean source.

The scanner is development tooling under tests/Architecture. It is not
part of the framework's production API or autoload configuration.

The existing root development namespace mapping loads it.

## Covered code forms

The rule detects explicit App references in:

- Ordinary imports.
- Aliased imports.
- Grouped imports.
- Function and constant imports.
- Fully qualified class references.
- Parameter and other native type declarations.
- Attributes.
- Case variants of the reserved namespace prefix.

An alias import is rejected at its declaration. The scanner does not need
to inspect every subsequent use of that alias.

## Source traversal

FrameworkSourceBoundaryTest recursively scans framework/src.

It sorts PHP paths before inspection so diagnostics have a deterministic
order.

Missing source, unreadable files, malformed PHP, and an empty PHP source
set cannot produce a successful check.

Symbolic links inside the source tree, or as the source root, are rejected
rather than followed or silently skipped.

The Composer vendor-package link is outside the scanned source tree.

Reported violations identify repository-relative paths and line numbers.

## Test coverage

Scanner tests cover forbidden references, unrelated namespace names,
comments, documentation comments, string literals, case variations,
grouped imports, line reporting, and malformed PHP.

The repository test applies the scanner to actual framework source.

PHPUnit discovers these tests through the existing integration suite.

PHPStan and PHP-CS-Fixer already cover the complete tests directory.

## Deliberate limitations

This is not a complete semantic dependency graph.

It does not resolve:

- Dynamically assembled class names.
- Dependencies expressed through string literals.
- PHPDoc types.
- Runtime includes or filesystem paths.
- Reflection or service identifiers.
- Transitive dependencies of external packages.

Ignoring a string literal does not authorize using it to bypass the
framework/application dependency boundary.

These cases remain subject to review and future targeted checks.

Namespace declarations and complete symbol ownership are not validated
by this scanner.

The check is an architecture guard, not a security sandbox.

## Failure handling

Replace an application dependency with an appropriate framework contract
or move application-specific behavior into the application.

Do not introduce a speculative abstraction solely to satisfy the check.

Do not suppress a valid violation or move the dependency into a string.

A deliberate change to the reserved namespace policy requires an updated
architecture decision and corresponding tests.

## Compatibility and cost

No production API, runtime dependency, or Composer manifest changes.

Tokenization and traversal run only during development checks.

Files are tokenized individually. No benchmark or runtime performance
improvement is claimed.