<?php

declare(strict_types=1);

namespace Serafim\TFIDF\Memory;

/**
 * @template-extends \IteratorAggregate<non-empty-lowercase-string, int<1, max>>
 */
final class ArrayMemory implements MemoryInterface, \IteratorAggregate
{
    /**
     * @var array<non-empty-lowercase-string, int<1, max>>
     */
    private array $terms = [];

    /**
     * {@inheritDoc}
     */
    public function inc(string $term): void
    {
        if (isset($this->terms[$term])) {
            ++$this->terms[$term];
        } else {
            $this->terms[$term] = 1;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $term): int
    {
        return $this->terms[$term] ?? 0;
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $term): bool
    {
        return isset($this->terms[$term]);
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->terms);
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return \count($this->terms);
    }
}
