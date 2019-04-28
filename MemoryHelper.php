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
     * Gets formatted allocated peak memory.
     *
     * @param string $unit The size unit, one of: bytes, kB, MB, GB, TB, PB.
     * @param int $precision
     *
     * @return float
     */
    public static function getPeakUsage($unit = 'bytes', $precision = 0)
    {
        return round(memory_get_peak_usage() / self::getMultiplier($unit), $precision);
    }

    /**
     * @param string $unit The size unit, one of: bytes, kB, MB, GB, TB, PB.
     *
     * @return number
     */
    private static function getMultiplier($unit)
    {
        $unitExpMap = [
            'bytes' => 0,
            'kB' => 1,
            'MB' => 2,
            'GB' => 3,
            'TB' => 4,
            'PB' => 5,
        ];

        /*if (!isset($unitExpMap[$unit])) {
            throw new \InvalidArgumentException(sprintf('Invalid size unit "%s"', $unit));
        }*/

        return pow(1024, $unitExpMap[$unit]);
    }
}
