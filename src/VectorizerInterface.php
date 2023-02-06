<?php

declare(strict_types=1);

namespace Serafim\TFIDF;

use Serafim\TFIDF\Document\DocumentInterface;
use Serafim\TFIDF\Document\StreamingDocumentInterface;
use Serafim\TFIDF\Document\TextDocumentInterface;

interface VectorizerInterface
{
    /**
     * @param StreamingDocumentInterface|TextDocumentInterface $document
     *
     * @return void
     */
    public function add(StreamingDocumentInterface|TextDocumentInterface $document): void;

    /**
     * @return iterable<DocumentInterface, list<Entry>>
     */
    public function compute(): iterable;

    /**
     * @param StreamingDocumentInterface|TextDocumentInterface $document
     *
     * @return list<Entry>
     */
    public function computeFor(StreamingDocumentInterface|TextDocumentInterface $document): iterable;
}
