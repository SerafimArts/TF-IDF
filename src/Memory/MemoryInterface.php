<?php

declare(strict_types=1);

namespace Serafim\TFIDF\Memory;

/**
 * @template-extends \Traversable<non-empty-lowercase-string, int<1, max>>
 */
interface MemoryInterface extends \Traversable, \Countable
{
    /**
     * @param non-empty-lowercase-string $term
     *
     * @return void
     */
    public function inc(string $term): void;

    /**
     * @param non-empty-lowercase-string $term
     *
     * @return int<0, max>
     */
    public function get(string $term): int;

    /**
     * @param non-empty-lowercase-string $term
     *
     * @return bool
     */
    public function has(string $term): bool;
}
