<?php

declare(strict_types=1);

namespace Serafim\TFIDF\StopWords;

use voku\helper\StopWords;
use voku\helper\StopWordsLanguageNotExists;

final class Factory implements FactoryInterface
{
    /**
     * @var StopWords
     */
    private readonly StopWords $voku;

    public function __construct()
    {
        $this->voku = new StopWords();
    }

    /**
     * @param non-empty-string $locale
     *
     * @return StopWordsInterface
     */
    public function create(string $locale): StopWordsInterface
    {
        $primary = $this->getPrimaryLanguage($locale);

        try {
            /** @var non-empty-list<non-empty-lowercase-string> $words */
            $words = $this->voku->getStopWordsFromLanguage($primary);

            return new InMemory($words);
        } catch (StopWordsLanguageNotExists) {
            // ...
        }

        return new NullStopWords();
    }

    /**
     * @param non-empty-string $locale
     *
     * @return non-empty-lowercase-string
     */
    private function getPrimaryLanguage(string $locale): string
    {
        $parts = \explode('_', $locale);

        return \strtolower($parts[0]);
    }
}
