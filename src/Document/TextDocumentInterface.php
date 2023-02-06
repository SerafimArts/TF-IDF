<?php

declare(strict_types=1);

namespace Serafim\TFIDF\Document;

interface TextDocumentInterface extends DocumentInterface
{
    /**
     * @return string
     */
    public function getContent(): string;
}
