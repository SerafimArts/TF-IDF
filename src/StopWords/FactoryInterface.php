<?php

declare(strict_types=1);

namespace Serafim\TFIDF\StopWords;

interface FactoryInterface
{
    /**
     * @param non-empty-string $locale
     *
     * @return StopWordsInterface
     */
    public function create(string $locale): StopWordsInterface;
}
