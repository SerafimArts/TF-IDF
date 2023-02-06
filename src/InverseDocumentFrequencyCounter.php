<?php

declare(strict_types=1);

namespace Serafim\TFIDF;

use Serafim\TFIDF\Document\StreamingDocumentInterface;
use Serafim\TFIDF\Document\TextDocumentInterface;
use Serafim\TFIDF\Memory\FactoryInterface;
use Serafim\TFIDF\Memory\Factory;
use Serafim\TFIDF\Memory\MemoryInterface;

final class InverseDocumentFrequencyCounter implements InverseDocumentFrequencyCounterInterface
{
    /**
     * @var \WeakMap<StreamingDocumentInterface|TextDocumentInterface, MemoryInterface>
     */
    private readonly \WeakMap $counters;

    /**
     * @param FactoryInterface $allocator
     * @param TermFrequencyCounterInterface $tf
     */
    public function __construct(
        private readonly TermFrequencyCounterInterface $tf = new TermFrequencyCounter(),
        private readonly FactoryInterface $allocator = new Factory(),
    ) {
        $this->counters = new \WeakMap();
    }

    /**
     * @template TValue of mixed
     *
     * @param iterable<array-key, TValue> $haystack
     *
     * @return array<array-key, TValue>
     */
    private function toArray(iterable $haystack): array
    {
        if ($haystack instanceof \Traversable) {
            return \iterator_to_array($haystack, false);
        }

        return $haystack;
    }

    /**
     * {@inheritDoc}
     */
    public function compute(StreamingDocumentInterface|TextDocumentInterface $document, iterable $haystack): iterable
    {
        $haystack = $this->toArray($haystack);
        $size = \count($haystack);

        if (!\in_array($document, $haystack, true)) {
            ++$size;
        }

        $memory = $this->memory($document);
        foreach ($memory as $term => $occurrences) {
            $inverse = $this->getInverseDocumentFrequencyMemory($document, $haystack);

            // Term Frequency
            $tf = $occurrences / $memory->count();
            // Document Frequency
            $df = $inverse->get($term) + 1;
            // Inverse Document Frequency
            $idf = \log($size / $df);

            yield new Entry(
                term: $term,
                occurrences: $occurrences,
                df: $df,
                tf: $tf,
                idf: $idf,
                tfidf: $tf * $idf,
            );
        }
    }

    /**
     * @param StreamingDocumentInterface|TextDocumentInterface $document
     * @param array<StreamingDocumentInterface|TextDocumentInterface> $haystack
     *
     * @return MemoryInterface
     */
    private function getInverseDocumentFrequencyMemory(
        StreamingDocumentInterface|TextDocumentInterface $document,
        array $haystack,
    ): MemoryInterface {
        $memory = $this->memory($document);
        $result = $this->allocator->create();

        foreach ($haystack as $entry) {
            // Skip self reference
            if ($entry === $document) {
                continue;
            }

            foreach ($this->memory($entry) as $term => $_) {
                if ($memory->get($term)) {
                    $result->inc($term);
                }
            }
        }

        return $result;
    }

    /**
     * @param StreamingDocumentInterface|TextDocumentInterface $document
     *
     * @return MemoryInterface
     */
    private function memory(StreamingDocumentInterface|TextDocumentInterface $document): MemoryInterface
    {
        return $this->counters[$document] ??= $this->tf->compute($document);
    }
}
