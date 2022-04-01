<?php

namespace App\Services\Excel\Imports;

use InvalidArgumentException;

class HeadingRowFormatter
{
    /**
     * @const string
     */
    const FORMATTER_NONE = 'none';

    /**
     * @const string
     */
    const FORMATTER_SLUG = 'slug';

    /**
     * @var string
     */
    protected static $formatter;

    /**
     * @var callable[]
     */
    protected static $customFormatters = [];

    /**
     * @var array
     */
    protected static $defaultFormatters = [
        self::FORMATTER_NONE,
        self::FORMATTER_SLUG,
    ];

    /**
     * @param array $headings
     *
     * @return array
     */
    public static function format(array $headings): array
    {
        return array_map(function ($value, $key) {
            return static::callFormatter($value, $key);
        }, $headings, array_keys($headings));
    }

    /**
     * @param string|null $name
     */
    public static function default(string $name = null)
    {
        if (null !== $name && !isset(static::$customFormatters[$name]) && !in_array($name, static::$defaultFormatters, true)) {
            throw new InvalidArgumentException(sprintf('Formatter "%s" does not exist', $name));
        }

        static::$formatter = $name;
    }

    /**
     * @param string $name
     * @param callable $formatter
     */
    public static function extend(string $name, callable $formatter)
    {
        static::$customFormatters[$name] = $formatter;
    }

    /**
     * Reset the formatter.
     */
    public static function reset()
    {
        static::default();
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected static function callFormatter($value, $key = null)
    {
        static::$formatter = static::$formatter ?? config('excel.imports.heading_row.formatter', self::FORMATTER_SLUG);

        // Call custom formatter
        if (isset(static::$customFormatters[static::$formatter])) {
            $formatter = static::$customFormatters[static::$formatter];

            return $formatter($value, $key);
        }

        if (static::$formatter === self::FORMATTER_SLUG) {
            return static::slug($value, '_');
        }

        // No formatter (FORMATTER_NONE)
        return $value;
    }

    /**
     * Generate a URL friendly "slug" from a given string.
     *
     * @param string $title
     * @param string $separator
     * @param string|null $language
     * @return string
     */
    public static function slug($title, $separator = '-', $language = 'en')
    {
        // $title = $language ? static::ascii($title, $language) : $title;

        // Convert all dashes/underscores into separator
        $flip = $separator === '-' ? '_' : '-';

        $title = preg_replace('![' . preg_quote($flip) . ']+!u', $separator, $title);

        // Replace @ with the word 'at'
        $title = str_replace('@', $separator . 'at' . $separator, $title);

        // Remove all characters that are not the separator, letters, numbers, or whitespace.
        $title = preg_replace('![^' . preg_quote($separator) . '\pL\pN\s]+!u', '', mb_strtolower($title, 'UTF-8'));

        // Replace all separator characters and whitespace by a single separator
        $title = preg_replace('![' . preg_quote($separator) . '\s]+!u', $separator, $title);

        return trim($title, $separator);
    }
}
