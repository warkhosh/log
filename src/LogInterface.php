<?php

namespace Warkhosh\Log;

/**
 * Interface LogInterface
 *
 * @package Warkhosh\Log
 */
interface LogInterface
{
    /**
     * @param string $mode
     * @return void
     */
    public function setMode(string $mode);

    /**
     * @param string $path
     * @return void
     */
    public function setPath(string $path);

    /**
     * @param \Throwable|mixed $log
     * @return void
     */
    public function info($log);

    /**
     * @param \Throwable|mixed $log
     * @return void
     */
    public function warning($log);

    /**
     * @param \Throwable|mixed $log
     * @return void
     */
    public function error($log);

    /**
     * @param \Throwable|mixed $log
     * @return void
     */
    public function debug($log);

    /**
     * @param \Throwable|mixed $log
     * @return void
     */
    public function report($log);

    /**
     * Метод определяет параметры для записис в лог.
     *
     * @param \Throwable|array|string $arg
     * @return array
     */
    public function getParams($arg = null);

    /**
     * Save log
     *
     * @param string $content
     * @param string $file
     * @return bool
     */
    public function save(string $content, string $file);
}