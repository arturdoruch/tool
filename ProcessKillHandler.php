<?php

namespace ArturDoruch\Tool;

/**
 * Adds handler to signals (SIGTERM, SIGINT, SIGHUP, SIGTSTP) killing the process.
 *
 * @author Artur Doruch <arturdoruch@interia.pl>
 */
class ProcessKillHandler
{
    /**
     * @var bool
     */
    private $stop = false;

    /**
     * @param callable|callable[] $listeners The killing process listener or listeners function.
     * @param bool $handleTerminalStopSignal Whether to handle terminal stop signal "SIGTSTP".
     */
    public function __construct($listeners, $handleTerminalStopSignal = false)
    {
        $handler = function ($signalNumber) use ($listeners) {
            foreach ((array) $listeners as $listener) {
                if (is_callable($listener)) {
                    $listener($signalNumber);
                }
            }

            $this->stop = true;
        };

        pcntl_signal(SIGTERM, $handler);
        pcntl_signal(SIGINT, $handler);
        pcntl_signal(SIGHUP, $handler);

        if ($handleTerminalStopSignal === true) {
            pcntl_signal(SIGTSTP, $handler);
        }
    }

    /**
     * Checks if one of killing signal (SIGTERM, SIGINT, SIGHUP, SIGTSTP) has been send.
     *
     * @return bool
     */
    public function isSignalReceived()
    {
        pcntl_signal_dispatch();

        return $this->stop;
    }
}
