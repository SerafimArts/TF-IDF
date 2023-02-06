<?php

declare(strict_types=1);

namespace Serafim\TFIDF\Tokenizer;

use Serafim\TFIDF\Document\StreamingDocumentInterface;
use Serafim\TFIDF\Document\TextDocumentInterface;

interface TokenizerInterface
{
    /**
     * Split the passed text or resource stream by words.
     *
     * @param StreamingDocumentInterface|TextDocumentInterface $document
     *
     * @return list<non-empty-string>
     */
    public function tokenize(StreamingDocumentInterface|TextDocumentInterface $document): iterable;
}
