<?php

declare(strict_types=1);

namespace Serafim\TFIDF;

use Serafim\TFIDF\Document\StreamingDocumentInterface;
use Serafim\TFIDF\Document\TextDocumentInterface;
use Serafim\TFIDF\Memory\MemoryInterface;

interface TermFrequencyCounterInterface
{
    /**
     * @param StreamingDocumentInterface|TextDocumentInterface $document
     *
     * @return MemoryInterface
     */
    public function compute(StreamingDocumentInterface|TextDocumentInterface $document): MemoryInterface;
}
