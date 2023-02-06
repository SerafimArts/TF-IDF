<?php

declare(strict_types=1);

namespace Serafim\TFIDF;

use Serafim\TFIDF\Document\FileDocument;
use Serafim\TFIDF\Document\StreamingDocument;
use Serafim\TFIDF\Document\StreamingDocumentInterface;
use Serafim\TFIDF\Document\TextDocument;
use Serafim\TFIDF\Document\TextDocumentInterface;
use Serafim\TFIDF\Locale\Repository;
use Serafim\TFIDF\Locale\RepositoryInterface;
use Serafim\TFIDF\Memory\FactoryInterface as MemoryFactoryInterface;
use Serafim\TFIDF\Memory\Factory as MemoryFactory;
use Serafim\TFIDF\StopWords\Factory as StopWordsFactory;
use Serafim\TFIDF\StopWords\FactoryInterface as StopWordsFactoryInterface;
use Serafim\TFIDF\Tokenizer\TokenizerInterface;

final class Vectorizer implements VectorizerInterface
{
    /**
     * @var RepositoryInterface
     */
    private readonly RepositoryInterface $locales;

    /**
     * @var InverseDocumentFrequencyCounterInterface
     */
    private readonly InverseDocumentFrequencyCounterInterface $idf;

    /**
     * @var list<StreamingDocumentInterface|TextDocumentInterface>
     */
    private array $documents = [];

    /**
     * @param MemoryFactoryInterface $memory
     * @param RepositoryInterface|null $locales
     * @param StopWordsFactoryInterface $stopWords
     * @param TokenizerInterface|null $tokenizer
     */
    public function __construct(
        private readonly MemoryFactoryInterface $memory = new MemoryFactory(),
        StopWordsFactoryInterface $stopWords = new StopWordsFactory(),
        TokenizerInterface $tokenizer = null,
        RepositoryInterface $locales = null,
    ) {
        $this->locales = $locales ?? Repository::fromGlobals();

        $this->idf = new InverseDocumentFrequencyCounter(
            tf: new TermFrequencyCounter(
                allocator: $this->memory,
                stopWords: $stopWords,
                tokenizer: $tokenizer,
            ),
            allocator: $this->memory,
        );
    }

    /**
     * @psalm-taint-sink file $file
     *
     * @param non-empty-string|\SplFileInfo $file
     * @param non-empty-string|null $locale
     *
     * @return FileDocument
     */
    public function createFile(string|\SplFileInfo $file, string $locale = null): FileDocument
    {
        if ($file instanceof \SplFileInfo) {
            $file = (string)$file->getRealPath();
        }

        $locale ??= $this->locales->getDefault();

        return new FileDocument($file, $locale);
    }

    /**
     * @psalm-taint-sink file $file
     *
     * @param non-empty-string|\SplFileInfo $file
     * @param non-empty-string|null $locale
     *
     * @return void
     */
    public function addFile(string|\SplFileInfo $file, string $locale = null): void
    {
        $this->add($this->createFile($file, $locale));
    }

    /**
     * @psalm-taint-sink file $pathname
     *
     * @param resource $stream
     * @param non-empty-string|null $locale
     *
     * @return StreamingDocument
     */
    public function createStream(mixed $stream, string $locale = null): StreamingDocument
    {
        assert(\is_resource($stream));

        $locale ??= $this->locales->getDefault();

        return new StreamingDocument($stream, $locale);
    }

    /**
     * @psalm-taint-sink file $pathname
     *
     * @param resource $stream
     * @param non-empty-string|null $locale
     *
     * @return void
     */
    public function addStream(mixed $stream, string $locale = null): void
    {
        $this->add($this->createStream($stream, $locale));
    }

    /**
     * @param string $text
     * @param non-empty-string|null $locale
     *
     * @return TextDocument
     */
    public function createText(string $text, string $locale = null): TextDocument
    {
        $locale ??= $this->locales->getDefault();

        return new TextDocument($text, $locale);
    }

    /**
     * @param string $text
     * @param non-empty-string|null $locale
     *
     * @return void
     */
    public function addText(string $text, string $locale = null): void
    {
        $this->add($this->createText($text, $locale));
    }

    /**
     * {@inheritDoc}
     */
    public function add(StreamingDocumentInterface|TextDocumentInterface $document): void
    {
        $this->documents[] = $document;
    }

    /**
     * {@inheritDoc}
     */
    public function compute(): iterable
    {
        foreach ($this->documents as $document) {
            yield $document => $this->idf->compute($document, $this->documents);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function computeFor(StreamingDocumentInterface|TextDocumentInterface $document): iterable
    {
        return $this->idf->compute($document, $this->documents);
    }
}
