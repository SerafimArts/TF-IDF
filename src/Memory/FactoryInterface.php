<?php

declare(strict_types=1);

namespace Serafim\TFIDF\Memory;

interface FactoryInterface
{
    /**
     * @return MemoryInterface
     */
    public function create(): MemoryInterface;
}
