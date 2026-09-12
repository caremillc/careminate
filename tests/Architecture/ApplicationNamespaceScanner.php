<?php

declare(strict_types=1);

namespace CareminateIntegration\Tests\Architecture;

use PhpToken;

/**
 * Development-only lexical check for reserved application references.
 *
 * @internal
 */
final class ApplicationNamespaceScanner
{
    /**
     * @return list<int>
     */
    public function violationLines(string $source): array
    {
        $tokens = array_values(array_filter(
            PhpToken::tokenize($source, TOKEN_PARSE),
            static fn (PhpToken $token): bool => !$token->isIgnorable(),
        ));

        $lines = [];

        foreach ($tokens as $index => $token) {
            if (!$token->is([
                T_STRING,
                T_NAME_QUALIFIED,
                T_NAME_FULLY_QUALIFIED,
            ])) {
                continue;
            }

            $name = strtolower(ltrim($token->text, '\\'));

            if (str_starts_with($name, 'app\\')) {
                $lines[] = $token->line;

                continue;
            }

            $next = $tokens[$index + 1] ?? null;

            if ($name === 'app' && $next?->is('\\') === true) {
                $lines[] = $token->line;
            }
        }

        return array_values(array_unique($lines));
    }
}
