<?php

namespace ArturDoruch\Tool;

/**
 * @author Artur Doruch <arturdoruch@interia.pl>
 */
class MemoryHelper
{
    /**
     * Validates limit of memory peak usage.
     *
     * @param float $limit
     * @param string $unit The limit size unit. One of: bytes, kB, MB, GB, TB.
     *
     * @throws \RuntimeException when memory limit is reached.
     */
    public static function validateLimit(float $limit, string $unit = 'bytes')
    {
        if (($usage = memory_get_peak_usage()) > $limit * self::getMultiplier($unit)) {
            throw new \RuntimeException(sprintf(
                'The memory usage limit of %s %s has been reached with the value %d bytes.',
                $limit, $unit, $usage
            ));
        }
    }

    /**
     * @todo In version 2.0 set parameter $addUnit as default to true.
     *
     * Gets formatted peak of the allocated memory.
     *
     * @param string $unit The size unit. One of: bytes, kB, MB, GB, TB.
     * @param int $precision
     * @param bool $addUnit Whether to add size unit to the returned value.
     *
     * @return float|string
     */
    public static function getPeakUsage(string $unit = 'bytes', int $precision = 0, bool $addUnit = false)
    {
        return sprintf('%.'.$precision.'f', memory_get_peak_usage() / self::getMultiplier($unit)) . ($addUnit ? ' '.$unit : '');
    }


    private static function getMultiplier(string $unit): float
    {
        static $unitExponentMap = [
            'bytes' => 0,
            'kB' => 1,
            'MB' => 2,
            'GB' => 3,
            'TB' => 4,
        ];

        if (!isset($unitExponentMap[$unit])) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid size unit "%s". Allowed units are: "%s".',
                $unit, join('", "', array_keys($unitExponentMap))
            ));
        }

        return pow(1024, $unitExponentMap[$unit]);
    }
}
