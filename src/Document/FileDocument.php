<?php

declare(strict_types=1);

namespace Serafim\TFIDF\Document;

final class FileDocument extends Document implements StreamingDocumentInterface
{
    /**
     * @psalm-taint-sink file $pathname
     * @param non-empty-string $pathname
     * @param non-empty-string $locale
     */
    public function __construct(
        public readonly string $pathname,
        string $locale
    ) {
        if (!\is_readable($this->pathname)) {
            throw new \LogicException(\sprintf('File "%s" not readable', $this->pathname));
        }

        parent::__construct($locale);
    }

    /**
     * {@inheritDoc}
     */
    public function getContentStream(): mixed
    {
        return \fopen($this->pathname, 'rb')
            ?: throw new \LogicException(\sprintf('Could not open "%s" for reading', $this->pathname));
    }
}
