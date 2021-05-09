<?php

namespace ArturDoruch\Tool\ExceptionFormatter;

/**
 * @author Artur Doruch <arturdoruch@interia.pl>
 *
 * @deprecated Use the "arturdoruch/exception-formatter" component instead.
 */
class ExceptionFormatter
{
    /**
     * @var string The application directory path used to shorting the exception trace filename.
     */
    private $appDir = '/';

    /**
     * @var bool
     */
    private $shortenFilename;

    /**
     * @var array
     */
    private $traceHtmlTemplates = [
        'list' => '<ol class="exception-trace">%s</ol>',
        'listItem' => '<li>%s</li>',
        'function' => '%s%s<b>%s</b><span class="text-muted">(%s)</span>',
        'class' => '<abbr title="%s">%s</abbr>',
        'path' => ' in <span class="text-danger">%s<b>%s</b> (line %d)</span>',
    ];

    /**
     * @param string $appDir The application directory path used to shorting the exception trace filename.
     * @param array $traceHtmlTemplates
     *  - list (string)
     *  - listItem (string)
     *  - function (string)
     *  - class (string)
     *  - path (string)
     */
    public function __construct(string $appDir = '', array $traceHtmlTemplates = [])
    {
        if ($appDir) {
            if (!$path = realpath($appDir)) {
                throw new \InvalidArgumentException(sprintf('The application directory path "%s" does not exist.', $appDir));
            }

            $this->appDir = rtrim($path, '/') . '/';
        }

        $this->traceHtmlTemplates = array_merge($this->traceHtmlTemplates, $traceHtmlTemplates);
    }

    /**
     * Formats exception properties.
     *
     * @param \Throwable $e
     * @param bool $shortenFilename Whether the filename should be shorten.
     *
     * @return array
     */
    public function format(\Throwable $e, bool $shortenFilename = true): array
    {
        $this->shortenFilename = $shortenFilename;

        return [
            'message' => $e->getMessage(),
            'class' => $class = get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $this->getTraceAsHtml($e),
            'formattedClass' => $this->formatClass($class),
            'path' => $this->formatPath($e->getFile(), $e->getLine()),
        ];
    }

    /**
     * Gets exception trace formatted to HTML.
     *
     * @param \Throwable $e
     * @param bool $shortenFilename Whether the filename should be shorten.
     *
     * @return string
     */
    public function getTraceAsHtml(\Throwable $e, bool $shortenFilename = true): string
    {
        $this->shortenFilename = $shortenFilename;

        if ($e instanceof \Error) {
            $e = new ErrorException($e);
        }

        $exceptionStack = FlattenException::create($e)->toArray();

        if (!$exception = array_shift($exceptionStack)) {
            return null;
        }

        $items = '';

        foreach ($exception['trace'] as $trace) {
            $item = '';

            if ($trace['function']) {
                $item .= sprintf(
                    $this->traceHtmlTemplates['function'],
                    $this->formatClass($trace['class']),
                    $trace['type'],
                    $trace['function'],
                    $this->formatArgs($trace['args'])
                );
            }

            if (isset($trace['file']) && isset($trace['line'])) {
                $item .= $this->formatPath($trace['file'], $trace['line']);
            }

            $items .= sprintf($this->traceHtmlTemplates['listItem'], $item);
        }

        return sprintf($this->traceHtmlTemplates['list'], $items);
    }

    /**
     * Removes application root directory from the filename.
     *
     * @param string $filename
     *
     * @return string
     */
    public function shortenFilename(string $filename)
    {
        return preg_replace('~^' . $this->appDir . '~', '', $filename);
    }

    /**
     * Formats an array as a string.
     *
     * @param array $args The argument array
     *
     * @return string
     */
    private function formatArgs(array $args)
    {
        $result = [];

        foreach ($args as $key => $item) {
            if ('object' === $item[0]) {
                $formattedValue = sprintf('<em>object</em>(%s)', $this->formatClass($item[1]));
            } elseif ('array' === $item[0]) {
                $formattedValue = sprintf('<em>array</em>(%s)', is_array($item[1]) ? $this->formatArgs($item[1]) : $item[1]);
            } elseif ('null' === $item[0]) {
                $formattedValue = '<em>null</em>';
            } elseif ('boolean' === $item[0]) {
                $formattedValue = '<em>'.strtolower(var_export($item[1], true)).'</em>';
            } elseif ('resource' === $item[0]) {
                $formattedValue = '<em>resource</em>';
            } else {
                $formattedValue = str_replace("\n", '', $this->escapeHtml(var_export($item[1], true)));
            }

            $result[] = is_int($key) ? $formattedValue : sprintf("'%s' => %s", $key, $formattedValue);
        }

        return implode(', ', $result);
    }

    /**
     * @param string $file
     * @param int $line
     *
     * @return string
     */
    private function formatPath($file, $line)
    {
        if ($this->shortenFilename) {
            $file = $this->shortenFilename($file);
        }

        $lastDirSeparatorPosition = strrpos($file, DIRECTORY_SEPARATOR) + 1;
        $path = substr($file, 0, $lastDirSeparatorPosition);
        $filename = substr($file, $lastDirSeparatorPosition);

        return sprintf($this->traceHtmlTemplates['path'], $path, $filename, $line);
    }

    /**
     * @param string $class
     *
     * @return string
     */
    private function formatClass(string $class)
    {
        $parts = explode('\\', $class);

        return sprintf($this->traceHtmlTemplates['class'], $class, array_pop($parts));
    }

    /**
     * HTML-encodes a string.
     *
     * @param $string
     * @return string
     */
    private function escapeHtml($string)
    {
        return htmlspecialchars($string, ENT_COMPAT | ENT_SUBSTITUTE, 'UTF-8');
    }
}
