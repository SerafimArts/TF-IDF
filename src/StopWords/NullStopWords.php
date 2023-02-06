<?php

declare(strict_types=1);

namespace Serafim\TFIDF\StopWords;

final class NullStopWords implements StopWordsInterface
{
    public function match(string $term): bool
    {
        return false;
    }
}
