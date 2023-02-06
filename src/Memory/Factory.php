<?php

declare(strict_types=1);

namespace Serafim\TFIDF\Memory;

final class Factory implements FactoryInterface
{
    public function create(): MemoryInterface
    {
        return new ArrayMemory();
    }
}
