<p align="center">
    <a href="https://packagist.org/packages/serafim/tf-idf"><img src="https://poser.pugx.org/serafim/tf-idf/require/php?style=for-the-badge" alt="PHP 8.1+"></a>
    <a href="https://packagist.org/packages/serafim/tf-idf"><img src="https://poser.pugx.org/serafim/tf-idf/version?style=for-the-badge" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/serafim/tf-idf"><img src="https://poser.pugx.org/serafim/tf-idf/v/unstable?style=for-the-badge" alt="Latest Unstable Version"></a>
    <a href="https://packagist.org/packages/serafim/tf-idf"><img src="https://poser.pugx.org/serafim/tf-idf/downloads?style=for-the-badge" alt="Total Downloads"></a>
    <a href="https://raw.githubusercontent.com/SerafimArts/TF-IDF/master/LICENSE.md"><img src="https://poser.pugx.org/serafim/tf-idf/license?style=for-the-badge" alt="License MIT"></a>
</p>
<p align="center">
    <a href="https://github.com/SerafimArts/TF-IDF/actions"><img src="https://github.com/SerafimArts/TF-IDF/workflows/tests/badge.svg"></a>
</p>

## Introduction

TF-IDF is a method of information retrieval that is used to rank the importance
of words in a document. It is based on the idea that words that appear in a 
document more often are more relevant to the document.

TF-IDF is the product of Term Frequency and Inverse Document Frequency. Here’s 
the formula for TF-IDF calculation.

```
TF-IDF = Term Frequency (TF) * Inverse Document Frequency (IDF)
```

### Term Frequency

the ratio of the number of occurrences of a certain word to the total number of 
words in the document. Thus, the importance of the word $t_{{i}}$ within a 
single document is evaluated

$\mathrm{tf}(t, d) = \frac{n_t}{\sum _kn_k}$

where $n_t$ is the number of occurrences of the word $t$ in the document, and 
the denominator is the total number of words in the document.

### Inverse Document Frequency

