# Exception foundation

## Scope

Phase 1C defines the public exception boundary for Careminate.

It provides:

- Careminate\Exception\ExceptionInterface
- Careminate\Exception\FrameworkException

It does not install an exception handler, render HTTP responses, log
failures, or introduce component-specific exceptions.

## Architecture decision

Status: Accepted
Scope: Phase 1C

ExceptionInterface extends PHP's Throwable contract.

It identifies framework-owned exceptions independently of their native
exception family.

FrameworkException extends PHP's RuntimeException and implements
ExceptionInterface. It is abstract because callers should receive a
specific component failure.

The abstract base is a supported extension boundary. Its empty body is
intentional: native exception behavior is inherited without duplication.

Concrete component exceptions should be final unless their own contract
explicitly supports inheritance.

An exception that needs a different native classification may extend
that native exception class and implement ExceptionInterface.

A class cannot obtain throwable behavior merely by implementing the
interface. It must inherit from an appropriate PHP exception class.

## Public API

| Type | Contract |
| --- | --- |
| ExceptionInterface | Common catch boundary for framework-owned exceptions |
| FrameworkException | Abstract base for specific runtime failures |

FrameworkException inherits the native constructor parameters:

- message: string, default empty string
- code: int, default zero
- previous: nullable Throwable, default null

The exception code has no automatic HTTP-status meaning.

Existing Throwable accessors remain available, including getMessage(),
getCode(), getPrevious(), getFile(), getLine(), and getTrace().

No extra context array, serialization format, or mutable framework
metadata is introduced.

## Extension guidance

A runtime component should introduce a specifically named exception
extending FrameworkException when that component is implemented.

Use inherited named constructor arguments where appropriate:

- message: a useful diagnostic description
- code: an explicitly documented integer, otherwise zero
- previous: the original failure when translating across a boundary

If a subclass defines its own constructor, it must deliberately preserve
the relevant native exception state through the parent constructor.

Do not wrap every caught Throwable automatically. Translate a failure
only when the component boundary requires a different public contract.

## Catching failures

Catch the most specific exception for which the caller has a recovery
policy.

Catch ExceptionInterface at a boundary that intentionally handles
framework-owned failures collectively.

ExceptionInterface does not match unrelated native exceptions, arbitrary
third-party exceptions, or every failure a framework operation might
propagate.

A generic Throwable handler belongs at a separately designed execution
boundary. This unit does not implement one.

Catching an exception does not justify discarding it or silently returning
a successful result.

## Cause preservation and security

The previous Throwable is preserved by identity, including its own chain.

An Error can be the immediate previous Throwable.

Preserved causes may contain sensitive messages, paths, arguments, and
trace information. This exception foundation does not redact them.

Component exception factories must avoid constructing messages from
secrets or untrusted raw payloads.

Future logging and rendering boundaries must apply their own explicit
redaction and disclosure policies. Do not send raw exception strings or
traces to public clients.

## Testing

FrameworkExceptionTest covers:

1. Native constructor defaults.
2. Explicit message and integer code preservation.
3. Preservation of an existing cause chain.
4. An Error as the immediate cause.
5. The common catch contract for runtime and other native exception families.

The tests instantiate subclasses through Composer's production
Careminate namespace mapping. Missing or stale installed source prevents
the tests from succeeding.

PHPUnit discovers framework tests directly. No test namespace is added
to the framework's production Composer autoload configuration.

## Compatibility

This is an additive public API in the fresh Phase 1 codebase.

No earlier public type has been removed or renamed.

The namespace, marker contract, and runtime-base inheritance are public
compatibility commitments once a stable release is published.

Compatibility with older, unavailable Careminate implementations has not
been evaluated.

## Performance

No additional constructor, reflection, filesystem access, or logging is
added to exception construction.

No benchmark result or performance improvement is claimed.