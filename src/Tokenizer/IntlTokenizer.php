<?php

declare(strict_types=1);

namespace Serafim\TFIDF\Tokenizer;

use Serafim\TFIDF\Document\StreamingDocumentInterface;
use Serafim\TFIDF\Document\TextDocumentInterface;

final class IntlTokenizer extends Tokenizer
{
    /**
     * Expected buffer size
     */
    private const BUFFER_SIZE = 65536;

    /**
     * @param int<1, max>|null $buffer
     */
    public function __construct(
        private readonly ?int $buffer = self::BUFFER_SIZE,
    ) {
        assert($this->buffer === null || $this->buffer >= 1);
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-suppress PossiblyNullArgument
     * @psalm-suppress InvalidReturnType
     */
    public function tokenize(StreamingDocumentInterface|TextDocumentInterface $document): iterable
    {
        $iterator = \IntlRuleBasedBreakIterator::createWordInstance($document->getLocale());

        if ($document instanceof StreamingDocumentInterface) {
            if ($this->buffer === null) {
                return $this->nonBuffered($iterator, $document);
            }

            return $this->buffered($iterator, $document);
        }

        return $this->load($iterator, $document->getContent());
    }

    /**
     * @param \IntlRuleBasedBreakIterator $iterator
     * @param StreamingDocumentInterface $document
     *
     * @return list<non-empty-string>
     *
     * @psalm-suppress InvalidReturnType
     */
    private function buffered(\IntlRuleBasedBreakIterator $iterator, StreamingDocumentInterface $document): iterable
    {
        foreach ($this->chunks($document) as $chunk) {
            yield from $this->load($iterator, $chunk);
        }
    }

    /**
     * @param \IntlRuleBasedBreakIterator $iterator
     * @param StreamingDocumentInterface $document
     *
     * @return list<non-empty-string>
     *
     * @psalm-suppress InvalidReturnType
     */
    private function nonBuffered(\IntlRuleBasedBreakIterator $iterator, StreamingDocumentInterface $document): iterable
    {
        yield from $this->load($iterator, \stream_get_contents(
            $document->getContentStream(),
        ));
    }

    /**
     * @param \IntlRuleBasedBreakIterator $iterator
     * @param string $chunk
     *
     * @return list<non-empty-string>
     *
     * @psalm-suppress InvalidReturnType
     */
    private function load(\IntlRuleBasedBreakIterator $iterator, string $chunk): iterable
    {
        $iterator->setText($chunk);

        /** @var non-empty-string $word */
        foreach ($iterator->getPartsIterator() as $word) {
            if ($iterator->getRuleStatus() === \IntlBreakIterator::WORD_LETTER) {
                yield $word;
            }
        }
    }

    /**
     * @param StreamingDocumentInterface $document
     *
     * @return list<non-empty-string>
     *
     * @psalm-suppress InvalidReturnType
     */
    private function chunks(StreamingDocumentInterface $document): iterable
    {
        $stream = $document->getContentStream();

        while (!\feof($stream)) {
            $buffer = \fread($stream, self::BUFFER_SIZE);
            $buffer .= $this->pad($stream);

            yield $buffer;
        }

        \fclose($stream);
    }

    /**
     * Pad the buffer with data until:
     *
     *  - The stream ends.
     *  - The trailing character is a punctuation.
     *
     * @param resource $stream
     *
     * @return string
     */
    private function pad(mixed $stream): string
    {
        if (\feof($stream)) {
            return '';
        }

        $buffer = '';

        do {
            $char = (string)\fread($stream, 1);

            $buffer .= $char;
        } while ($char !== '' && !\ctype_punct($char) && !\feof($stream));

        return $buffer;
    }
}