The inverse of the frequency with which a certain word occurs in the documents 
of the collection. The founder of this concept is [Karen Spark Jones](https://en.wikipedia.org/wiki/Karen_Sp%C3%A4rck_Jones). 
Accounting for IDF reduces the weight of commonly used words. There is only one 
IDF value for each unique word within a given collection of documents.

$\mathrm{idf}(t, D) = \log \frac {|D|}{| \{\,d_{i}\in D\mid t\in d_{i}\,\} |}$

where

- $|D|$ — The number of documents in the collection;
- ${\displaystyle |\{d_{i}\in D\mid t\in d_{i}\}|}$ — the number of 
  documents in collection $D$ where $t$ occurs (when ${\displaystyle n_{t}\neq 0}$).

The choice of the base of the logarithm in the formula does not matter, since
changing the base changes the weight of each word by a constant factor, which
does not affect the weight ratio.

Thus, the TF-IDF measure is the product of two factors:

$\operatorname{tf-idf}(t, d, D) = \operatorname{tf}(t,d)\times \operatorname{idf}(t,D)$

High weight in TF-IDF will be given to words with high frequency within a
particular document and low frequency in other documents.

## Installation

TF-IDF is available as composer repository and can be 
installed using the following command in a root of your project:

```bash
$ composer require serafim/tf-idf
```

## Quick Start

Getting information about words:

```php
$vectorizer = new \Serafim\TFIDF\Vectorizer();

$vectorizer->addFile(__DIR__ . '/path/to/file-1.txt');
$vectorizer->addFile(__DIR__ . '/path/to/file-2.txt');

foreach ($loader->compute() as $document => $entries) {
    var_dump($document);

    foreach ($entries as $entry) {
        var_dump($entry);
    }
}
```

Example Result:

```
Serafim\TFIDF\Document\FileDocument {
    locale: "ru_RU"
    pathname: "/home/example/how-it-works.md"
}

Serafim\TFIDF\Entry {
    term: "работает"
    occurrences: 4
    df: 1
    tf: 0.012012012012012
    idf: 0.69314718055995
    tfidf: 0.0083260922589783
}

Serafim\TFIDF\Entry {
    term: "php"
    occurrences: 26
    df: 2
    tf: 0.078078078078078
    idf: 0.0
    tfidf: 0.0
}

Serafim\TFIDF\Entry {
    term: "запуска"
    occurrences: 2
    df: 1
    tf: 0.006006006006006
    idf: 0.69314718055995
    tfidf: 0.0041630461294892
}

// ...etc...
```

### Adding Documents

The IDF (Inverse Document Frequency) calculation requires several documents in 
the corpus. To do this, you can use several methods:

```php
$vectorizer = new \Serafim\TFIDF\Vectorizer();

$vectorizer->addFile(__DIR__ . '/path/to/file.txt');
$vectorizer->addFile(new \SplFileInfo(__DIR__ . '/path/to/file.txt'));
$vectorizer->addText('example text');
$vectorizer->addStream(fopen(__DIR__ . '/path/to/file.txt', 'rb'));

// OR

$vectorizer->add(new class implements \Serafim\TFIDF\Document\TextDocumentInterface {
    public function getLocale(): string { /* ... */ }
    public function getContent(): string { /* ... */ }
});
```

### Creating Documents

```php
$vectorizer = new \Serafim\TFIDF\Vectorizer();

$file = $vectorizer->createFile(__DIR__ . '/path/to/file.txt');
$text = $vectorizer->createText('example text');
$stream = $vectorizer->createStream(fopen(__DIR__ . '/path/to/file.txt', 'rb'));
```

### Computing

To calculate TF-IDF between loaded documents, use the "`compute()``" method:

```php
foreach ($vectorizer->compute() as $document => $result) { 
    // $document = object(Serafim\TFIDF\Document\DocumentInterface)
    // $result   = list<object(Serafim\TFIDF\Entry)>
}
```

To calculate the TF-IDF between the loaded documents and the passed one, use 
the "`computeFor()`" method:

```php
$text = $vectorizer->createText('example text');

$result = $vectorizer->computeFor($text);

// $result = list<object(Serafim\TFIDF\Entry)>
```


### Custom Memory Driver

By default, all operations are calculated in memory. This happens pretty
quickly, but it can overflow it. You can write your own driver if you need to
save memory.

```php
use Serafim\TFIDF\Vectorizer;
use Serafim\TFIDF\Memory\FactoryInterface;
use Serafim\TFIDF\Memory\MemoryInterface;

$vectorizer = new Vectorizer(
    memory: new class implements FactoryInterface {
        // Method for creating a memory area for counters
        public function create(): MemoryInterface
        {
            return new class implements MemoryInterface, \IteratorAggregate {
                // Increment counter for the given term.
                public function inc(string $term): void { /* ... */ }

                // Return counter value for the given term or
                // 0 if the counter is not found.
                public function get(string $term): int { /* ... */ }

                // Should return TRUE if there is a counter for the
                // specified term.
                public function has(string $term): bool { /* ... */ }

                // Returns the number of registered counters.
                public function count(): int { /* ... */ }

                // Returns a list of terms and counter values in
                // format: [ WORD => 42 ]
                public function getIterator(): \Traversable { /* ... */ }

                // Destruction of the allocated memory area.
                public function __destruct() { /* ... */ }
            };
        }
    }
);
```

### Custom Stop Words

In the case that it is required that some set of "stop words", which would not
be taken into account in the result, a custom implementation should be specified.

> Please note that by default, the list of stop words from the
> [voku/stop-words](https://github.com/voku/stop-words) package is used.

```php
use Serafim\TFIDF\Vectorizer;
use Serafim\TFIDF\StopWords\FactoryInterface;
use Serafim\TFIDF\StopWords\StopWordsInterface;

$vectorizer = new Vectorizer(
    stopWords: new class implements FactoryInterface {
        public function create(string $locale): StopWordsInterface
        {
            // You can use a different set of stop word drivers depending
            // on the locale ("$locale" argument) of the document.
            return new class implements StopWordsInterface {
                // TRUE should be returned if the word should be ignored.
                // For example prepositions.
                public function match(string $term): bool
                {
                    return \in_array($term, ['and', 'or', /* ... */], true);
                }
            };
        }
    }
);
```

### Custom Locale

```php
use Serafim\TFIDF\Vectorizer;
use Serafim\TFIDF\Locale\IntlRepository;

$vectorizer = new Vectorizer(
    locales: new class extends IntlRepository {
        // Specifying the default locale
        public function getDefault(): string
        {
            return 'en_US';
        }
    }
);
```

### Custom Tokenizer

If for some reason the analysis of words in the text does not suit you, you 
can write your own tokenizer.

```php
use Serafim\TFIDF\Vectorizer;
use Serafim\TFIDF\Tokenizer\TokenizerInterface;
use Serafim\TFIDF\Document\StreamingDocumentInterface;
use Serafim\TFIDF\Document\TextDocumentInterface;

$vectorizer = new Vectorizer(
    tokenizer: new class implements TokenizerInterface {
        // Please note that there can be several types of document:
        //  - Text Document: One that contains text in string representation.
        //  - Streaming Document: One that can be read and may contain a
        //    large amount of data.
        public function tokenize(StreamingDocumentInterface|TextDocumentInterface $document): iterable 
        {
            $content = $document instanceof StreamingDocumentInterface
                ? \stream_get_contents($document->getContentStream())
                : $document->getContent();

            // Please note that the document also contains the locale, based on
            // which the term (word) separation logic can change.
            //
            // i.e. `if ($document->getLocale() === 'ar') { ... }`
            //

            return \preg_split('/[\s,]+/isum', $content);
        }
    }
);
```

