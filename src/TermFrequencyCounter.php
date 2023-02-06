<?php

declare(strict_types=1);

namespace Serafim\TFIDF;

use Serafim\TFIDF\Document\StreamingDocumentInterface;
use Serafim\TFIDF\Document\TextDocumentInterface;
use Serafim\TFIDF\Memory\FactoryInterface as MemoryFactoryInterface;
use Serafim\TFIDF\Memory\Factory as MemoryFactory;
use Serafim\TFIDF\Memory\MemoryInterface;
use Serafim\TFIDF\StopWords\Factory as StopWordsFactory;
use Serafim\TFIDF\StopWords\FactoryInterface as StopWordsFactoryInterface;
use Serafim\TFIDF\StopWords\StopWordsInterface;
use Serafim\TFIDF\Tokenizer\IntlTokenizer;
use Serafim\TFIDF\Tokenizer\TokenizerInterface;
use voku\helper\UTF8;

final class TermFrequencyCounter implements TermFrequencyCounterInterface
{
    /**
     * @var TokenizerInterface
     */
    private readonly TokenizerInterface $tokenizer;

    /**
     * @param MemoryFactoryInterface $allocator
     * @param StopWordsFactoryInterface $stopWords
     * @param TokenizerInterface|null $tokenizer
     */
    public function __construct(
        private readonly MemoryFactoryInterface $allocator = new MemoryFactory(),
        private readonly StopWordsFactoryInterface $stopWords = new StopWordsFactory(),
        TokenizerInterface $tokenizer = null,
    ) {
        $this->tokenizer = $tokenizer ?? new IntlTokenizer();
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-suppress ArgumentTypeCoercion
     */
    public function compute(StreamingDocumentInterface|TextDocumentInterface $document): MemoryInterface
    {
        $filter = $this->stopWords->create($document->getLocale());
        $memory = $this->allocator->create();

        foreach ($this->tokenizer->tokenize($document) as $term) {
            $normalized = $this->normalize($term);

            if ($this->filter($normalized, $filter)) {
                continue;
            }

            $memory->inc($normalized);
        }

        return $memory;
    }

    /**
     * @param lowercase-string $normalized
     * @param StopWordsInterface $filter
     *
     * @return bool
     * @psalm-suppress ArgumentTypeCoercion
     */
    private function filter(string $normalized, StopWordsInterface $filter): bool
    {
        return $normalized === ''
            || \mb_strlen($normalized) < 2
            || $filter->match($normalized);
    }

    /**
     * @param non-empty-string $term
     *
     * @return lowercase-string
     * @psalm-suppress LessSpecificReturnStatement
     */
    private function normalize(string $term): string
    {
        return UTF8::filter(UTF8::strtolower($term));
    }
}
