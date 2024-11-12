<?php

namespace Warkhosh\Log;

use Exception;
use Throwable;

/**
 * Class AppLog
 *
 * @package Warkhosh\Log
 */
abstract class AppLog implements LogInterface
{
    /**
     * @var int|null
     */
    public const MAX_SIZE_IN_BYTES = null;

    /**
     * Mode
     */
    public const PROD = 'prod';
    public const STAGE = 'stage';
    public const DEV = 'dev';

    /**
     * @var array
     */
    public static array $modeList = [self::PROD, self::STAGE, self::DEV];

    /**
     * @var string
     */
    protected string $mode = self::PROD;

    /**
     * @var string
     */
    protected string $path;

    /**
     * AppLog constructor
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @param string $mode
     * @return void
     */
    public function setMode(string $mode): void
    {
        if (in_array($mode, static::$modeList)) {
            $this->mode = $mode;
        }
    }

    /**
     * @param string $path
     * @return void
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @param mixed|Throwable $log
     * @return void
     */
    public function info(mixed $log): void
    {
        try {
            $content = var_export(static::getParams($log), true);
            $this->save(date('[Y-m-d H:i:s]')." {$content}", "info_".date('Y_m_d').".log");

        } catch (Throwable $e) {
            $this->handlingSaveLog($e);
        }
    }

    /**
     * @param mixed|Throwable $log
     * @return void
     */
    public function warning(mixed $log): void
    {
        try {
            $content = var_export(static::getParams($log), true);
            $this->save(date('[Y-m-d H:i:s]')." {$content}", "warning_".date('Y_m_d').".log");

        } catch (Throwable $e) {
            $this->handlingSaveLog($e);
        }
    }

    /**
     * @param mixed|Throwable $log
     * @return void
     */
    public function error(mixed $log): void
    {
        try {
            $content = var_export(static::getParams($log), true);
            $this->save(date('[Y-m-d H:i:s]')." {$content}", "error_".date('Y_m_d').".log");

        } catch (Throwable $e) {
            $this->handlingSaveLog($e);
        }
    }

    /**
     * @param mixed|Throwable $log
     * @return void
     */
    public function debug(mixed $log): void
    {
        try {
            $content = var_export(static::getParams($log), true);
            $this->save(date('[Y-m-d H:i:s]')." {$content}", "debug_".date('Y_m_d').".log");

        } catch (Throwable $e) {
            $this->handlingSaveLog($e);
        }
    }

    /**
     * @param mixed|Throwable $log
     * @return void
     */
    public function report(mixed $log): void
    {
        try {
            $content = var_export(static::getParams($log), true);
            $this->save(date('[Y-m-d H:i:s]')." {$content}", "report_".date('Y_m_d').".log");

        } catch (Throwable $e) {
            $this->handlingSaveLog($e);
        }
    }

    /**
     * @param string $url
     * @param string|null $userAgent
     * @param string|null $clientIp
     * @return void
     */
    public function notFound(string $url, ?string $userAgent = null, ?string $clientIp = null): void
    {
        try {
            ob_start();
            $df = fopen("php://output", 'w');

            $array = ["time" => date('[H:i:s]'), "url" => $url];

            if (! empty($userAgent)) {
                $array["user_agent"] = $userAgent;
            }

            if (! empty($clientIp)) {
                $array["client_ip"] = $clientIp;
            }

            fputcsv($df, $array, ",");
            fclose($df);
            $content = ob_get_clean();

            $this->save($content, "notFound_".date('Y_m_d').".log", "");

        } catch (Throwable $e) {
            $this->handlingSaveLog($e);
        }
    }

    /**
     * Метод определяет параметры для записи в лог.
     *
     * @param array|float|int|string|Throwable|null $arg
     * @return array
     */
    public static function getParams(mixed $arg = null): array
    {
        if ($arg instanceof Throwable) {
            return [
                "log" => $arg->getMessage(),
                "code" => $arg->getCode(),
                "file" => $arg->getFile().($arg->getLine() > 0 ? "(".$arg->getLine().")" : ''),
                "line" => $arg->getLine(),
                "trace" => $arg->getTraceAsString(),
            ];
        }

        if (is_array($arg)) {
            $arg = ['log' => $arg];
        } else {
            $arg = ['log' => func_get_args()];
        }

        if (isset($arg['log']['trace']) && is_array($arg['log']['trace'])) {
            $arg['trace'] = $arg['log']['trace'];
            unset($arg['log']['trace']);

        } else {
            $trace = debug_backtrace();

            if (isset($trace[3]) && $trace[3]['function'] === 'call_user_func_array' && isset($trace[4])) {
                $trace = array_slice($trace, 5, 30);
                $trace = static::getItemsOnly(["file", "line"], $trace);
                $arg['trace'] = '';

                foreach ($trace as $row) {
                    if (is_array($row) && count($row) === 2) {
                        $arg['trace'] .= "\n{$row['file']}({$row['line']})";
                    }
                }

            } elseif (isset($trace[3])) {
                $trace = array_slice($trace, 3, 30);
                $trace = static::getItemsOnly(["file", "line"], $trace);
                $arg['trace'] = '';

                foreach ($trace as $row) {
                    if (is_array($row) && count($row) === 2) {
                        $arg['trace'] .= "\n{$row['file']}({$row['line']})";
                    }
                }
            }
        }

        return $arg;
    }

    /**
     * Save log
     *
     * @param string $content
     * @param string $file
     * @param string $lineCompletion
     * @return bool
     */
    public function save(string $content, string $file, string $lineCompletion = "\n\n"): bool
    {
        try {
            $file = "{$this->path}/{$file}";
            $fileExist = file_exists($file);
            $content .= $lineCompletion;

            if (! is_dir($this->path)) {
                return mkdir($this->path, 0755, true);
            }

            // Log file exceeds 50 megabytes
            if (static::MAX_SIZE_IN_BYTES > 0 && $fileExist && filesize($file) > static::MAX_SIZE_IN_BYTES) {
                $info = pathinfo($file);
                $oldFile = "{$this->path}/{$info['filename']}-second-part.{$info['extension']}";

                if (! file_exists($oldFile)) {
                    unlink($oldFile);
                }

                rename($file, $oldFile);
                chmod($oldFile, 0755);
            }

            if ($fileExist === false) {
                if (file_put_contents($file, $content, LOCK_EX) === false) {
                    throw new Exception("I can not create a file {$file}");
                }

            } elseif (file_put_contents($file, $content, FILE_APPEND | LOCK_EX) === false) {
                throw new Exception("I can not create a file: {$file}");
            }

            chmod($file, 0755);

            return true;

        } catch (Throwable $e) {
            if ($this->mode !== static::PROD) {
                echo $e->getMessage()."\n";
                echo $content;
            }
        }

        return false;
    }

    /**
     * Leave the specified subset of items in lists
     *
     * @param array|string $haystack
     * @param array $array
     * @return array
     */
    public static function getItemsOnly(array|string $haystack, array $array): array
    {
        if (count($array)) {
            $haystack = is_array($haystack) ? $haystack : (array)$haystack;

            foreach ($array as $key => $values) {
                $array[$key] = array_intersect_key($values, array_flip($haystack));
            }
        }

        return $array;
    }

    /**
     * Error handler for logging
     *
     * @note reassign this method in your child class for more fine-tuning.
     *
     * @param Throwable $error
     * @return void
     */
    protected function handlingSaveLog(Throwable $error): void
    {
        if ($this->mode !== static::PROD) {
            var_dump($error->getMessage());
        }
    }
}
