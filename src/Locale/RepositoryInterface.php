<?php

declare(strict_types=1);

namespace Serafim\TFIDF\Locale;

interface RepositoryInterface
{
    /**
     * Gets the default locale value string.
     *
     * @return non-empty-string
     */
    public function getDefault(): string;

    /**
     * Returns {@see true} in case of passed locale `$name` is supported by
     * environment or {@see false} instead.
     *
     * @param non-empty-string $name
     *
     * @return bool
     */
    public function isValid(string $name): bool;

    /**
     * Returns list of the available locale strings.
     *
     * @return list<non-empty-string>
     */
    public function getAll(): iterable;
}
