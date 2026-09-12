# Phase 1 verification

## Purpose

This document defines the evidence needed to close Phase 1.

It is a checklist, not a declaration that the checks have passed.

## Implemented units

| Unit | Deliverable |
| --- | --- |
| 1A | Package manifests and repository conventions |
| 1B | Tests, static analysis, and formatting infrastructure |
| 1C | Exception contracts and behavior tests |
| 1D.1 | Package metadata boundaries and version policy |
| 1D.2 | Lexical framework source boundary checks |
| 1E | CI, contributor instructions, and verification procedure |

General-purpose support utilities were not introduced because the
foundation has no concrete runtime consumer requiring them.

Version ownership is documented through Composer and repository tags.
No duplicate runtime version constant is required.

## Local verification

Run from the repository root:

```powershell
composer install
composer check-platform-reqs
composer check
git diff --check
```

Each command must succeed.

If the initial lock file does not exist, generate it deliberately using
PHP 8.4, validate it, and commit it before CI.

Do not delete an existing lock merely to obtain a different resolution.

## CI matrix

All four jobs must succeed for the same tested source revision:

| Runner | PHP | Required result |
| --- | --- | --- |
| Ubuntu 24.04 | 8.4 | PASS |
| Ubuntu 24.04 | 8.5 | PASS |
| Windows Server 2022 | 8.4 | PASS |
| Windows Server 2022 | 8.5 | PASS |

A cancelled or skipped job is not a passing result.

A previous successful run does not verify subsequent code changes.

## What each job checks

1. The root composer.lock exists in the checkout and is tracked.
2. Both package manifests validate.
3. Dependencies install from the lock without updating it.
4. Actual PHP and extension requirements are satisfied.
5. All configured tests pass.
6. PHPStan max passes.
7. Formatting passes.
8. Dependency manifests and the lock remain unchanged.

The workflow uses the existing Composer scripts so local and CI quality
commands remain aligned.

## Test areas

The suite includes:

- Installed package and autoload mapping checks.
- Exception defaults and cause preservation.
- Exception catch-contract interoperability.
- Source Composer metadata boundaries.
- Scanner positive and negative cases.
- Actual framework source inspection.

Scanner limitations remain documented in ADR 0003. Passing that check
does not prove the absence of every dynamic dependency.

## Dependency evidence

The committed root lock file must correspond to the tested revision.

If PHP 8.4 cannot install that lock, the compatibility gate fails even if
PHP 8.5 succeeds.

Resolve the conflict under the minimum supported runtime, review the
dependency changes, and rerun the complete matrix.

Do not use composer update inside CI.

## Recording evidence

For the final phase report, retain:

- Tested commit identifier.
- CI run identifier or URL.
- All four job outcomes.
- Actual PHP and Composer versions from the jobs.
- Test results and any diagnostic output.
- Confirmation that the generated dependency lock is committed.
- Any remaining explicitly accepted verification exception.

Update docs/progress.md using those results.

## Remaining boundaries

Phase 1 does not implement the dependency container, application kernel,
modules, configuration services, HTTP handling, routing, or console
framework.

Coverage percentages, mutation scores, benchmarks, full backward-
compatibility automation, and release security artifacts are not claimed.

The current production exception types inherit native behavior and add
no custom algorithm requiring a mutation threshold in this unit.

## Completion rule

Declare Phase 1 complete only after its mandatory gates pass or the user
explicitly accepts a named external verification exception.

After completion, recommend Phase 2 without beginning it automatically.