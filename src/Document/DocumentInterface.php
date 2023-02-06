<?php

declare(strict_types=1);

namespace Serafim\TFIDF\Document;

interface DocumentInterface
{
    /**
     * @return non-empty-string
     */
    public function getLocale(): string;
}
