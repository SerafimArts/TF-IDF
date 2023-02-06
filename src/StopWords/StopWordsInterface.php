<?php

declare(strict_types=1);

namespace Serafim\TFIDF\StopWords;

interface StopWordsInterface
{
    /**
     * @param non-empty-lowercase-string $term
     *
     * @return bool
     */
    public function match(string $term): bool;
}
