<?php

namespace Warkhosh\Log;

use Throwable;

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
    public function setMode(string $mode): void;

    /**
     * @param string $path
     * @return void
     */
    public function setPath(string $path): void;

    /**
     * @param mixed|Throwable $log
     * @return void
     * @throws Throwable
     */
    public function info(mixed $log): void;

    /**
     * @param mixed|Throwable $log
     * @return void
     * @throws Throwable
     */
    public function warning(mixed $log): void;

    /**
     * @param mixed|Throwable $log
     * @return void
     * @throws Throwable
     */
    public function error(mixed $log): void;

    /**
     * @param mixed|Throwable $log
     * @return void
     * @throws Throwable
     */
    public function debug(mixed $log): void;

    /**
     * @param mixed|Throwable $log
     * @return void
     * @throws Throwable
     */
    public function report(mixed $log): void;

    /**
     * @param string $url
     * @param string|null $userAgent
     * @param string|null $clientIp
     * @return void
     */
    public function notFound(string $url, ?string $userAgent = null, ?string $clientIp = null): void;

    /**
     * Метод определяет параметры для записи в лог.
     *
     * @param array|float|int|string|Throwable|null $arg
     * @return array
     */
    public static function getParams(mixed $arg = null): array;

    /**
     * Save log
     *
     * @param string $content
     * @param string $file
     * @param string $lineCompletion
     * @return bool
     */
    public function save(string $content, string $file, string $lineCompletion = "\n\n"): bool;
}
