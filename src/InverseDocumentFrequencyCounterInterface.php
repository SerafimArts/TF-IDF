<?php

declare(strict_types=1);

namespace Serafim\TFIDF;

use Serafim\TFIDF\Document\StreamingDocumentInterface;
use Serafim\TFIDF\Document\TextDocumentInterface;

interface InverseDocumentFrequencyCounterInterface
{
    /**
     * @param StreamingDocumentInterface|TextDocumentInterface $document
     * @param list<StreamingDocumentInterface|TextDocumentInterface> $haystack
     *
     * @return list<Entry>
     */
    public function compute(
        StreamingDocumentInterface|TextDocumentInterface $document,
        iterable $haystack,
    ): iterable;
}
