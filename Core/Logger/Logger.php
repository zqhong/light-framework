<?php
/**
 * Created by PhpStorm.
 * User: zqhong
 * Date: 2016/2/25
 * Time: 10:08
 */

namespace Core\Logger;

use Core\Logger\LoggerInterface;

class Logger implements LoggerInterface
{
    /**
     * Detailed debug information
     */
    const DEBUG = 100;

    /**
     * Interesting events
     *
     * Examples: User logs in, SQL logs.
     */
    const INFO = 200;

    /**
     * Uncommon events
     */
    const NOTICE = 250;

    /**
     * Exceptional occurrences that are not errors
     *
     * Examples: Use of deprecated APIs, poor use of an API,
     * undesirable things that are not necessarily wrong.
     */
    const WARNING = 300;

    /**
     * Runtime errors
     */
    const ERROR = 400;

    /**
     * Critical conditions
     *
     * Example: Application component unavailable, unexpected exception.
     */
    const CRITICAL = 500;

    /**
     * Action must be taken immediately
     *
     * Example: Entire website down, database unavailable, etc.
     * This should trigger the SMS alerts and wake you up.
     */
    const ALERT = 550;

    /**
     * Urgent alert.
     */
    const EMERGENCY = 600;

    /**
     * Logging levels from syslog protocol defined in RFC 5424
     *
     * @var array $levels Logging levels
     */
    protected static $levels = [
        self::DEBUG     => 'DEBUG',
        self::INFO      => 'INFO',
        self::NOTICE    => 'NOTICE',
        self::WARNING   => 'WARNING',
        self::ERROR     => 'ERROR',
        self::CRITICAL  => 'CRITICAL',
        self::ALERT     => 'ALERT',
        self::EMERGENCY => 'EMERGENCY',
    ];

    /**
     * The handler stack
     *
     * @var HandlerInterface[]
     */
    protected $handlers = array();

    /**
     * 日志类等级。备注：日志类只输出大于或等于该日志等级的log
     *
     * @var int
     */
    protected $level;

    /**
     * 日志格式
     *
     * @var string
     */
    protected $format = "[:LEVEL] [:DATETIME] :MESSAGE";

    /** 日志时间格式
     *
     * @var string
     */
    protected $datetime_format = "Y-m-d H:i:s";

    /**
     * 单例实例
     *
     * @var Logger
     */
    private static $_instace = NULL;

    /**
     * @param $level int
     */
    protected function __construct($level)
    {
        $this->level  = (int)$level;
    }

    /**
     * 获取单例句柄。默认 level 为 DEBUG
     *
     * @return Logger
     */
    public static function getInstance($level = self::DEBUG)
    {
        if (NULL == self::$_instace) {
            self::$_instace = new Logger($level);
        }

        return self::$_instace;
    }

    /**
     * @param $level int
     * @param $message string
     * @param array $context array
     */
    public function addRecord($level, $message, $context = array())
    {
        if ($level < $this->level) {
            return false;
        }

        $level = self::$levels[$level];
        $datetime = $this->getDateTime();
        $record = $this->format;

        $map = array(
            ":LEVEL"     => $level,
            ":DATETIME" => $datetime,
            ":MESSAGE"  => (string)$message,
        );
        foreach ($map as $k => $v) {
            $record = str_replace($k, $v, $record);
        }
        $record .= "\n";

        foreach ($this->handlers as $handler) {
            if ($handler instanceof LoggerHandlerInterface) {
                $handler->handle($record);
            }
        }
    }

    /**
     * 获取当前时间
     *
     * @return bool|string
     */
    public function getDateTime()
    {
        return date($this->datetime_format);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function emergency($message, array $context = array())
    {
        return $this->addRecord(self::EMERGENCY, $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function alert($message, array $context = array())
    {
        return $this->addRecord(self::ALERT, $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function critical($message, array $context = array())
    {
        return $this->addRecord(self::CRITICAL, $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function error($message, array $context = array())
    {
        return $this->addRecord(self::ERROR, $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function warning($message, array $context = array())
    {
        return $this->addRecord(self::WARNING, $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function notice($message, array $context = array())
    {
        return $this->addRecord(self::NOTICE, $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function info($message, array $context = array())
    {
        return $this->addRecord(self::INFO, $message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function debug($message, array $context = array())
    {
        return $this->addRecord(self::DEBUG, $message, $context);
    }

    /**
     * @param mixed $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = array())
    {
        return $this->addRecord(self::LOG, $message, $context);
    }

    /**
     * 设置时间输出格式
     *
     * @param $datetime_format
     */
    public function setDatetimeFormat($datetime_format)
    {
        $this->datetime_format = (string)$datetime_format;
    }

    /**
     * 设置日志输出格式
     *
     * @param $format
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }

    /**
     * 设置日志等级
     *
     * @param $level int
     */
    public function setLevel($level)
    {
        $this->level = (int)$level;
    }

    /**
     * @param $handle LoggerHandlerInterface
     */
    public function addHandler($handle)
    {
        $this->handlers[] = $handle;
    }
}