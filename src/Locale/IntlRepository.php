<?php

declare(strict_types=1);

namespace Serafim\TFIDF\Locale;

class IntlRepository extends Repository
{
    /**
     * @var non-empty-string
     */
    private readonly string $default;

    /**
     * @var list<non-empty-string>
     */
    private readonly array $locales;

    /**
     * @var \WeakReference<IntlRepository>|null
     */
    private static ?\WeakReference $instance = null;

    /**
     * Please use {@see IntlRepository::new()} instead.
     *
     * @psalm-suppress PropertyTypeCoercion
     */
    private function __construct()
    {
        $this->default = \Locale::getDefault();
        $this->locales = \ResourceBundle::getLocales('') ?: [];
    }

    /**
     * @return self
     */
    public static function new(): self
    {
        if ($instance = self::$instance?->get()) {
            return $instance;
        }

        self::$instance = \WeakReference::create(
            $instance = new self(),
        );

        return $instance;
    }

    /**
     * {@inheritDoc}
     */
    public function getDefault(): string
    {
        return $this->default;
    }

    /**
     * {@inheritDoc}
     */
    public function getAll(): iterable
    {
        return $this->locales;
    }

    /**
     * {@inheritDoc}
     */
    public function isValid(string $name): bool
    {
        return \in_array($name, $this->locales, true);
    }
}
