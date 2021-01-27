<?php

namespace ArturDoruch\Tool;

/**
 * @author Artur Doruch <arturdoruch@interia.pl>
 */
class MemoryHelper
{
    /**
     * @param float $limit
     * @param string $unit
     *
     * @throws \RuntimeException when memory limit is reached.
     */
    public static function validateLimit($limit, $unit = 'bytes')
    {
        if (memory_get_peak_usage() > $limit * self::getMultiplier($unit)) {
            throw new \RuntimeException(sprintf('The memory limit %s %s has been reached.', $limit, $unit));
        }
    }

    /**
     * Gets formatted peak of the allocated memory.
     *
     * @param string $unit The size unit. One of: bytes, kB, MB, GB, TB.
     * @param int $precision
     *
     * @return float
     */
    public static function getPeakUsage(string $unit = 'bytes', int $precision = 0): float
    {
        return sprintf('%.'.$precision.'f', memory_get_peak_usage() / self::getMultiplier($unit));
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
