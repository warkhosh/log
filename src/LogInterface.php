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
     * @param string      $url
     * @param string|null $userAgent
     * @param string|null $clientIp
     * @return void
     */
    public function notFound(string $url, ?string $userAgent = null, ?string $clientIp = null);

    /**
     * Метод определяет параметры для записи в лог.
     *
     * @param \Throwable|array|string $arg
     * @return array
     */
    static public function getParams($arg = null);

    /**
     * Save log
     *
     * @param string $content
     * @param string $file
     * @param string $lineCompletion
     * @return bool
     */
    public function save(string $content, string $file, string $lineCompletion = "\n\n");
}