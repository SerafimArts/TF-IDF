<?php

declare(strict_types=1);

namespace Serafim\TFIDF\Document;

final class TextDocument extends Document implements TextDocumentInterface
{
    /**
     * @param string $contents
     * @param non-empty-string $locale
     */
    public function __construct(
        public readonly string $contents,
        string $locale
    ) {
        parent::__construct($locale);
    }

    /**
     * {@inheritDoc}
     */
    public function getContent(): string
    {
        return $this->contents;
    }
}
