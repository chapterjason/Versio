<?php
/**
 * This file is part of the Versio package.
 *
 * (c) Jason Schilling <jason.schilling@sourecode.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * File that was distributed with this source code.
 */

namespace Versio;

class Utils
{

    public static function strictParseInt($value)
    {
        if (is_numeric($value) && ctype_digit($value)) {
            return intval($value, 10);
        }

        return NAN;
    }

    /**
     * @param string[] $prerelease
     * @return string[][]
     */
    public static function convertToExtra(array $prerelease): array
    {
        $built = implode('.', $prerelease);

        return array_map(
            static function (string $value) {
                return self::lengthFilter(explode('.', $value));
            },
            self::lengthFilter(explode('-', $built))
        );
    }

    /**
     * @param array $items
     * @return array
     */
    public static function lengthFilter(array $items): array
    {
        return array_values(array_filter($items, 'strlen'));
    }

    /**
     * @param string[][] $extra
     * @return string[]
     */
    public static function convertToPrerelease(array $extra): array
    {
        $items = array_map(
            static function (array $item) {
                return implode('.', $item);
            },
            $extra
        );

        return explode('.', implode('-', $items));
    }

    /**
     * @param string $branch
     * @return bool
     */
    public static function isReleaseBranch(string $branch): bool
    {
        return strpos($branch, 'release/') === 0;
    }

}