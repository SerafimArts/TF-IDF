<?php

declare(strict_types=1);

namespace Serafim\TFIDF\Locale;

abstract class Repository implements RepositoryInterface
{
    /**
     * Returns locale instance from global environment.
     *
     * @return RepositoryInterface
     */
    public static function fromGlobals(): RepositoryInterface
    {
        return IntlRepository::new();
    }

    /**
     * {@inheritDoc}
     */
    public function isValid(string $name): bool
    {
        foreach ($this->getAll() as $locale) {
            if ($locale === $name) {
                return true;
            }
        }

        return false;
    }
}
