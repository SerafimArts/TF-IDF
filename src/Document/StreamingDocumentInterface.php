<?php

declare(strict_types=1);

namespace Serafim\TFIDF\Document;

interface StreamingDocumentInterface extends DocumentInterface
{
    /**
     * @return resource
     */
    public function getContentStream(): mixed;
}
