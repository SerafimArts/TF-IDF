<?php

declare(strict_types=1);

namespace Serafim\TFIDF\Document;

final class StreamingDocument extends Document implements StreamingDocumentInterface
{
    /**
     * @param resource $stream
     * @param non-empty-string $locale
     */
    public function __construct(
        public readonly mixed $stream,
        string $locale
    ) {
        assert(\is_resource($this->stream));

        parent::__construct($locale);
    }

    /**
     * {@inheritDoc}
     */
    public function getContentStream(): mixed
    {
        \rewind($this->stream);

        return $this->stream;
    }
}
