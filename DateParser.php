<?php

namespace ArturDoruch\Tool;

/**
 * @author Artur Doruch <arturdoruch@interia.pl>
 *
 * @deprecated Use the arturdoruch/date-parser package instead.
 */
class DateParser
{
    /**
     * Parses formatted datetime or timestamp.
     *
     * @param string|int $date The formatted datetime or timestamp.
     *
     * @return \DateTime|null DateTime object or null when given date is invalid.
     */
    public static function parse($date)
    {
        if ($date instanceof \DateTime) {
            return $date;
        }

        if (!is_string($date)) {
            // Is timestamp
            if (is_int($date) && $date > 0) {
                return new \DateTime('@'.$date);
            }

            return null;
        }

        $date = self::fixMonth($date);

        try {
            $dateObject = new \DateTime($date);

            if ($dateObject && preg_match('/(\d{4})/', $date, $matches) && $matches[1] == $dateObject->format('Y')) {
                return $dateObject;
            }
        } catch (\Exception $e) {
        }

        $parts = date_parse($date);

        if ($parts['error_count'] === 0) {
            return new \DateTime(sprintf('%d-%d-%d %d:%d:%d', $parts['day'], $parts['month'], $parts['year'], $parts['hour'], $parts['minute'], $parts['second']));
        }

        if (preg_match('/^(?!00)(\d{1,2}).(?!00)(\d{2}).((?:19|20)?\d{2})$/', $date, $match)) {
            return self::createDate($match[1], $match[2], $match[3]);
        }

        if (preg_match('/^(?!0.+)(\d{4}).(?!00)(\d{2}).(?!00)(\d{2})$/', $date, $match)) {
            return self::createDate($match[3], $match[2], $match[1]);
        }

        return null;
    }

    /**
     * @param string $day
     * @param string $month
     * @param string $year
     *
     * @return \DateTime|null
     */
    private static function createDate($day, $month, $year)
    {
        return @checkdate($month, $day, $year) ? new \DateTime($day . '-' . $month . '-' . $year) : null;
    }

    /**
     * @param string $date
     * @return string
     */
    private static function fixMonth($date)
    {
        static $monthsMap = [
            'maa' => 'mar',
            'mei' => 'may',
            // Norway
            'des' => 'dec',
            // German
            'mär' => 'mar',
            'mai' => 'may',
            'okt' => 'oct',
            'dez' => 'dec',
            // Polish
            'sty' => 'jan',
            'lut' => 'feb',
            'kwi' => 'apr',
            'maj' => 'may',
            'cze' => 'jun',
            'lip' => 'jul',
            'sie' => 'aug',
            'wrz' => 'sep',
            'paź' => 'oct',
            'lis' => 'nov',
            'gru' => 'dec'
        ];

        $date = str_replace([',', '  '], ' ', mb_strtolower($date));

        return preg_replace_callback('/([\p{L}]{3})[\p{L}]*/u', function ($matches) use ($monthsMap) {
            $month = $matches[1];

            return isset($monthsMap[$month]) ? $monthsMap[$month] : $month;
        }, $date);
    }
}
 