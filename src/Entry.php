<?php

declare(strict_types=1);

namespace Serafim\TFIDF;

final class Entry
{
    /**
     * @param non-empty-lowercase-string $term The passed term in lower case.
     * @param int<1, max> $occurrences Term occurrences count per document.
     * @param int<1, max> $df Document Frequency: Number of occurrences of a
     *                        term in various documents.
     * @param float $tf Term Frequency: The number of times a word appears in a
     *                  document divded by the total number of words in the
     *                  document. Every document has its own term frequency.
     * @param float $idf Inverse Document Frequency: The log of the number of
     *                   documents divided by the number of documents that
     *                   contain the word. Inverse data frequency determines the
     *                   weight of rare words across all documents in the corpus.
     * @param float $tfidf TF-IDF is simply the "Term Frequency" multiplied by
     *                     "Inverse Document Frequency".
     */
    public function __construct(
        public readonly string $term,
        public readonly int $occurrences = 1,
        public readonly int $df = 1,
        public readonly float $tf = 0.0,
        public readonly float $idf = 0.0,
        public readonly float $tfidf = 0.0,
    ) {
    }
}
