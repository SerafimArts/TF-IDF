<?php

declare(strict_types=1);

namespace Serafim\TFIDF\StopWords;

final class InMemory implements StopWordsInterface
{
    /**
     * @param non-empty-list<non-empty-lowercase-string> $words
     */
    public function __construct(
        private readonly array $words,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function match(string $term): bool
    {
        return \in_array($term, $this->words, true);
    }
}
