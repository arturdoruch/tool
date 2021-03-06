<?php

namespace ArturDoruch\Tool\ExceptionFormatter;

/**
 * @author Artur Doruch <arturdoruch@interia.pl>
 *
 * @internal
 */
class ErrorException extends \ErrorException
{
    public function __construct(\Throwable $e)
    {
        if ($e instanceof \ParseError) {
            $message = 'Parse error: '.$e->getMessage();
            $severity = E_PARSE;
        } elseif ($e instanceof \TypeError) {
            $message = 'Type error: '.$e->getMessage();
            $severity = E_RECOVERABLE_ERROR;
        } else {
            $message = $e->getMessage();
            $severity = E_ERROR;
        }

        parent::__construct($message, $e->getCode(), $severity, $e->getFile(), $e->getLine(), $e->getPrevious());
        $this->setTrace($e->getTrace());
    }


    protected function setTrace(array $trace)
    {
        $traceReflector = new \ReflectionProperty('Exception', 'trace');
        $traceReflector->setAccessible(true);
        $traceReflector->setValue($this, $trace);
    }
}
