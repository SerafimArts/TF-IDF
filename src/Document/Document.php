<?php

declare(strict_types=1);

namespace Serafim\TFIDF\Document;

abstract class Document implements DocumentInterface
{
    /**
     * @param non-empty-string $locale
     */
    protected function __construct(
        protected readonly string $locale,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getLocale(): string
    {
        return $this->locale;
    }
}
